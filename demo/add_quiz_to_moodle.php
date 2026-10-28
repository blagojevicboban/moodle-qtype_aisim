<?php
/**
 * CLI script to add an interactive AI Simulation quiz with auto-graded questions to Moodle Course 77.
 *
 * Usage:
 *   php add_quiz_to_moodle.php
 */

define('CLI_SCRIPT', true);

if (file_exists('/home/tsp/moodle45/config.php')) {
    require_once('/home/tsp/moodle45/config.php');
} else {
    require_once(dirname(__DIR__, 4) . '/config.php');
}

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

global $DB, $CFG, $USER;

// Set admin as current user for CLI operations.
$USER = $DB->get_record('user', ['id' => 2]);

echo "=== Dodavanje AI Simulacionog Kviza u Kurs 77 (moodle45) ===\n\n";

$courseid = 77;
$course = $DB->get_record('course', ['id' => $courseid]);
if (!$course) {
    die("GRESKA: Kurs ID=77 ne postoji!\n");
}
echo "✓ Pronadjen kurs: {$course->fullname} ({$course->shortname})\n";

$coursecontext = context_course::instance($course->id);

// 1. Pronadji ili kreiraj kategoriju pitanja u kursu.
$qcat = $DB->get_record('question_categories', [
    'contextid' => $coursecontext->id,
    'name' => 'AI za VET - Simulacije'
]);

if (!$qcat) {
    $parentcat = question_get_top_category($coursecontext->id, true);
    $qcat = new stdClass();
    $qcat->name = 'AI za VET - Simulacije';
    $qcat->contextid = $coursecontext->id;
    $qcat->info = 'Kategorija sa pitanjima praktičnih simulacija veštačke inteligencije';
    $qcat->infoformat = FORMAT_HTML;
    $qcat->stamp = make_unique_id_code();
    $qcat->parent = $parentcat->id;
    $qcat->sortorder = 999;
    $qcat->idnumber = null;
    $qcat->id = $DB->insert_record('question_categories', $qcat);
    echo "✓ Kreirana kategorija pitanja: {$qcat->name} (ID={$qcat->id})\n";
} else {
    echo "✓ Postojeća kategorija pitanja: {$qcat->name} (ID={$qcat->id})\n";
}

// Helper funkcija za kreiranje qtype_aisim pitanja u banci pitanja.
function create_aisim_question($catid, $name, $text, $simtype, $scenario, $p1, $p2, $tol, $config = '') {
    global $DB;

    $existing = $DB->get_record_sql(
        "SELECT q.*
           FROM {question} q
           JOIN {question_versions} qv ON qv.questionid = q.id
           JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
          WHERE qbe.questioncategoryid = ? AND q.name = ?",
        [$catid, $name]
    );

    if ($existing) {
        echo "  [INFO] Pitanje već postoji: '{$name}' (ID={$existing->id})\n";
        return $existing->id;
    }

    $qtype = question_bank::get_qtype('aisim');

    $question = new stdClass();
    $question->qtype = 'aisim';

    $form = new stdClass();
    $form->category = $catid;
    $form->name = $name;
    $form->questiontext = ['text' => $text, 'format' => FORMAT_HTML];
    $form->generalfeedback = ['text' => '', 'format' => FORMAT_HTML];
    $form->defaultmark = 1.0;
    $form->penalty = 0.3333333;
    $form->status = 'ready';

    // AISIM Opcije
    $form->simtype = $simtype;
    $form->scenario = $scenario;
    $form->targetparam1 = (float)$p1;
    $form->targetparam2 = (float)$p2;
    $form->targettolerance = (float)$tol;
    $form->simconfig = $config;

    $form->correctfeedback = 'Odlično! Uspešno ste podesili model tako da zadovolji zadate kriterijume performansi.';
    $form->correctfeedbackformat = FORMAT_HTML;
    $form->partiallycorrectfeedback = 'Delimično tačno. Zadovoljili ste jedan od zadatih ciljeva, ali je potrebno dodatno podesiti parametre.';
    $form->partiallycorrectfeedbackformat = FORMAT_HTML;
    $form->incorrectfeedback = 'Cilj nije ostvaren. Pažljivo proučite ponašanje modela na grafiku i podesite parametre u skladu sa uputstvom.';
    $form->incorrectfeedbackformat = FORMAT_HTML;

    $savedq = $qtype->save_question($question, $form);
    echo "  [OK] Kreirano pitanje '{$name}' (ID={$savedq->id})\n";
    return $savedq->id;
}

