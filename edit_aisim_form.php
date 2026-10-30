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
 * Defines the editing form for the AI Simulation question type.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');

/**
 * AI Simulation question editing form definition.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_aisim_edit_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built
     */
    protected function definition_inner($mform) {
        $mform->addElement('header', 'simheader', get_string('simulationsettings', 'qtype_aisim'));

        // Simulation type dropdown.
        $simtypes = [
            'confusion_matrix' => get_string('sim_cm', 'qtype_aisim'),
            'gradient_descent' => get_string('sim_gd', 'qtype_aisim'),
            'regression' => get_string('sim_reg', 'qtype_aisim'),
            'knn_tree' => get_string('sim_knn', 'qtype_aisim'),
            'kmeans' => get_string('sim_km', 'qtype_aisim'),
            'neural_network' => get_string('sim_nn', 'qtype_aisim'),
            'convolution' => get_string('sim_conv', 'qtype_aisim'),
            'q_learning' => get_string('sim_ql', 'qtype_aisim'),
        ];
        $mform->addElement('select', 'simtype', get_string('simtype', 'qtype_aisim'), $simtypes);
        $mform->setDefault('simtype', 'confusion_matrix');
        $mform->addHelpButton('simtype', 'simtype', 'qtype_aisim');

        // Scenario / Preset.
        $mform->addElement('text', 'scenario', get_string('scenario', 'qtype_aisim'), ['size' => 30]);
        $mform->setType('scenario', PARAM_ALPHANUMEXT);
        $mform->setDefault('scenario', 'quality');
        $mform->addHelpButton('scenario', 'scenario', 'qtype_aisim');

        // Target Metric 1 (Primary Goal).
        $mform->addElement('text', 'targetparam1', get_string('targetparam1', 'qtype_aisim'), ['size' => 15]);
        $mform->setType('targetparam1', PARAM_FLOAT);
        $mform->setDefault('targetparam1', 0.90);
        $mform->addHelpButton('targetparam1', 'targetparam1', 'qtype_aisim');

        // Target Metric 2 (Secondary Goal).
        $mform->addElement('text', 'targetparam2', get_string('targetparam2', 'qtype_aisim'), ['size' => 15]);
        $mform->setType('targetparam2', PARAM_FLOAT);
        $mform->setDefault('targetparam2', 0.75);
        $mform->addHelpButton('targetparam2', 'targetparam2', 'qtype_aisim');

        // Tolerance.
        $mform->addElement('text', 'targettolerance', get_string('targettolerance', 'qtype_aisim'), ['size' => 15]);
        $mform->setType('targettolerance', PARAM_FLOAT);
        $mform->setDefault('targettolerance', 0.02);
        $mform->addHelpButton('targettolerance', 'targettolerance', 'qtype_aisim');

        // Advanced Simulation JSON Configuration.
        $mform->addElement('textarea', 'simconfig', get_string('simconfig', 'qtype_aisim'), ['rows' => 3, 'cols' => 60]);
        $mform->setType('simconfig', PARAM_RAW);
        $mform->setDefault('simconfig', '');
        $mform->addHelpButton('simconfig', 'simconfig', 'qtype_aisim');

        // Combined feedback fields.
        $this->add_combined_feedback_fields(true);

        // Multiple tries / Hints.
        $this->add_interactive_settings(true, true);
    }

    /**
     * Preprocess data when editing an existing question.
     *
     * @param object $question
     * @return object
     */
    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_combined_feedback($question, true);
        $question = $this->data_preprocessing_hints($question, true, true);

        if (!empty($question->options)) {
            $question->simtype = $question->options->simtype;
            $question->scenario = $question->options->scenario;
            $question->targetparam1 = $question->options->targetparam1;
            $question->targetparam2 = $question->options->targetparam2;
            $question->targettolerance = $question->options->targettolerance;
            $question->simconfig = $question->options->simconfig;
        }

        return $question;
    }

    /**
     * Validate the form data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (empty($data['simtype'])) {
            $errors['simtype'] = get_string('required');
        }

        if (!empty($data['simconfig'])) {
            $decoded = json_decode($data['simconfig'], true);
            if ($decoded === null && trim($data['simconfig']) !== '') {
                $errors['simconfig'] = get_string('invalidjson', 'qtype_aisim');
            }
        }

        return $errors;
    }

    /**
     * Question type name.
     *
     * @return string
     */
    public function qtype(): string {
        return 'aisim';
    }
}
