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
 * Strings for component 'qtype_aisim', language 'en'.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['correctansweris'] = 'The target criteria was: {$a}';
$string['goalfailed'] = '✗ Target Not Met (0%)';
$string['goalpartial'] = '⚠ Partially Achieved (50%)';
$string['goalsatisfied'] = '✓ Target Achieved (100%)';
$string['invalidjson'] = 'Invalid JSON format in simulation configuration.';
$string['noresponse'] = 'No simulation configuration submitted.';
$string['pleaseinteractwithsim'] = 'Please interact with the simulation to configure the model before submitting.';
$string['pluginname'] = 'AI Simulation';
$string['pluginname_help'] = 'Students solve practical machine learning tasks interactively in real-time simulations. Responses are graded automatically based on model performance metrics.';
$string['pluginname_link'] = 'question/type/aisim';
$string['pluginnameadding'] = 'Adding an AI Simulation question';
$string['pluginnameediting'] = 'Editing an AI Simulation question';
$string['pluginnamesummary'] = 'An interactive question type where students tune AI/ML parameters in simulation engines, automatically graded against specified targets.';
$string['privacy:metadata'] = 'The AI Simulation question type does not store any personal data.';
$string['rightsummary_cm'] = 'Recall ≥ {$a->rec}% and Precision ≥ {$a->prec}%';
$string['rightsummary_gd'] = 'Loss J(w) ≤ {$a->loss}';
$string['rightsummary_km'] = 'WCSS ≤ {$a->wcss} with K = {$a->k}';
$string['rightsummary_knn'] = 'Test Accuracy ≥ {$a->acc}%';
$string['rightsummary_nn'] = 'Accuracy ≥ {$a->acc}% with at most {$a->hidden} hidden neurons';
$string['rightsummary_nn_any'] = 'Accuracy ≥ {$a->acc}%';
$string['rightsummary_reg'] = 'Validation MSE ≤ {$a->mse} with polynomial degree ≤ {$a->degree}';
$string['scenario'] = 'Scenario / Preset';
$string['scenario_help'] = 'Domain scenario (e.g. "quality" for industrial quality control, "medical" for screening, or dataset key like "sine", "moons", "blobs3"; for the neural network "linear", "xor" or "circles").';
$string['sim_cm'] = 'Confusion Matrix & Classification Threshold';
$string['sim_gd'] = 'Gradient Descent Optimization';
$string['sim_km'] = 'K-Means Clustering';
$string['sim_knn'] = 'Decision Boundaries (k-NN & Decision Tree)';
$string['sim_nn'] = 'Perceptron & Neural Network';
$string['sim_reg'] = 'Polynomial Regression & Regularization';
$string['simconfig'] = 'Advanced Simulation Configuration (JSON)';
$string['simconfig_help'] = 'Optional JSON object to pass custom initial values or constraints to the simulation engine.';
$string['simtype'] = 'Simulation Type';
$string['simtype_help'] = 'Select the machine learning engine: Confusion Matrix & Threshold, Gradient Descent Optimization, Polynomial Regression, Decision Boundaries, K-Means Clustering, or Perceptron & Neural Network.';
$string['simulationsettings'] = 'AI Simulation Settings';
$string['targetparam1'] = 'Target Metric 1 (Primary Goal)';
$string['targetparam1_help'] = 'Main target, depending on the simulation. Confusion matrix: minimum recall (e.g. 0.80). Gradient descent: maximum cost J(w); the lowest possible cost is 1.00 (convex), 0.98 (non-convex) and 1.82 (plateau), so for example 1.01. Regression: maximum validation MSE (reachable values are about 0.12–0.20, e.g. 0.19). k-NN / decision tree: minimum accuracy (e.g. 0.95). K-Means: maximum WCSS (about 7–25 depending on the dataset, e.g. 10). Neural network: minimum accuracy (e.g. 0.98).';
$string['targetparam2'] = 'Target Metric 2 (Secondary Goal)';
$string['targetparam2_help'] = 'Second target. Confusion matrix: minimum precision (e.g. 0.70). Regression: maximum polynomial degree (0 = no limit). K-Means: required K (0 = any). Neural network: maximum number of hidden neurons (0 = no limit; XOR needs at least 2, a circle about 3–4). Not used for gradient descent and k-NN / decision tree. Full marks need both targets; meeting only one gives half marks.';
$string['targettolerance'] = 'Acceptable Tolerance';
$string['targettolerance_help'] = 'Gradient descent only: a cost up to target + tolerance gives half marks (e.g. 0.05). For k-NN / decision tree, half marks are given for an accuracy up to 0.05 below the target.';
$string['taskgoal'] = 'Task Objective';
