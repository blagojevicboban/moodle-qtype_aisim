<?php
/**
 * CLI script to create a complete interactive demo course on Moodle 5.3.
 *
 * Includes:
 * 1. Educational Pages with embedded AI simulations (filter_aisim / tiny_aisim).
 * 2. Question Bank with all 5 auto-graded AI simulation question types (qtype_aisim).
 * 3. Interactive Quiz with all 5 questions configured and ready for students.
 */

define('CLI_SCRIPT', true);
require('/home/tsp/moodle/config.php');
$CFG->debug = E_ALL;
$CFG->debugdisplay = 1;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->libdir . '/filterlib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

global $DB, $CFG, $USER;

$USER = $DB->get_record('user', ['id' => 2]); // Admin user.

echo "==========================================================\n";
echo "Kreiranje kompletnog oglednog kursa na Moodle 5.3 (AI for VET)\n";
echo "==========================================================\n\n";

// 1. Enable filter_aisim globally.
filter_set_global_state('aisim', TEXTFILTER_ON);
echo "[OK] Filter filter_aisim je aktiviran (TEXTFILTER_ON).\n";

// 2. Find or create Course.
$shortname = 'AIVET_SIM53';
$course = $DB->get_record('course', ['shortname' => $shortname]);

if (!$course) {
    $category = $DB->get_record('course_categories', ['id' => 1]);
    $courseData = new stdClass();
    $courseData->category = $category->id;
    $courseData->fullname = 'AI for VET: Praktične AI simulacije i modeli mašinskog učenja';
    $courseData->shortname = $shortname;
    $courseData->summary = '<p>Zvanični ogledni kurs sa interaktivnim simulacijama veštačke inteligencije i mašinskog učenja (Erasmus+ projekat <strong>AI for VET</strong>). Kurs sadrži teorijske laboratorijske vežbe i automatski ocenjivane simulacione kvizove.</p>';
    $courseData->summaryformat = FORMAT_HTML;
    $courseData->format = 'topics';
    $courseData->numsections = 2;
    $courseData->visible = 1;

    $course = create_course($courseData);
    echo "[OK] Kreiran novi kurs: ID={$course->id} ('{$course->fullname}').\n";
} else {
    echo "[INFO] Kurs već postoji: ID={$course->id} ('{$course->fullname}').\n";
}

$coursecontext = context_course::instance($course->id);

// Helper function to add/update Page activity.
function add_or_update_page($course, $sectionNum, $name, $htmlContent) {
    global $DB;

    $existing = $DB->get_record('page', ['course' => $course->id, 'name' => $name]);
    if ($existing) {
        $existing->content = $htmlContent;
        $DB->update_record('page', $existing);
        echo "  [UPDATE] Stranica '{$name}' je ažurirana.\n";
        return;
    }

    $page = new stdClass();
    $page->course = $course->id;
    $page->name = $name;
    $page->intro = '<p>Interaktivna laboratorijska lekcija sa AI simulacijom.</p>';
    $page->introformat = FORMAT_HTML;
    $page->content = $htmlContent;
    $page->contentformat = FORMAT_HTML;
    $page->timemodified = time();

    $pageId = $DB->insert_record('page', $page);

    $mod = $DB->get_record('modules', ['name' => 'page']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $mod->id;
    $cm->instance = $pageId;
    $cm->section = $sectionNum;
    $cm->visible = 1;
    $cm->visibleold = 1;
    $cm->groupmode = 0;
    $cm->groupingid = 0;
    $cm->completion = 0;
    $cmId = add_course_module($cm);

    $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $sectionNum]);
    if (!$section) {
        $section = new stdClass();
        $section->course = $course->id;
        $section->section = $sectionNum;
        $section->summary = '';
        $section->summaryformat = FORMAT_HTML;
        $section->visible = 1;
        $section->sequence = '';
        $sectionId = $DB->insert_record('course_sections', $section);
        $section->id = $sectionId;
    }

    course_add_cm_to_section($course, $cmId, $sectionNum);
    echo "  [OK] Kreirana nova stranica: '{$name}' (cmid={$cmId}).\n";
}

echo "\n--- SEKCIJA 1: Kreiranje interaktivnih lekcija sa simulacijama ---\n";

