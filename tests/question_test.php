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

namespace qtype_aisim;

use qtype_aisim_question;
use question_state;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the AI Simulation question definition: grading, summaries and response handling.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_aisim_question
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_aisim_question::class)]
final class question_test extends \advanced_testcase {
    /**
     * A question of the given simulation type with the given targets.
     *
     * @param string $simtype
     * @param float $target1
     * @param float $target2
     * @param float $tolerance
     * @return qtype_aisim_question
     */
    private function make_question(string $simtype, float $target1, float $target2, float $tolerance = 0.0): qtype_aisim_question {
        $q = test_question_maker::make_question('aisim', 'nn');
        $q->simtype = $simtype;
        $q->targetparam1 = $target1;
        $q->targetparam2 = $target2;
        $q->targettolerance = $tolerance;
        return $q;
    }

    /**
     * A response as the simulation sends it.
     *
     * @param string $simtype
     * @param array $parameters
     * @param array $metrics
     * @return array
     */
    private function response(string $simtype, array $parameters, array $metrics): array {
        return ['answer' => json_encode(['simtype' => $simtype, 'parameters' => $parameters, 'metrics' => $metrics])];
    }

    /**
     * Measured responses of every simulation type and the fraction they must get.
     *
     * @return array
     */
    public static function grading_provider(): array {
        return [
            'confusion matrix, both targets' => ['confusion_matrix', 0.9, 0.7, 0, [], ['recall' => 0.95, 'precision' => 0.8], 1.0],
            'confusion matrix, recall only' => ['confusion_matrix', 0.9, 0.7, 0, [], ['recall' => 0.98, 'precision' => 0.55], 0.5],
            'confusion matrix, neither' => ['confusion_matrix', 0.9, 0.7, 0, [], ['recall' => 0.6, 'precision' => 0.6], 0.0],
            'gradient descent, at the minimum' => ['gradient_descent', 1.01, 0, 0.05, [], ['loss' => 1.0], 1.0],
            'gradient descent, within tolerance' => ['gradient_descent', 1.01, 0, 0.05, [], ['loss' => 1.04], 0.5],
            'gradient descent, local minimum' => ['gradient_descent', 1.01, 0, 0.05, [], ['loss' => 2.3], 0.0],
            'regression, good fit' => ['regression', 0.19, 4, 0, ['degree' => 2], ['valMse' => 0.13], 1.0],
            'regression, degree too high' => ['regression', 0.19, 4, 0, ['degree' => 9], ['valMse' => 0.13], 0.5],
            'regression, overfit' => ['regression', 0.19, 4, 0, ['degree' => 9], ['valMse' => 0.5], 0.0],
            'regression, no degree limit' => ['regression', 0.19, 0, 0, ['degree' => 9], ['valMse' => 0.13], 1.0],
            'k-NN, target met' => ['knn_tree', 0.95, 0, 0, [], ['testAcc' => 1.0], 1.0],
            'k-NN, close' => ['knn_tree', 0.95, 0, 0, [], ['testAcc' => 0.92], 0.5],
            'k-NN, far' => ['knn_tree', 0.95, 0, 0, [], ['testAcc' => 0.8], 0.0],
            'k-means, right k' => ['kmeans', 10, 3, 0, ['k' => 3], ['wcss' => 8.7, 'converged' => true], 1.0],
            'k-means, wrong k' => ['kmeans', 10, 3, 0, ['k' => 4], ['wcss' => 8.7, 'converged' => true], 0.5],
            'k-means, not converged' => ['kmeans', 10, 3, 0, ['k' => 3], ['wcss' => 8.7, 'converged' => false], 0.0],
            'neural network, small and accurate' => ['neural_network', 0.98, 4, 0, ['hidden' => 3], ['accuracy' => 1.0], 1.0],
            'neural network, too many neurons' => ['neural_network', 0.98, 4, 0, ['hidden' => 8], ['accuracy' => 1.0], 0.5],
            'neural network, perceptron on XOR' => ['neural_network', 0.98, 4, 0, ['hidden' => 0], ['accuracy' => 0.5], 0.5],
            'neural network, neither' => ['neural_network', 0.98, 4, 0, ['hidden' => 8], ['accuracy' => 0.75], 0.0],
            'convolution, exact filter' => ['convolution', 0.95, 0, 0, [], ['match' => 1.0], 1.0],
            'convolution, default tolerance' => ['convolution', 0.95, 0, 0, [], ['match' => 0.87], 0.5],
            'convolution, other filter' => ['convolution', 0.95, 0, 0, [], ['match' => 0.35], 0.0],
            'q-learning, shortest path in time' => ['q_learning', 4, 1000, 0, ['episodes' => 1000],
                ['reached' => true, 'steps' => 4], 1.0],
            'q-learning, longer path' => ['q_learning', 4, 1000, 0, ['episodes' => 500],
                ['reached' => true, 'steps' => 6], 0.5],
            'q-learning, too many episodes' => ['q_learning', 4, 1000, 0, ['episodes' => 1500],
                ['reached' => true, 'steps' => 4], 0.5],
            'q-learning, goal not reached' => ['q_learning', 4, 1000, 0, ['episodes' => 500],
                ['reached' => false, 'steps' => 0], 0.0],
            'data bias, fair and small' => ['data_bias', 0.88, 120, 0, ['n' => 120], ['worst' => 0.91], 1.0],
            'data bias, sample too big' => ['data_bias', 0.88, 120, 0, ['n' => 400], ['worst' => 0.91], 0.5],
            'data bias, biased sample' => ['data_bias', 0.88, 120, 0, ['n' => 120], ['worst' => 0.76], 0.0],
        ];
    }