echo "\nKreiranje pitanja u banci pitanja...\n";

// Pitanje 1: Matrica konfuzije
$q1Id = create_aisim_question(
    $qcat->id,
    'Zadatak 1: Medicinska dijagnostika (Prag odlučivanja)',
    '<p>U medicinskom skriningu prioritet je otkriti što veći procenat obolelih pacijenata (visok Recall), uz prihvatljivu preciznost.</p>' .
    '<p><strong>Zadatak:</strong> Na interaktivnoj simulaciji podesite prag odlučivanja &theta; tako da postignete <strong>Recall &ge; 90%</strong> i <strong>Precision &ge; 70%</strong>.</p>',
    'confusion_matrix',
    'medical',
    0.90,
    0.70,
    0.02
);

// Pitanje 2: Gradijentni spust
$q2Id = create_aisim_question(
    $qcat->id,
    'Zadatak 2: Optimizacija funkcije troška gradijentnim spustom',
    '<p>Cilj obučavanja modela je pronalaženje težine $w$ koja minimizuje funkciju troška $J(w)$.</p>' .
    '<p><strong>Zadatak:</strong> Podesite odgovarajuću stopu učenja &alpha; i početnu težinu $w_0$, a zatim pokrenite gradijentni spust tako da model konvergira u minimum sa greškom <strong>J(w) &le; 0.05</strong>.</p>',
    'gradient_descent',
    'convex',
    0.05,
    0.0,
    0.02
);

// Pitanje 3: Polinomska regresija i regularizacija
$q3Id = create_aisim_question(
    $qcat->id,
    'Zadatak 3: Polinomska regresija i sprečavanje preprilagođavanja',
    '<p>Kompleksni modeli visokog stepena mogu memorisati šum u trening podacima i dovesti do loše generalizacije (Overfitting).</p>' .
    '<p><strong>Zadatak:</strong> Odaberite optimalan stepen polinoma $d$ (maksimalno $d \le 5$) tako da postignete validacionu grešku <strong>Validation MSE &le; 0.15</strong>.</p>',
    'regression',
    'sine',
    0.15,
    5.0,
    0.02
);

// 2. Kreiraj Kviz u Kursu 77 ako već ne postoji.
$quizname = 'Kviz znanja: Praktične AI simulacije i optimizacija';
$quiz = $DB->get_record('quiz', ['course' => $course->id, 'name' => $quizname]);

