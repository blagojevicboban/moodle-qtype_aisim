<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Question type class for the AI Simulation question type.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

/**
 * The AI Simulation question type: the student interacts with an AI simulation,
 * and their model parameters/metrics are graded automatically against target criteria.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_aisim extends question_type {

    /**
     * Extra question options stored in mdl_qtype_aisim_options.
     *
     * @return array
     */
    public function extra_question_fields(): array {
        return [
            'qtype_aisim_options',
            'simtype', 'scenario', 'targetparam1', 'targetparam2', 'targettolerance', 'simconfig',
            'correctfeedback', 'correctfeedbackformat',
            'partiallycorrectfeedback', 'partiallycorrectfeedbackformat',
            'incorrectfeedback', 'incorrectfeedbackformat',
            'shownumcorrect',
        ];
    }

    /**
     * AI simulation questions are graded automatically.
     *
     * @return bool
     */
    public function is_manual_graded(): bool {
        return false;
    }

    /**
     * Questions can be picked by random questions in quizzes.
     *
     * @return bool
     */
    public function is_usable_by_random(): bool {
        return true;
    }

    /**
     * Response analysis is available.
     *
     * @return bool
     */
    public function can_analyse_responses(): bool {
        return true;
    }

    /**
     * This type stores no files with the response.
     *
     * @return array
     */
    public function response_file_areas(): array {
        return [];
    }

    /**
     * Initialise the question definition from the loaded data.
     *
     * @param question_definition $question the question being initialised.
     * @param object $questiondata the question data from the database.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata): void {
        parent::initialise_question_instance($question, $questiondata);

        $options = $questiondata->options ?? null;
        if ($options) {
            $question->simtype = $options->simtype ?? 'confusion_matrix';
            $question->scenario = $options->scenario ?? '';
            $question->targetparam1 = (float)($options->targetparam1 ?? 0.0);
            $question->targetparam2 = (float)($options->targetparam2 ?? 0.0);
            $question->targettolerance = (float)($options->targettolerance ?? 0.0);
            $question->simconfig = $options->simconfig ?? '';
        }

        $this->initialise_combined_feedback($question, $questiondata, true);
    }

    /**
     * A random guess score is zero.
     *
     * @param stdClass $questiondata the question data
     * @return float
     */
    public function get_random_guess_score($questiondata): float {
        return 0.0;
    }

    /**
     * Export question to Moodle XML format.
     *
     * @param stdClass $question question data
     * @param qformat_xml $format the export format class
     * @param mixed $extra unused
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null): string {
        $fields = $this->extra_question_fields();
        array_shift($fields);
        $out = '';
        foreach ($fields as $field) {
            $val = $question->options->$field ?? '';
            $out .= "    <{$field}>" . $format->xml_escape((string)$val) . "</{$field}>\n";
        }
        return $out;
    }

    /**
     * Import question from Moodle XML format.
     *
     * @param array $data parsed XML data
     * @param stdClass $question unused
     * @param qformat_xml $format the import format class
     * @param mixed $extra unused
     * @return stdClass|false
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (($data['@']['type'] ?? '') !== $this->name()) {
            return false;
        }

        $qo = $format->import_headers($data);
        $qo->qtype = $this->name();

        $fields = $this->extra_question_fields();
        array_shift($fields);

        $defaults = [
            'simtype' => 'confusion_matrix',
            'scenario' => 'quality',
            'targetparam1' => '0.8500',
            'targetparam2' => '1.0000',
            'targettolerance' => '0.0500',
            'simconfig' => '',
            'correctfeedback' => '<p>Odlično!</p>',
            'correctfeedbackformat' => 1,
            'partiallycorrectfeedback' => '<p>Delimično tačno.</p>',
            'partiallycorrectfeedbackformat' => 1,
            'incorrectfeedback' => '<p>Netačno.</p>',
            'incorrectfeedbackformat' => 1,
            'shownumcorrect' => 0,
        ];

        foreach ($fields as $field) {
            $qo->$field = $format->getpath($data, ['#', $field, 0, '#'], $defaults[$field] ?? '');
        }

        return $qo;
    }
}