// Stranica 1: Gradijentni spust
$p1_html = <<<HTML
<div class="aivet-lesson">
    <h3>1. Optimizacija modela i Gradijentni spust</h3>
    <p>Gradijentni spust (<em>Gradient Descent</em>) je temeljni algoritam za treniranje modela mašinskog učenja i neuronskih mreža. Cilj algoritma je minimizacija funkcije troška <strong>J(w)</strong> ažuriranjem težina u smeru suprotnom od gradijenta:</p>
    <p style="text-align:center; font-size:1.15rem; font-weight:600; color:#1e40af;">w &larr; w - &alpha; &middot; &nabla;J(w)</p>
    <p>U simulaciji ispod istražite uticaj stope učenja (&alpha;) na brzinu konvergencije kod konveksne i nekonveksne funkcije troška:</p>
    <div class="aisim-simulation-card" data-aisim="gradient_descent" data-profile="convex" data-lr="0.10" data-w0="3.50">
        <div class="aisim-editor-placeholder" contenteditable="false">
            <div class="aisim-ph-icon">⚡</div>
            <div class="aisim-ph-title">Gradijentni spust (Gradient Descent)</div>
            <div class="aisim-ph-desc">J(w) = w² | α = 0.10 | w₀ = 3.50</div>
            <span class="aisim-ph-pill">Dvokliknite za izmenu simulacije</span>
        </div>
    </div>
</div>
HTML;
add_or_update_page($course, 1, 'Lekcija 1: Gradijentni spust i optimizacija funkcije troška', $p1_html);

// Stranica 2: Matrica konfuzije
$p2_html = <<<HTML
<div class="aivet-lesson">
    <h3>2. Evaluacija klasifikatora i Matrica konfuzije</h3>
    <p>U industrijskim primenama veštačke inteligencije (npr. detekcija škarta na proizvodnoj liniji), tačnost (<em>Accuracy</em>) često nije dovoljna metrika zbog nebalansiranih klasa. Matrica konfuzije pruža uvid u raspodelu:</p>
    <ul>
        <li><strong>TP (True Positive)</strong>: Ispravno detektovani neispravni komadi.</li>
        <li><strong>FP (False Positive)</strong>: Lažna uzbuna (ispravan komad označen kao škart).</li>
        <li><strong>FN (False Negative)</strong>: Kritična greška (neispravan komad prošao kontrolu).</li>
        <li><strong>TN (True Negative)</strong>: Ispravno potvrđeni ispravni komadi.</li>
    </ul>
    <p>Pomerajte slajder praga odluke (&theta;) i posmatrajte promenu Precision, Recall i F1-score metrika:</p>
    <div class="aisim-simulation-card" data-aisim="confusion_matrix" data-scenario="quality" data-threshold="0.50">
        <div class="aisim-editor-placeholder" contenteditable="false">
            <div class="aisim-ph-icon">📊</div>
            <div class="aisim-ph-title">Matrica konfuzije i prag odlučivanja</div>
            <div class="aisim-ph-desc">Scenario: quality | Prag: 0.50</div>
            <span class="aisim-ph-pill">Dvokliknite za izmenu simulacije</span>
        </div>
    </div>
</div>
HTML;
add_or_update_page($course, 1, 'Lekcija 2: Matrica konfuzije i evaluacija klasifikatora', $p2_html);

// Stranica 3: Polinomska regresija
$p3_html = <<<HTML
<div class="aivet-lesson">
    <h3>3. Polinomska regresija i regularizacija (Underfitting vs Overfitting)</h3>
    <p>Kako stepen polinoma raste, model dobija veći kapacitet da prati podatke, ali postoji rizik od prepodešavanja (<em>overfitting</em>). Regularizacija (L2 / Ridge) dodaje kaznu na veličinu težina &lambda;&Sigma;w²:</p>
    <div class="aisim-simulation-card" data-aisim="regression" data-dataset="sine" data-degree="3" data-lambda="0.0">
        <div class="aisim-editor-placeholder" contenteditable="false">
            <div class="aisim-ph-icon">📈</div>
            <div class="aisim-ph-title">Polinomska regresija i regularizacija</div>
            <div class="aisim-ph-desc">Skup: sine | Stepen d = 3 | λ = 0.0</div>
            <span class="aisim-ph-pill">Dvokliknite za izmenu simulacije</span>
        </div>
    </div>
</div>
HTML;
add_or_update_page($course, 1, 'Lekcija 3: Polinomska regresija i regularizacija', $p3_html);