if (!$quiz) {
    echo "\nKreiranje novog kviza u kursu...\n";
    $quiz = new stdClass();
    $quiz->course = $course->id;
    $quiz->name = $quizname;
    $quiz->intro = '<p>U ovom kvizu rešavate praktične zadatke mašinskog učenja direktnim podešavanjem interaktivnih modela u realnom vremenu.</p>' .
                   '<p>Nakon podešavanja parametara, predajte odgovor. Sistem će automatski oceniti vaš rad na osnovu postignutih metrika!</p>';
    $quiz->introformat = FORMAT_HTML;
    $quiz->timeopen = 0;
    $quiz->timeclose = 0;
    $quiz->timelimit = 0;
    $quiz->overduehandling = 'autosubmit';
    $quiz->graceperiod = 0;
    $quiz->preferredbehaviour = 'interactive'; // Interaktivno sa višestrukim pokušajima i proverom
    $quiz->canredoquestions = 1;
    $quiz->attempts = 0; // Neograničeno
    $quiz->attemptonlast = 0;
    $quiz->grademethod = QUIZ_GRADEHIGHEST;
    $quiz->decimalpoints = 2;
    $quiz->questiondecimalpoints = -1;
    $quiz->reviewattempt = 0x11110;
    $quiz->reviewcorrectness = 0x11110;
    $quiz->reviewmaxmarks = 0x11110;
    $quiz->reviewmarks = 0x11110;
    $quiz->reviewspecificfeedback = 0x11110;
    $quiz->reviewgeneralfeedback = 0x11110;
    $quiz->reviewrightanswer = 0x11110;
    $quiz->reviewoverallfeedback = 0x11110;
    $quiz->questionsperpage = 1;
    $quiz->navmethod = 'free';
    $quiz->shuffleanswers = 0;
    $quiz->sumgrades = 3.0;
    $quiz->grade = 100.0;
    $quiz->timecreated = time();
    $quiz->timemodified = time();
    $quiz->password = '';
    $quiz->subnet = '';
    $quiz->browsersecurity = '-';
    $quiz->delay1 = 0;
    $quiz->delay2 = 0;
    $quiz->showuserpicture = 0;
    $quiz->showblocks = 0;
    $quiz->completionattemptsexhausted = 0;
    $quiz->completionpass = 0;
    $quiz->allowofflineattempts = 0;

    $quiz->id = $DB->insert_record('quiz', $quiz);

    // Kreiraj course module
    $module = $DB->get_record('modules', ['name' => 'quiz']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $module->id;
    $cm->instance = $quiz->id;
    $cm->section = 1; // Sekcija 1
    $cm->idnumber = null;
    $cm->added = time();
    $cm->score = 0;
    $cm->indent = 0;
    $cm->visible = 1;
    $cm->visibleold = 1;
    $cm->groupmode = 0;
    $cm->groupingid = 0;
    $cm->completion = 0;
    $cm->id = $DB->insert_record('course_modules', $cm);

    // Dodaj u sekciju
    course_add_cm_to_section($course->id, $cm->id, 1);
    $quiz->cmid = $cm->id;

    // Kreiraj prvu sekciju u kvizu (OBAVEZNO ZA MOODLE QUIZ!)
    $DB->insert_record('quiz_sections', [
        'quizid' => $quiz->id,
        'firstslot' => 1,
        'heading' => '',
        'shufflequestions' => 0
    ]);

    // Dodaj pitanja u kviz
    quiz_add_quiz_question($q1Id, $quiz, 1, 1.0);
    quiz_add_quiz_question($q2Id, $quiz, 2, 1.0);
    quiz_add_quiz_question($q3Id, $quiz, 3, 1.0);

    echo "✓ Kviz je uspešno kreiran: ID={$quiz->id}, cmid={$cm->id}\n";
    $cmid = $cm->id;
} else {
    $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
    $cmid = $cm->id;
    $quiz->cmid = $cmid;
    $quiz->coursemodule = $cmid;
    echo "✓ Postojeći kviz: ID={$quiz->id}, cmid={$cmid}\n";

    // Proveri da li kviz ima sekciju u quiz_sections.
    $hassection = $DB->record_exists('quiz_sections', ['quizid' => $quiz->id]);
    if (!$hassection) {
        $DB->insert_record('quiz_sections', [
            'quizid' => $quiz->id,
            'firstslot' => 1,
            'heading' => '',
            'shufflequestions' => 0
        ]);
        echo "  ✓ Dodata nedostajuća sekcija u quiz_sections za kviz ID={$quiz->id}\n";
    }

    // Obriši neispravne stare pokušaje koji su imali prazan layout pre dodavanja sekcije
    $brokenattempts = $DB->get_records_select('quiz_attempts', "quiz = ? AND (layout IS NULL OR layout = '')", [$quiz->id]);
    if ($brokenattempts) {
        foreach ($brokenattempts as $ba) {
            $DB->delete_records('quiz_attempts', ['id' => $ba->id]);
            echo "  ✓ Obrisan neispravan prazan pokušaj ID={$ba->id}\n";
        }
    }
}

quiz_update_sumgrades($quiz);

// Očisti keš kursa
rebuild_course_cache($course->id, true);

echo "\n=== Kviz je spreman za testiranje! ===\n";
echo "Direktan link ka kvizu: 👉 http://localhost/m45/mod/quiz/view.php?id={$cmid}\n";
echo "Kurs: 👉 http://localhost/m45/course/view.php?id={$course->id}\n";
