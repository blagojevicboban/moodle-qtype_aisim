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
 * AI Simulation question renderer.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renders the question formulation, goal instructions, interactive simulation widget, and review status.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_aisim_renderer extends qtype_renderer {
    /**
     * Generate the display of the question formulation and the response controls.
     *
     * @param question_attempt $qa the attempt
     * @param question_display_options $options display options
     * @return string HTML
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options): string {
        $question = $qa->get_question();
        $inputname = $qa->get_qt_field_name('answer');
        $answer = (string) $qa->get_last_qt_var('answer', '');
        $containerid = $inputname . '_aisim';

        // 1. Question text.
        $result = html_writer::tag('div', $question->format_questiontext($qa), ['class' => 'qtext']);

        // 2. Goal card banner.
        $goaltext = $question->get_right_answer_summary();
        $goalhtml = html_writer::tag('strong', get_string('taskgoal', 'qtype_aisim') . ': ') . s($goaltext);

        $statusbadge = '';
        if ($options->readonly && !empty($answer)) {
            $data = qtype_aisim_question::decode_answer($answer);
            if ($data) {
                [$fraction] = $question->grade_response(['answer' => $answer]);
                if ($fraction >= 1.0) {
                    $badgeclass = 'aisim-badge-success';
                    $badgetext = get_string('goalsatisfied', 'qtype_aisim');
                } else if ($fraction > 0.0) {
                    $badgeclass = 'aisim-badge-partial';
                    $badgetext = get_string('goalpartial', 'qtype_aisim');
                } else {
                    $badgeclass = 'aisim-badge-danger';
                    $badgetext = get_string('goalfailed', 'qtype_aisim');
                }
                $statusbadge = html_writer::tag('span', $badgetext, ['class' => 'aisim-status-badge ' . $badgeclass]);
            }
        }

        $result .= html_writer::tag(
            'div',
            html_writer::tag('div', $goalhtml, ['class' => 'aisim-goal-title']) . $statusbadge,
            ['class' => 'aisim-goal-card']
        );

        // 3. Hidden response input for storing serialized simulation parameters & metrics.
        $result .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $inputname,
            'id' => $inputname,
            'value' => $answer,
            'class' => 'qtype_aisim_response_input',
        ]);

        // 4. Interactive Simulation Canvas Container.
        $result .= html_writer::tag('div', '', [
            'id' => $containerid,
            'class' => 'qtype_aisim_container',
            'data-simtype' => $question->simtype,
        ]);

        // 5. Validation error if any.
        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag(
                'div',
                $question->get_validation_error(['answer' => $answer]),
                ['class' => 'validationerror alert alert-danger mt-2']
            );
        }

        // 6. AMD Initialization call.
        $config = [
            'simtype' => $question->simtype,
            'scenario' => $question->scenario,
            'target1' => (float)$question->targetparam1,
            'target2' => (float)$question->targetparam2,
            'tolerance' => (float)$question->targettolerance,
            'simconfig' => $question->simconfig,
            'readonly' => (bool)$options->readonly,
            'lang' => current_language(),
        ];

        $this->page->requires->js_call_amd('qtype_aisim/attempt', 'init', [
            $containerid,
            $inputname,
            $config,
        ]);

        return $result;
    }

    /**
     * Display specific feedback if options allow.
     *
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa): string {
        $question = $qa->get_question();
        $answer = (string) $qa->get_last_qt_var('answer', '');
        if ($answer === '') {
            return '';
        }

        $feedback = '';
        [$fraction] = $question->grade_response(['answer' => $answer]);
        if ($fraction >= 1.0 && !empty($question->correctfeedback)) {
            $feedback .= $question->format_text(
                $question->correctfeedback,
                $question->correctfeedbackformat,
                $qa,
                'question',
                'correctfeedback',
                null
            );
        } else if ($fraction > 0.0 && !empty($question->partiallycorrectfeedback)) {
            $feedback .= $question->format_text(
                $question->partiallycorrectfeedback,
                $question->partiallycorrectfeedbackformat,
                $qa,
                'question',
                'partiallycorrectfeedback',
                null
            );
        } else if (!empty($question->incorrectfeedback)) {
            $feedback .= $question->format_text(
                $question->incorrectfeedback,
                $question->incorrectfeedbackformat,
                $qa,
                'question',
                'incorrectfeedback',
                null
            );
        }

        return $feedback;
    }

    /**
     * Display the correct response target.
     *
     * @param question_attempt $qa
     * @return string
     */
    public function correct_response(question_attempt $qa): string {
        $question = $qa->get_question();
        $summary = $question->get_right_answer_summary();
        if (empty($summary)) {
            return '';
        }
        return get_string('correctansweris', 'qtype_aisim', $summary);
    }
}
