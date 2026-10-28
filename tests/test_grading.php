<?php
define('CLI_SCRIPT', true);

if (file_exists('/home/tsp/moodle45/config.php')) {
    require_once('/home/tsp/moodle45/config.php');
} else {
    require_once(dirname(__DIR__, 4) . '/config.php');
}
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/type/aisim/question.php');

global $CFG, $DB;

echo "=== AI Simulation Question Type (qtype_aisim) Automated Verification ===\n\n";

try {
    // 1. Verify plugin installation.
    $pluginman = core_plugin_manager::instance();
    $qtypes = $pluginman->get_plugins_of_type('qtype');
    if (!isset($qtypes['aisim'])) {
        die("ERROR: qtype_aisim is not registered in core_plugin_manager!\n");
    }
    echo "✓ qtype_aisim is registered and enabled (version: " . $qtypes['aisim']->versiondisk . ")\n";

    // 2. Verify DB table.
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('qtype_aisim_options')) {
        die("ERROR: Table mdl_qtype_aisim_options does not exist!\n");
    }
    echo "✓ DB table mdl_qtype_aisim_options exists\n";

    // 3. Test question definition and grading logic.
    $qtype = question_bank::get_qtype('aisim');

    function make_test_question($simtype, $scenario, $target1, $target2, $tol = 0.0) {
        global $qtype;
        $q = new qtype_aisim_question();
        $q->qtype = $qtype;
        $q->simtype = $simtype;
        $q->scenario = $scenario;
        $q->targetparam1 = (float)$target1;
        $q->targetparam2 = (float)$target2;
        $q->targettolerance = (float)$tol;
        return $q;
    }

    // Test Suite 1: Confusion Matrix
    echo "\nTesting Suite 1: Confusion Matrix & Classification Threshold...\n";
    $qCM = make_test_question('confusion_matrix', 'medical', 0.90, 0.70);

    $respFull = [
        'answer' => json_encode([
            'simtype' => 'confusion_matrix',
            'parameters' => ['scenario' => 'medical', 'threshold' => 0.35],
            'metrics' => ['recall' => 0.95, 'precision' => 0.80, 'f1' => 0.868, 'accuracy' => 0.88]
        ])
    ];
    list($fracFull, $stateFull) = $qCM->grade_response($respFull);
    echo "  ✓ Full credit response: fraction = $fracFull, state = " . $stateFull->get_feedback_class() . "\n";
    if ($fracFull !== 1.0) {
        throw new Exception("Expected fraction 1.0, got $fracFull");
    }

    $respPart = [
        'answer' => json_encode([
            'simtype' => 'confusion_matrix',
            'parameters' => ['scenario' => 'medical', 'threshold' => 0.20],
            'metrics' => ['recall' => 0.98, 'precision' => 0.55, 'f1' => 0.70, 'accuracy' => 0.75]
        ])
    ];
    list($fracPart, $statePart) = $qCM->grade_response($respPart);
    echo "  ✓ Partial credit response: fraction = $fracPart, state = " . $statePart->get_feedback_class() . "\n";
    if ($fracPart !== 0.5) {
        throw new Exception("Expected fraction 0.5, got $fracPart");
    }

    $respZero = [
        'answer' => json_encode([
            'simtype' => 'confusion_matrix',
            'parameters' => ['scenario' => 'medical', 'threshold' => 0.80],
            'metrics' => ['recall' => 0.60, 'precision' => 0.60, 'f1' => 0.60, 'accuracy' => 0.70]
        ])
    ];
    list($fracZero, $stateZero) = $qCM->grade_response($respZero);
    echo "  ✓ Zero credit response: fraction = $fracZero, state = " . $stateZero->get_feedback_class() . "\n";
    if ($fracZero !== 0.0) {
        throw new Exception("Expected fraction 0.0, got $fracZero");
    }

    // Test Suite 2: Gradient Descent
    echo "\nTesting Suite 2: Gradient Descent Optimization...\n";
    $qGD = make_test_question('gradient_descent', 'convex', 0.05, 0.0, 0.02);

    $respGD1 = [
        'answer' => json_encode([
            'simtype' => 'gradient_descent',
            'parameters' => ['w' => 1.98, 'learningRate' => 0.08, 'steps' => 25],
            'metrics' => ['loss' => 0.012, 'grad' => 0.003]
        ])
    ];
    list($fracGD1) = $qGD->grade_response($respGD1);
    echo "  ✓ Converged below target loss: fraction = $fracGD1\n";
    if ($fracGD1 !== 1.0) {
        throw new Exception("Expected fraction 1.0, got $fracGD1");
    }

    $respGD2 = [
        'answer' => json_encode([
            'simtype' => 'gradient_descent',
            'parameters' => ['w' => 1.85, 'learningRate' => 0.02, 'steps' => 10],
            'metrics' => ['loss' => 0.065, 'grad' => 0.04]
        ])
    ];
    list($fracGD2) = $qGD->grade_response($respGD2);
    echo "  ✓ Loss within tolerance margin: fraction = $fracGD2\n";
    if ($fracGD2 !== 0.5) {
        throw new Exception("Expected fraction 0.5, got $fracGD2");
    }

    // Test Suite 3: Polynomial Regression
    echo "\nTesting Suite 3: Polynomial Regression & Regularization...\n";
    $qReg = make_test_question('regression', 'sine', 0.15, 5);

    $respReg1 = [
        'answer' => json_encode([
            'simtype' => 'regression',
            'parameters' => ['degree' => 3, 'lambda' => 0.01, 'dataset' => 'sine'],
            'metrics' => ['trainMse' => 0.042, 'valMse' => 0.085]
        ])
    ];
    list($fracReg1) = $qReg->grade_response($respReg1);
    echo "  ✓ Good fit (d=3, MSE=0.085): fraction = $fracReg1\n";
    if ($fracReg1 !== 1.0) {
        throw new Exception("Expected fraction 1.0, got $fracReg1");
    }

    $respRegOverfit = [
        'answer' => json_encode([
            'simtype' => 'regression',
            'parameters' => ['degree' => 8, 'lambda' => 0.0, 'dataset' => 'sine'],
            'metrics' => ['trainMse' => 0.002, 'valMse' => 0.950]
        ])
    ];
    list($fracRegOverfit) = $qReg->grade_response($respRegOverfit);
    echo "  ✓ Overfitted model rejected: fraction = $fracRegOverfit\n";
    if ($fracRegOverfit !== 0.0) {
        throw new Exception("Expected fraction 0.0, got $fracRegOverfit");
    }

    // Test Suite 4: Decision Boundaries (k-NN)
    echo "\nTesting Suite 4: Decision Boundaries (k-NN)...\n";
    $qKNN = make_test_question('knn_tree', 'moons', 0.85, 0.0);
    $respKNN = [
        'answer' => json_encode([
            'simtype' => 'knn_tree',
            'parameters' => ['model' => 'knn', 'k' => 5, 'depth' => 3, 'dataset' => 'moons'],
            'metrics' => ['testAcc' => 0.916]
        ])
    ];
    list($fracKNN) = $qKNN->grade_response($respKNN);
    echo "  ✓ Smooth k-NN boundary achieves 91.6% accuracy: fraction = $fracKNN\n";
    if ($fracKNN !== 1.0) {
        throw new Exception("Expected fraction 1.0, got $fracKNN");
    }

    // Test Suite 5: K-Means Clustering
    echo "\nTesting Suite 5: K-Means Clustering...\n";
    $qKM = make_test_question('kmeans', 'blobs3', 350.0, 3);
    $respKM = [
        'answer' => json_encode([
            'simtype' => 'kmeans',
            'parameters' => ['k' => 3, 'dataset' => 'blobs3', 'iterations' => 6],
            'metrics' => ['wcss' => 245.8, 'converged' => true]
        ])
    ];
    list($fracKM) = $qKM->grade_response($respKM);
    echo "  ✓ Converged K=3 clusters with WCSS=245.8: fraction = $fracKM\n";
    if ($fracKM !== 1.0) {
        throw new Exception("Expected fraction 1.0, got $fracKM");
    }

    echo "\n======================================================\n";
    echo "ALL 5 SIMULATION GRADING TESTS PASSED WITH 100% ACCURACY!\n";
    echo "======================================================\n";

} catch (Throwable $e) {
    echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
