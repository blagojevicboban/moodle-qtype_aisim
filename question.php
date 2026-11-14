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
    /** @var string Simulation type (e.g. confusion_matrix, gradient_descent, neural_network, convolution, q_learning). */
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

    /** @var string Feedback for a fully correct response. */
    public $correctfeedback;

    /** @var int Format of the feedback for a fully correct response. */
    public $correctfeedbackformat;

    /** @var string Feedback for a partially correct response. */
    public $partiallycorrectfeedback;

    /** @var int Format of the feedback for a partially correct response. */
    public $partiallycorrectfeedbackformat;

    /** @var string Feedback for an incorrect response. */
    public $incorrectfeedback;

    /** @var int Format of the feedback for an incorrect response. */
    public $incorrectfeedbackformat;

    /** @var bool Whether to show the number of correct parts (part of the standard combined feedback). */
    public $shownumcorrect;

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
                $targetrec = (float)$this->targetparam1;
                $targetprec = (float)$this->targetparam2;

                $recok = ($rec >= $targetrec);
                $precok = ($prec >= $targetprec);

                if ($recok && $precok) {
                    $fraction = 1.0;
                } else if ($recok || $precok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'gradient_descent':
                $loss = (float)($metrics['loss'] ?? 999.0);
                $targetloss = (float)$this->targetparam1;
                $tol = ($this->targettolerance > 0) ? (float)$this->targettolerance : 0.02;

                if ($loss <= $targetloss) {
                    $fraction = 1.0;
                } else if ($loss <= ($targetloss + $tol)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'regression':
                $valmse = (float)($metrics['valMse'] ?? 999.0);
                $degree = (int)($params['degree'] ?? 1);
                $targetmse = (float)$this->targetparam1;
                $maxdegree = (int)$this->targetparam2;

                $mseok = ($valmse <= $targetmse);
                $degreeok = ($maxdegree <= 0 || $degree <= $maxdegree);

                if ($mseok && $degreeok) {
                    $fraction = 1.0;
                } else if ($mseok || $degreeok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'knn_tree':
                $testacc = (float)($metrics['testAcc'] ?? 0.0);
                $targetacc = (float)$this->targetparam1;

                if ($testacc >= $targetacc) {
                    $fraction = 1.0;
                } else if ($testacc >= ($targetacc - 0.05)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'neural_network':
                $acc = (float)($metrics['accuracy'] ?? 0.0);
                $hidden = (int)($params['hidden'] ?? 0);
                $maxhidden = (int)$this->targetparam2;

                $accok = ($acc >= (float)$this->targetparam1);
                $hiddenok = ($maxhidden <= 0 || $hidden <= $maxhidden);

                if ($accok && $hiddenok) {
                    $fraction = 1.0;
                } else if ($accok || $hiddenok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'convolution':
                $match = (float)($metrics['match'] ?? 0.0);
                $targetmatch = (float)$this->targetparam1;
                $tol = ($this->targettolerance > 0) ? (float)$this->targettolerance : 0.10;

                if ($match >= $targetmatch) {
                    $fraction = 1.0;
                } else if ($match >= ($targetmatch - $tol)) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'q_learning':
                // Full marks: the learned policy reaches the goal within the step limit, after at most the allowed episodes.
                $reached = !empty($metrics['reached']);
                $steps = (int)($metrics['steps'] ?? 0);
                $episodes = (int)($params['episodes'] ?? 0);
                $maxsteps = (int)$this->targetparam1;
                $maxepisodes = (int)$this->targetparam2;

                $stepsok = $reached && ($maxsteps <= 0 || $steps <= $maxsteps);
                $episodesok = ($maxepisodes <= 0 || $episodes <= $maxepisodes);

                if ($stepsok && $episodesok) {
                    $fraction = 1.0;
                } else if ($reached) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'data_bias':
                // The weakest factory decides: a model is only as good as it is for the group it serves worst.
                $worst = (float)($metrics['worst'] ?? 0.0);
                $n = (int)($params['n'] ?? 0);
                $maxn = (int)$this->targetparam2;

                $worstok = ($worst >= (float)$this->targetparam1);
                $nok = ($maxn <= 0 || $n <= $maxn);

                if ($worstok && $nok) {
                    $fraction = 1.0;
                } else if ($worstok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'logistic':
                // Accuracy is the main target; recall (targetparam2) and precision (tolerance) are optional limits.
                $accok = ((float)($metrics['accuracy'] ?? 0.0) >= (float)$this->targetparam1);
                $recok = ((float)$this->targetparam2 <= 0 || (float)($metrics['recall'] ?? 0.0) >= (float)$this->targetparam2);
                $precok = ((float)$this->targettolerance <= 0
                    || (float)($metrics['precision'] ?? 0.0) >= (float)$this->targettolerance);

                if ($accok && $recok && $precok) {
                    $fraction = 1.0;
                } else if ($accok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'cross_validation':
                // The validation error of the chosen degree, estimated with at least the required number of folds.
                $mseok = ((float)($metrics['cvMse'] ?? 999.0) <= (float)$this->targetparam1);
                $minfolds = (int)$this->targetparam2;
                $foldsok = ($minfolds <= 0 || (int)($params['k'] ?? 0) >= $minfolds);

                if ($mseok && $foldsok) {
                    $fraction = 1.0;
                } else if ($mseok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'data_prep':
                // Accuracy on the clean test data, with an optional minimum recall of class 1.
                $accok = ((float)($metrics['accuracy'] ?? 0.0) >= (float)$this->targetparam1);
                $recok = ((float)$this->targetparam2 <= 0 || (float)($metrics['recall'] ?? 0.0) >= (float)$this->targetparam2);

                if ($accok && $recok) {
                    $fraction = 1.0;
                } else if ($accok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'rnn':
                // The loss of the network, with an optional minimum number of past inputs it still remembers.
                $lossok = ((float)($metrics['loss'] ?? 999.0) <= (float)$this->targetparam1);
                $minsteps = (int)$this->targetparam2;
                $memok = ($minsteps <= 0 || (int)($metrics['remembered'] ?? 0) >= $minsteps);

                if ($lossok && $memok) {
                    $fraction = 1.0;
                } else if ($lossok) {
                    $fraction = 0.5;
                } else {
                    $fraction = 0.0;
                }
                break;

            case 'kmeans':
                $wcss = (float)($metrics['wcss'] ?? 999999.0);
                $k = (int)($params['k'] ?? 0);
                $converged = !empty($metrics['converged']);
                $targetwcss = (float)$this->targetparam1;
                $targetk = (int)$this->targetparam2;

                $kok = ($targetk <= 0 || $k === $targetk);
                $wcssok = ($wcss <= $targetwcss);

                if ($converged && $wcssok && $kok) {
                    $fraction = 1.0;
                } else if ($converged && ($wcssok || $kok)) {
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
                $f1 = '?';
                if (isset($m['recall'], $m['precision']) && ($m['recall'] + $m['precision']) > 0) {
                    $f1 = round(200 * $m['recall'] * $m['precision'] / ($m['recall'] + $m['precision'])) . '%';
                }
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

            case 'neural_network':
                $hidden = $p['hidden'] ?? '?';
                $acc = isset($m['accuracy']) ? round($m['accuracy'] * 100) . '%' : '?';
                $epochs = $p['epochs'] ?? '?';
                return "Hidden neurons: {$hidden}, Accuracy: {$acc}, Epochs: {$epochs}";

            case 'convolution':
                $kernel = '?';
                if (isset($p['kernel']) && is_array($p['kernel'])) {
                    $kernel = implode(' ', array_map('intval', $p['kernel']));
                }
                $divisor = $p['divisor'] ?? '?';
                $abs = !empty($p['abs']) ? ', |x|' : '';
                $match = isset($m['match']) ? round($m['match'] * 100) . '%' : '?';
                return "Kernel: [{$kernel}] / {$divisor}{$abs}, Match: {$match}";

            case 'q_learning':
                $episodes = $p['episodes'] ?? '?';
                $path = !empty($m['reached']) ? ($m['steps'] ?? '?') . ' steps' : 'goal not reached';
                $alpha = $p['alpha'] ?? '?';
                $gamma = $p['gamma'] ?? '?';
                $epsilon = $p['epsilon'] ?? '?';
                return "α = {$alpha}, γ = {$gamma}, ε = {$epsilon}, Episodes: {$episodes}, Path: {$path}";

            case 'logistic':
                $pct = fn($key) => isset($m[$key]) ? round($m[$key] * 100) . '%' : '?';
                $num = fn($key) => isset($p[$key]) ? sprintf('%.2f', $p[$key]) : '?';
                [$w1, $w2, $b, $th] = [$num('w1'), $num('w2'), $num('b'), $num('threshold')];
                [$acc, $prec, $rec] = [$pct('accuracy'), $pct('precision'), $pct('recall')];
                return "w1 = {$w1}, w2 = {$w2}, b = {$b}, θ = {$th}, Accuracy: {$acc}, Precision: {$prec}, Recall: {$rec}";

            case 'cross_validation':
                $num = fn($key) => isset($m[$key]) ? sprintf('%.3f', $m[$key]) : '?';
                $k = $p['k'] ?? '?';
                $d = $p['degree'] ?? '?';
                $best = $m['bestDegree'] ?? '?';
                $test = isset($m['testMse']) ? sprintf('%.3f', $m['testMse']) : '–';
                return "k = {$k}, d = {$d}, CV MSE: {$num('cvMse')} ± {$num('cvSd')}, Best d by CV: {$best}, Test MSE: {$test}";

            case 'data_prep':
                $pct = fn($key) => isset($m[$key]) ? round($m[$key] * 100) . '%' : '?';
                $steps = implode(', ', array_map(fn($k) => $p[$k] ?? '?', ['missing', 'outliers', 'scaling']));
                $k = $p['k'] ?? '?';
                return "Pipeline: {$steps}, k = {$k}, Accuracy: {$pct('accuracy')}, Recall: {$pct('recall')}";

            case 'rnn':
                $num = fn($key) => isset($p[$key]) ? sprintf('%.2f', $p[$key]) : '?';
                [$w, $u, $v] = [$num('w'), $num('u'), $num('v')];
                $length = isset($p['length']) ? ", L = {$p['length']}" : '';
                $loss = isset($m['loss']) ? sprintf('%.4f', $m['loss']) : '?';
                $remembered = $m['remembered'] ?? '?';
                return "w = {$w}, u = {$u}, v = {$v}{$length}, Loss: {$loss}, Remembers: {$remembered} inputs";

            case 'data_bias':
                $pct = fn($key) => isset($m[$key]) ? round($m[$key] * 100) . '%' : '?';
                $n = $p['n'] ?? '?';
                $share = $p['share'] ?? '?';
                $k = $p['k'] ?? '?';
                [$test, $acca, $accb] = [$pct('testAcc'), $pct('accA'), $pct('accB')];
                return "n = {$n}, B share = {$share}%, k = {$k}, Test: {$test}, Factory A: {$acca}, Factory B: {$accb}";

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

            case 'neural_network':
                $acc = round($this->targetparam1 * 100);
                $hidden = (int)$this->targetparam2;
                $key = $hidden > 0 ? 'rightsummary_nn' : 'rightsummary_nn_any';
                return get_string($key, 'qtype_aisim', (object)['acc' => $acc, 'hidden' => $hidden]);

            case 'convolution':
                $match = round($this->targetparam1 * 100);
                return get_string('rightsummary_conv', 'qtype_aisim', (object)['match' => $match]);

            case 'q_learning':
                $steps = (int)$this->targetparam1;
                $episodes = (int)$this->targetparam2;
                $key = $episodes > 0 ? 'rightsummary_ql' : 'rightsummary_ql_any';
                return get_string($key, 'qtype_aisim', (object)['steps' => $steps, 'episodes' => $episodes]);

            case 'logistic':
                $parts = [get_string('rightsummary_log', 'qtype_aisim', round($this->targetparam1 * 100))];
                if ($this->targetparam2 > 0) {
                    $parts[] = get_string('rightsummary_log_recall', 'qtype_aisim', round($this->targetparam2 * 100));
                }
                if ($this->targettolerance > 0) {
                    $parts[] = get_string('rightsummary_log_precision', 'qtype_aisim', round($this->targettolerance * 100));
                }
                return implode(', ', $parts);

            case 'data_prep':
                $parts = [get_string('rightsummary_log', 'qtype_aisim', round($this->targetparam1 * 100))];
                if ($this->targetparam2 > 0) {
                    $parts[] = get_string('rightsummary_log_recall', 'qtype_aisim', round($this->targetparam2 * 100));
                }
                return implode(', ', $parts);

            case 'rnn':
                $loss = sprintf('%.3f', $this->targetparam1);
                $steps = (int)$this->targetparam2;
                $key = $steps > 0 ? 'rightsummary_rnn' : 'rightsummary_rnn_any';
                return get_string($key, 'qtype_aisim', (object)['loss' => $loss, 'steps' => $steps]);

            case 'cross_validation':
                $mse = sprintf('%.3f', $this->targetparam1);
                $k = (int)$this->targetparam2;
                $key = $k > 0 ? 'rightsummary_cv' : 'rightsummary_cv_any';
                return get_string($key, 'qtype_aisim', (object)['mse' => $mse, 'k' => $k]);

            case 'data_bias':
                $acc = round($this->targetparam1 * 100);
                $n = (int)$this->targetparam2;
                $key = $n > 0 ? 'rightsummary_db' : 'rightsummary_db_any';
                return get_string($key, 'qtype_aisim', (object)['acc' => $acc, 'n' => $n]);

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
        if (
            $component === 'question' && in_array(
                $filearea,
                ['correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback']
            )
        ) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }
}
