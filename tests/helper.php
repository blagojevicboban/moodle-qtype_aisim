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
 * Test helper for the AI Simulation question type.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Builds AI Simulation test questions.
 *
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_aisim_test_helper extends question_test_helper {
    /**
     * Names of the test questions.
     *
     * @return array
     */
    public function get_test_questions() {
        return ['nn'];
    }

    /**
     * A neural network question: XOR, accuracy at least 98% with at most 4 hidden neurons.
     *
     * @return qtype_aisim_question
     */
    public function make_aisim_question_nn(): qtype_aisim_question {
        question_bank::load_question_definition_classes('aisim');
        $q = new qtype_aisim_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'NN-02';
        $q->questiontext = 'Solve XOR with a small network.';
        $q->generalfeedback = '';
        $q->qtype = question_bank::get_qtype('aisim');
        $q->simtype = 'neural_network';
        $q->scenario = 'xor';
        $q->targetparam1 = 0.98;
        $q->targetparam2 = 4;
        $q->targettolerance = 0.0;
        $q->simconfig = '{"layers":0}';
        test_question_maker::set_standard_combined_feedback_fields($q);
        return $q;
    }

    /**
     * Form data for the neural network question, as the editing form submits it.
     *
     * @return stdClass
     */
    public function get_aisim_question_form_data_nn(): stdClass {
        $html = fn($text) => ['text' => $text, 'format' => FORMAT_HTML, 'itemid' => 0];
        return (object)[
            'name' => 'NN-02',
            'questiontext' => $html('Solve XOR with a small network.'),
            'generalfeedback' => $html('A hidden layer bends the boundary.'),
            'defaultmark' => 2,
            'penalty' => 0.3333333,
            'status' => \core_question\local\bank\question_version_status::QUESTION_STATUS_READY,
            'simtype' => 'neural_network',
            'scenario' => 'xor',
            'targetparam1' => 0.98,
            'targetparam2' => 4,
            'targettolerance' => 0.05,
            'simconfig' => '{"layers":0}',
            'correctfeedback' => $html('Well done.'),
            'partiallycorrectfeedback' => $html('Almost.'),
            'incorrectfeedback' => $html('Not yet.'),
            'shownumcorrect' => 0,
            'hint' => [$html('Add a hidden layer.')],
            'hintshownumcorrect' => [0],
            'hintclearwrong' => [0],
        ];
    }
}
