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
 * AI Simulation question definition class.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Represents an AI Simulation question.
 * The student interacts with a machine learning simulation, and their resulting
 * parameters and performance metrics are automatically evaluated.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_aisim_question extends question_graded_automatically {

    /** @var string Simulation type (e.g. confusion_matrix, gradient_descent, regression, knn_tree, kmeans). */
    public $simtype = 'confusion_matrix';

    /** @var string Scenario key or dataset preset. */
    public $scenario = '';

    /** @var float Primary target metric value. */
    public $targetparam1 = 0.0;

    /** @var float Secondary target metric value. */
    public $targetparam2 = 0.0;

    /** @var float Tolerance margin for numerical targets. */
    public $targettolerance = 0.0;

    /** @var string Initial simulation JSON configuration. */
    public $simconfig = '';

    /**
     * The response variable carries the JSON encoded simulation state.
     *
     * @return array
     */
    public function get_expected_data(): array {
        return ['answer' => PARAM_RAW];
    }

    /**
     * Decode a stored response string into an array.
     *
     * @param string $answer raw JSON string
     * @return array|null decoded data or null
     */
    public static function decode_answer(string $answer): ?array {
        if ($answer === '') {
            return null;
        }
        $data = json_decode($answer, true);
        if (!is_array($data) || empty($data['simtype'])) {
            return null;
        }
        return $data;
    }

    /**
     * Check whether the response is complete enough to be graded.
     *
     * @param array $response the response data
     * @return bool
     */
    public function is_complete_response(array $response): bool {
        if (!isset($response['answer']) || $response['answer'] === '') {
            return false;
        }
        $data = self::decode_answer($response['answer']);
        return ($data !== null && isset($data['metrics']));
    }

    /**
     * In situations where is_gradable_response() returns false, generate a validation message.
     *
     * @param array $response the student response
     * @return string
     */
    public function get_validation_error(array $response): string {
        if ($this->is_complete_response($response)) {
            return '';
        }
        return get_string('pleaseinteractwithsim', 'qtype_aisim');
    }

    /**
     * Check whether two responses are identical.
     *
     * @param array $prevresponse previous response
     * @param array $newresponse new response
     * @return bool
     */
    public function is_same_response(array $prevresponse, array $newresponse): bool {
        $prev = $prevresponse['answer'] ?? '';
        $new = $newresponse['answer'] ?? '';
        return ($prev === $new);
    }

    /**
     * Grade the student response against the question's target criteria.
     *
     * @param array $response student response
     * @return array [float fraction between 0.0 and 1.0, question_state state]
     */
    public function grade_response(array $response): array {
        if (!$this->is_gradable_response($response)) {
            return [0.0, question_state::$gradedwrong];
        }

        $data = self::decode_answer($response['answer']);
        if (!$data || !isset($data['metrics'])) {
            return [0.0, question_state::$gradedwrong];
        }

        $metrics = $data['metrics'];
        $params = $data['parameters'] ?? [];
        $fraction = 0.0;
        $simtype = $this->simtype;

        switch ($simtype) {
            case 'confusion_matrix':
                $rec = (float)($metrics['recall'] ?? 0.0);
                $prec = (float)($metrics['precision'] ?? 0.0);
                $targetRec = (float)$this->targetparam1;
                $targetPrec = (float)$this->targetparam2;

                $recOk = ($rec >= $targetRec);
                $precOk = ($prec >= $targetPrec);

                if ($recOk && $precOk) {
                    $fraction = 1.0;
                } else if ($recOk || $precOk) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'gradient_descent':
                $loss = (float)($metrics['loss'] ?? 999.0);
                $targetLoss = (float)$this->targetparam1;
                $tol = ($this->targettolerance > 0) ? (float)$this->targettolerance : 0.02;

                if ($loss <= $targetLoss) {
                    $fraction = 1.0;
                } else if ($loss <= ($targetLoss + $tol)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'regression':
                $valMse = (float)($metrics['valMse'] ?? 999.0);
                $degree = (int)($params['degree'] ?? 1);
                $targetMse = (float)$this->targetparam1;
                $maxDegree = (int)$this->targetparam2;

                $mseOk = ($valMse <= $targetMse);
                $degreeOk = ($maxDegree <= 0 || $degree <= $maxDegree);

                if ($mseOk && $degreeOk) {
                    $fraction = 1.0;
                } else if ($mseOk || $degreeOk) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'knn_tree':
                $testAcc = (float)($metrics['testAcc'] ?? 0.0);
                $targetAcc = (float)$this->targetparam1;

                if ($testAcc >= $targetAcc) {
                    $fraction = 1.0;
                } else if ($testAcc >= ($targetAcc - 0.05)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'kmeans':
                $wcss = (float)($metrics['wcss'] ?? 999999.0);
                $k = (int)($params['k'] ?? 0);
                $converged = !empty($metrics['converged']);
                $targetWcss = (float)$this->targetparam1;
                $targetK = (int)$this->targetparam2;

                $kOk = ($targetK <= 0 || $k === $targetK);
                $wcssOk = ($wcss <= $targetWcss);

                if ($converged && $wcssOk && $kOk) {
                    $fraction = 1.0;
                } else if ($converged && ($wcssOk || $kOk)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            default:
                $fraction = 0.0;
                break;
        }

        return [$fraction, question_state::graded_state_for_fraction($fraction)];
    }

    /**
     * Provide a plain-text summary of the student response.
     *
     * @param array $response the response
     * @return string
     */
    public function summarise_response(array $response): string {
        $answer = $response['answer'] ?? '';
        $data = self::decode_answer($answer);
        if (!$data || !isset($data['metrics'])) {
            return get_string('noresponse', 'qtype_aisim');
        }

        $m = $data['metrics'];
        $p = $data['parameters'] ?? [];

        switch ($this->simtype) {
            case 'confusion_matrix':
                $th = isset($p['threshold']) ? sprintf('%.2f', $p['threshold']) : '?';
                $rec = isset($m['recall']) ? round($m['recall'] * 100) . '%' : '?';
                $prec = isset($m['precision']) ? round($m['precision'] * 100) . '%' : '?';
                $f1 = isset($m['f1']) ? round($m['f1'] * 100) . '%' : '?';
                return "θ = {$th} (Recall: {$rec}, Precision: {$prec}, F1: {$f1})";

            case 'gradient_descent':
                $loss = isset($m['loss']) ? sprintf('%.4f', $m['loss']) : '?';
                $w = isset($p['w']) ? sprintf('%.3f', $p['w']) : '?';
                return "Loss J(w) = {$loss}, w = {$w}";

            case 'regression':
                $deg = $p['degree'] ?? '?';
                $val = isset($m['valMse']) ? sprintf('%.4f', $m['valMse']) : '?';
                return "Degree d = {$deg}, Val MSE = {$val}";

            case 'knn_tree':
                $model = $p['model'] ?? 'knn';
                $acc = isset($m['testAcc']) ? round($m['testAcc'] * 100) . '%' : '?';
                return "Model: {$model}, Test Acc: {$acc}";

            case 'kmeans':
                $k = $p['k'] ?? '?';
                $wcss = isset($m['wcss']) ? round($m['wcss']) : '?';
                $conv = !empty($m['converged']) ? '✓' : '✗';
                return "K = {$k}, WCSS = {$wcss}, Converged: {$conv}";

            default:
                return json_encode($m);
        }
    }

    /**
     * Provide a plain-text summary of the correct target requirements.
     *
     * @return string
     */
    public function get_right_answer_summary(): string {
        switch ($this->simtype) {
            case 'confusion_matrix':
                $rec = round($this->targetparam1 * 100);
                $prec = round($this->targetparam2 * 100);
                return get_string('rightsummary_cm', 'qtype_aisim', (object)['rec' => $rec, 'prec' => $prec]);

            case 'gradient_descent':
                $loss = sprintf('%.4f', $this->targetparam1);
                return get_string('rightsummary_gd', 'qtype_aisim', (object)['loss' => $loss]);

            case 'regression':
                $mse = sprintf('%.4f', $this->targetparam1);
                $deg = (int)$this->targetparam2;
                return get_string('rightsummary_reg', 'qtype_aisim', (object)['mse' => $mse, 'degree' => $deg]);

            case 'knn_tree':
                $acc = round($this->targetparam1 * 100);
                return get_string('rightsummary_knn', 'qtype_aisim', (object)['acc' => $acc]);

            case 'kmeans':
                $wcss = round($this->targetparam1);
                $k = (int)$this->targetparam2;
                return get_string('rightsummary_km', 'qtype_aisim', (object)['wcss' => $wcss, 'k' => $k]);

            default:
                return '';
        }
    }

    /**
     * Return the correct response array if applicable.
     *
     * @return array
     */
    public function get_correct_response(): array {
        return [];
    }

    /**
     * Check a request for access to a file belonging to a combined feedback field.
     *
     * @param question_attempt $qa the question attempt being displayed
     * @param question_display_options $options options controlling display
     * @param string $component component name
     * @param string $filearea name of the file area
     * @param array $args remaining path arguments
     * @param bool $forcedownload whether or not force download is requested
     * @return bool
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component === 'question' && in_array($filearea,
                ['correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'])) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }
}