// Stranica 4: Granice odlučivanja
$p4_html = <<<HTML
<div class="aivet-lesson">
    <h3>4. Granice odlučivanja: k-NN i Stablo odlučivanja</h3>
    <p>Uporedite dva različita pristupa klasifikaciji: algoritme bazirane na instancama (<strong>k-NN</strong>) i hijerarhijsko particionisanje prostora (<strong>Decision Tree</strong>):</p>
    <div class="aisim-simulation-card" data-aisim="knn_tree" data-model="knn" data-dataset="moons" data-k="3" data-depth="3">
        <div class="aisim-editor-placeholder" contenteditable="false">
            <div class="aisim-ph-icon">🌲</div>
            <div class="aisim-ph-title">Granice odlučivanja (k-NN i Stablo odlučivanja)</div>
            <div class="aisim-ph-desc">Model: knn | Skup: moons | k = 3 | dubina = 3</div>
            <span class="aisim-ph-pill">Dvokliknite za izmenu simulacije</span>
        </div>
    </div>
</div>
HTML;
add_or_update_page($course, 1, 'Lekcija 4: Nelinearne granice odlučivanja (k-NN i Stablo)', $p4_html);

// Stranica 5: K-Means klasterovanje
$p5_html = <<<HTML
<div class="aivet-lesson">
    <h3>5. Nenadgledano učenje i K-Means klasterizacija</h3>
    <p>K-Means je vodeći algoritam za klasterovanje bez labela. Kroz iterativno dodeljivanje tačaka najbližem centroidu i re-kalkulaciju pozicija centroida, algoritam pronalazi prirodne grupacije u podacima:</p>
    <div class="aisim-simulation-card" data-aisim="kmeans" data-dataset="blobs3" data-k="3">
        <div class="aisim-editor-placeholder" contenteditable="false">
            <div class="aisim-ph-icon">🎯</div>
            <div class="aisim-ph-title">K-Means klasterovanje</div>
            <div class="aisim-ph-desc">Skup: blobs3 | K = 3 klastera</div>
            <span class="aisim-ph-pill">Dvokliknite za izmenu simulacije</span>
        </div>
    </div>
</div>
HTML;
add_or_update_page($course, 1, 'Lekcija 5: K-Means klasterovanje i nenadgledano učenje', $p5_html);

echo "\n--- SEKCIJA 2: Kreiranje automatski ocenjivanog kviza (qtype_aisim) ---\n";

// 1. Pronađi ili kreiraj kategoriju u Banci pitanja.
$topcat = $DB->get_record('question_categories', ['contextid' => $coursecontext->id, 'parent' => 0]);
if (!$topcat) {
    $topcat = new stdClass();
    $topcat->name = 'top';
    $topcat->contextid = $coursecontext->id;
    $topcat->info = '';
    $topcat->infoformat = FORMAT_HTML;
    $topcat->stamp = make_unique_id_code();
    $topcat->parent = 0;
    $topcat->sortorder = 0;
    $topcat->idnumber = null;
    $topcat->id = $DB->insert_record('question_categories', $topcat);
}

$qcat = $DB->get_record('question_categories', [
    'contextid' => $coursecontext->id,
    'name' => 'AI za VET - Simulacije'
]);

if (!$qcat) {
    $qcat = new stdClass();
    $qcat->name = 'AI za VET - Simulacije';
    $qcat->contextid = $coursecontext->id;
    $qcat->info = 'Kategorija sa interaktivnim simulacionim pitanjima (qtype_aisim)';
    $qcat->infoformat = FORMAT_HTML;
    $qcat->stamp = make_unique_id_code();
    $qcat->parent = $topcat->id;
    $qcat->sortorder = 999;
    $qcat->idnumber = null;
    $qcat->id = $DB->insert_record('question_categories', $qcat);
    echo "[OK] Kreirana kategorija pitanja: {$qcat->name} (ID={$qcat->id})\n";
} else {
    echo "[INFO] Postojeća kategorija pitanja: {$qcat->name} (ID={$qcat->id})\n";
}