    /**
     * Every simulation type grades its measured responses as intended.
     *
     * @dataProvider grading_provider
     * @param string $simtype
     * @param float $target1
     * @param float $target2
     * @param float $tolerance
     * @param array $parameters
     * @param array $metrics
     * @param float $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('grading_provider')]
    public function test_grade_response(
        string $simtype,
        float $target1,
        float $target2,
        float $tolerance,
        array $parameters,
        array $metrics,
        float $expected
    ): void {
        $q = $this->make_question($simtype, $target1, $target2, $tolerance);
        [$fraction, $state] = $q->grade_response($this->response($simtype, $parameters, $metrics));
        $this->assertEqualsWithDelta($expected, $fraction, 0.0001);
        $this->assertEquals(question_state::graded_state_for_fraction($expected), $state);
    }

    /**
     * Empty and malformed responses are incomplete and get no marks.
     */
    public function test_incomplete_responses(): void {
        $q = $this->make_question('neural_network', 0.98, 4);
        foreach ([[], ['answer' => ''], ['answer' => 'not json'], ['answer' => '{"parameters":{}}']] as $response) {
            $this->assertFalse($q->is_complete_response($response));
            $this->assertNotEmpty($q->get_validation_error($response));
            $this->assertEquals(0.0, $q->grade_response($response)[0]);
        }
        $complete = $this->response('neural_network', ['hidden' => 3], ['accuracy' => 1.0]);
        $this->assertTrue($q->is_complete_response($complete));
        $this->assertSame('', $q->get_validation_error($complete));
        $this->assertTrue($q->is_same_response($complete, $complete));
        $this->assertFalse($q->is_same_response($complete, ['answer' => '']));
    }

    /**
     * Response summaries show the parameters and results a teacher needs in the reports.
     */
    public function test_summarise_response(): void {
        $q = $this->make_question('confusion_matrix', 0.9, 0.7);
        $summary = $q->summarise_response($this->response(
            'confusion_matrix',
            ['threshold' => 0.5],
            ['recall' => 0.9, 'precision' => 0.8]
        ));
        $this->assertStringContainsString('F1: 85%', $summary);

        $q = $this->make_question('convolution', 0.95, 0);
        $summary = $q->summarise_response($this->response(
            'convolution',
            ['kernel' => [-1, 0, 1, -2, 0, 2, -1, 0, 1], 'divisor' => 1, 'abs' => true],
            ['match' => 1.0]
        ));
        $this->assertStringContainsString('[-1 0 1 -2 0 2 -1 0 1] / 1, |x|', $summary);
        $this->assertStringContainsString('Match: 100%', $summary);

        $q = $this->make_question('q_learning', 4, 1000);
        $summary = $q->summarise_response($this->response(
            'q_learning',
            ['alpha' => 0.5, 'gamma' => 0.9, 'epsilon' => 0.4, 'episodes' => 1000],
            ['reached' => true, 'steps' => 4]
        ));
        $this->assertStringContainsString('Episodes: 1000, Path: 4 steps', $summary);

        $q = $this->make_question('data_bias', 0.88, 120);
        $summary = $q->summarise_response($this->response(
            'data_bias',
            ['n' => 120, 'share' => 40, 'k' => 5],
            ['testAcc' => 0.9, 'accA' => 0.943, 'accB' => 0.91]
        ));
        $this->assertStringContainsString('Factory A: 94%, Factory B: 91%', $summary);
    }

    /**
     * The right answer summary states the targets, with or without the optional limit.
     */
    public function test_right_answer_summary(): void {
        $this->assertSame(
            'Accuracy ≥ 98% with at most 4 hidden neurons',
            $this->make_question('neural_network', 0.98, 4)->get_right_answer_summary()
        );
        $this->assertSame('Accuracy ≥ 98%', $this->make_question('neural_network', 0.98, 0)->get_right_answer_summary());
        $this->assertSame(
            'Match with the target image (R²) ≥ 95%',
            $this->make_question('convolution', 0.95, 0)->get_right_answer_summary()
        );
        $this->assertSame(
            'The learned policy reaches the goal in at most 4 steps',
            $this->make_question('q_learning', 4, 0)->get_right_answer_summary()
        );
        $this->assertSame(
            'Real-world accuracy of both factories ≥ 88% with at most 120 samples',
            $this->make_question('data_bias', 0.88, 120)->get_right_answer_summary()
        );
    }
}