// Helper za kreiranje pitanja tipa aisim.
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
        echo "  [INFO] Pitanje '{$name}' već postoji (ID={$existing->id}).\n";
        return $existing->id;
    }

    $entry = new stdClass();
    $entry->questioncategoryid = $catid;
    $entry->idnumber = null;
    $entryid = $DB->insert_record('question_bank_entries', $entry);

    $q = new stdClass();
    $q->category = $catid;
    $q->parent = 0;
    $q->name = $name;
    $q->questiontext = $text;
    $q->questiontextformat = FORMAT_HTML;
    $q->generalfeedback = '<p>Bravo! Uspešno ste podesili parametre simulacije tako da model zadovolji tražene kriterijume.</p>';
    $q->generalfeedbackformat = FORMAT_HTML;
    $q->defaultmark = 2.0;
    $q->penalty = 0.3333333;
    $q->qtype = 'aisim';
    $q->length = 1;
    $q->stamp = make_unique_id_code();
    $q->timecreated = time();
    $q->timemodified = time();
    $q->createdby = 2;
    $q->modifiedby = 2;
    $qid = $DB->insert_record('question', $q);

    $version = new stdClass();
    $version->questionbankentryid = $entryid;
    $version->version = 1;
    $version->questionid = $qid;
    $version->status = 'ready';
    $DB->insert_record('question_versions', $version);

    $opts = new stdClass();
    $opts->questionid = $qid;
    $opts->simtype = $simtype;
    $opts->scenario = $scenario;
    $opts->targetparam1 = (float)$p1;
    $opts->targetparam2 = (float)$p2;
    $opts->targettolerance = (float)$tol;
    $opts->simconfig = $config;
    $opts->correctfeedback = '<p>Odlično! Simulacioni model je konvergirao u optimalnu zonu.</p>';
    $opts->correctfeedbackformat = FORMAT_HTML;
    $opts->partiallycorrectfeedback = '<p>Delimično tačno. Model je blizu optimuma, ali još uvek nije dostigao željenu tačnost.</p>';
    $opts->partiallycorrectfeedbackformat = FORMAT_HTML;
    $opts->incorrectfeedback = '<p>Netačno. Pokušajte ponovo da promenite parametre.</p>';
    $opts->incorrectfeedbackformat = FORMAT_HTML;
    $DB->insert_record('qtype_aisim_options', $opts);

    echo "  [OK] Kreirano novo pitanje: '{$name}' (ID={$qid}).\n";
    return $qid;
}

$qids = [];
$qids[] = create_aisim_question(
    $qcat->id,
    'Zadatak 1: Optimizacija stope učenja kod gradijentnog spusta',
    '<p>Ispitujete ponašanje gradijentnog spusta na konveksnoj funkciji troška čiji je minimum J(w) = 1.00. Podesite stopu učenja (&alpha;) i pokrenite algoritam tako da trošak padne na J(w) &le; 1.01 (za J(w) &le; 1.06 pola poena).</p>',
    'gradient_descent', 'convex', 1.01, 0.0, 0.05, '{"lr":0.10,"w0":3.50}'
);

$qids[] = create_aisim_question(
    $qcat->id,
    'Zadatak 2: Podešavanje praga odluke za kontrolu kvaliteta',
    '<p>Radite u fabrici automobilskih delova. Podesite prag odlučivanja (&theta;) klasifikatora tako da uhvatite većinu škarta bez previše lažnih uzbuna: odziv (Recall) &ge; 0.80 i preciznost (Precision) &ge; 0.70.</p>',
    'confusion_matrix', 'quality', 0.80, 0.70, 0.05, '{"threshold":0.30}'
);

$qids[] = create_aisim_question(
    $qcat->id,
    'Zadatak 3: Balansiranje složenosti polinomske regresije',
    '<p>Za periodični signal sa šumom odaberite stepen polinoma najviše 7 tako da model generalizuje trend bez preprilagođavanja, uz validacionu grešku MSE &le; 0.19.</p>',
    'regression', 'sine', 0.19, 7.0, 0.05, '{"degree":3,"lambda":0.0}'
);

$qids[] = create_aisim_question(
    $qcat->id,
    'Zadatak 4: Granice odlučivanja k-NN klasifikatora',
    '<p>k-NN klasifikator na skupu podataka "Dva polumeseca" kreće sa k = 15 i granica je previše uglačana. Smanjite broj suseda k tako da tačnost klasifikacije bude barem 98%.</p>',
    'knn_tree', 'moons', 0.98, 0.0, 0.05, '{"model":"knn","k":15}'
);

$qids[] = create_aisim_question(
    $qcat->id,
    'Zadatak 5: Pronalaženje klastera K-Means algoritmom',
    '<p>Pokrenite K-Means algoritam sa odgovarajućim brojem klastera (K = 3) i dovedite centroide do stabilne konvergencije, sa WCSS &le; 10.</p>',
    'kmeans', 'blobs3', 10.0, 3.0, 0.05, '{"k":2}'
);

// Kreiranje Kviz aktivnosti.
$quizname = 'Kviz znanja: Praktične AI simulacije i optimizacija';
$quiz = $DB->get_record('quiz', ['course' => $course->id, 'name' => $quizname]);

if (!$quiz) {
    $quiz = new stdClass();
    $quiz->course = $course->id;
    $quiz->name = $quizname;
    $quiz->intro = '<p>U ovom kvizu testirate svoje razumevanje rada algoritama veštačke inteligencije direktno kroz interaktivne simulacije. Pomerajte kontrole i posmatrajte odziv modela.</p>';
    $quiz->introformat = FORMAT_HTML;
    $quiz->timeopen = 0;
    $quiz->timeclose = 0;
    $quiz->timelimit = 0;
    $quiz->overduehandling = 'autosubmit';
    $quiz->graceperiod = 0;
    $quiz->preferredbehaviour = 'immediatefeedback'; // Trenutna povratna informacija!
    $quiz->attempts = 0; // Neograničeno.
    $quiz->grademethod = QUIZ_GRADEHIGHEST;
    $quiz->decimalpoints = 2;
    $quiz->questiondecimalpoints = -1;
    $quiz->reviewattempt = 0x11110;
    $quiz->reviewcorrectness = 0x10000;
    $quiz->reviewmarks = 0x10000;
    $quiz->reviewspecificfeedback = 0x10000;
    $quiz->reviewgeneralfeedback = 0x10000;
    $quiz->reviewrightanswer = 0x10000;
    $quiz->reviewoverallfeedback = 0x10000;
    $quiz->questionsperpage = 1;
    $quiz->navmethod = QUIZ_NAVMETHOD_FREE;
    $quiz->shuffleanswers = 0;
    $quiz->sumgrades = 10.0;
    $quiz->grade = 10.0;
    $quiz->timecreated = time();
    $quiz->timemodified = time();

    $quiz->id = $DB->insert_record('quiz', $quiz);

    $mod = $DB->get_record('modules', ['name' => 'quiz']);
    $cm = new stdClass();
    $cm->course = $course->id;
    $cm->module = $mod->id;
    $cm->instance = $quiz->id;
    $cm->section = 2;
    $cm->visible = 1;
    $cm->visibleold = 1;
    $cm->groupmode = 0;
    $cm->groupingid = 0;
    $cm->completion = 0;
    $cmId = add_course_module($cm);

    $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]);
    if (!$section) {
        $section = new stdClass();
        $section->course = $course->id;
        $section->section = 2;
        $section->summary = '';
        $section->summaryformat = FORMAT_HTML;
        $section->visible = 1;
        $section->sequence = '';
        $sectionId = $DB->insert_record('course_sections', $section);
        $section->id = $sectionId;
    }

    course_add_cm_to_section($course, $cmId, 2);

    // Dodaj sekciju za kviz.
    $qsec = new stdClass();
    $qsec->quizid = $quiz->id;
    $qsec->firstslot = 1;
    $qsec->heading = '';
    $qsec->shufflequestions = 0;
    $DB->insert_record('quiz_sections', $qsec);

    // Dodaj pitanja u kviz slotove.
    $slotnum = 1;
    foreach ($qids as $qid) {
        $slot = new stdClass();
        $slot->quizid = $quiz->id;
        $slot->slot = $slotnum;
        $slot->page = $slotnum;
        $slot->requireprevious = 0;
        $slot->maxmark = 2.0;
        $slot->displaynumber = (string)$slotnum;
        $slotid = $DB->insert_record('quiz_slots', $slot);

        $qref = new stdClass();
        $qref->usingcontextid = context_module::instance($cmId)->id;
        $qref->component = 'mod_quiz';
        $qref->questionarea = 'slot';
        $qref->itemid = $slotid;

        $qv = $DB->get_record('question_versions', ['questionid' => $qid]);
        $qref->questionbankentryid = $qv->questionbankentryid;
        $qref->version = null; // Latest version.
        $DB->insert_record('question_references', $qref);

        $slotnum++;
    }

    echo "[OK] Kreiran Kviz: '{$quiz->name}' (ID={$quiz->id}, cmid={$cmId}) sa 5 interaktivnih pitanja.\n";
} else {
    echo "[INFO] Kviz već postoji: ID={$quiz->id}.\n";
}

// Očisti keševe.
purge_all_caches();
echo "\n==========================================================\n";
echo "Uspešno postavljen ogledni kurs na Moodle 5.3!\n";
echo "Pristup kursu: {$CFG->wwwroot}/course/view.php?id={$course->id}\n";
echo "==========================================================\n";
