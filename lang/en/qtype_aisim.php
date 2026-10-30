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
$string['rightsummary_conv'] = 'Match with the target image (R²) ≥ {$a->match}%';
$string['rightsummary_db'] = 'Real-world accuracy of both factories ≥ {$a->acc}% with at most {$a->n} samples';
$string['rightsummary_db_any'] = 'Real-world accuracy of both factories ≥ {$a->acc}%';
$string['rightsummary_gd'] = 'Loss J(w) ≤ {$a->loss}';
$string['rightsummary_km'] = 'WCSS ≤ {$a->wcss} with K = {$a->k}';
$string['rightsummary_knn'] = 'Test Accuracy ≥ {$a->acc}%';
$string['rightsummary_nn'] = 'Accuracy ≥ {$a->acc}% with at most {$a->hidden} hidden neurons';
$string['rightsummary_nn_any'] = 'Accuracy ≥ {$a->acc}%';
$string['rightsummary_ql'] = 'The learned policy reaches the goal in at most {$a->steps} steps, after at most {$a->episodes} training episodes';
$string['rightsummary_ql_any'] = 'The learned policy reaches the goal in at most {$a->steps} steps';
$string['rightsummary_reg'] = 'Validation MSE ≤ {$a->mse} with polynomial degree ≤ {$a->degree}';
$string['scenario'] = 'Scenario / Preset';
$string['scenario_help'] = 'Domain scenario (e.g. "quality" for industrial quality control, "medical" for screening, or dataset key like "sine", "moons", "blobs3"; for the neural network "linear", "xor" or "circles"; for the convolution the image "shapes", "stripes", "face" or "noisy"; for Q-learning the map "maze", "cliff" or "treasure"; for data split & bias "shifted" or "similar").';
$string['sim_cm'] = 'Confusion Matrix & Classification Threshold';
$string['sim_conv'] = 'Convolution & Image Filters';
$string['sim_db'] = 'Data Split & Bias';
$string['sim_gd'] = 'Gradient Descent Optimization';
$string['sim_km'] = 'K-Means Clustering';
$string['sim_knn'] = 'Decision Boundaries (k-NN & Decision Tree)';
$string['sim_nn'] = 'Perceptron & Neural Network';
$string['sim_ql'] = 'Reinforcement Learning (Q-learning)';
$string['sim_reg'] = 'Polynomial Regression & Regularization';
$string['simconfig'] = 'Advanced Simulation Configuration (JSON)';
$string['simconfig_help'] = 'Optional JSON object to pass custom initial values or constraints to the simulation engine. For the convolution the target filter is set here: {"target": "sobelx"} (also "box", "gaussian", "sharpen", "sobely", "laplacian", "emboss"); add "presets": false to hide the ready-made filters, so the student has to type the kernel.';
$string['simtype'] = 'Simulation Type';
$string['simtype_help'] = 'Select the machine learning engine: Confusion Matrix & Threshold, Gradient Descent Optimization, Polynomial Regression, Decision Boundaries, K-Means Clustering, Perceptron & Neural Network, Convolution & Image Filters, Reinforcement Learning (Q-learning), or Data Split & Bias.';
$string['simulationsettings'] = 'AI Simulation Settings';
$string['targetparam1'] = 'Target Metric 1 (Primary Goal)';
$string['targetparam1_help'] = 'Main target, depending on the simulation. Confusion matrix: minimum recall (e.g. 0.80). Gradient descent: maximum cost J(w); the lowest possible cost is 1.00 (convex), 0.98 (non-convex) and 1.82 (plateau), so for example 1.01. Regression: maximum validation MSE (reachable values are about 0.12–0.20, e.g. 0.19). k-NN / decision tree: minimum accuracy (e.g. 0.95). K-Means: maximum WCSS (about 7–25 depending on the dataset, e.g. 10). Neural network: minimum accuracy (e.g. 0.98). Convolution: minimum match with the target image, R² (e.g. 0.95). Q-learning: maximum number of steps of the learned path to the goal (shortest paths: maze 10, cliff 11, treasure 4). Data split & bias: minimum real-world accuracy of the weaker factory (e.g. 0.88; with 5% of factory B in the sample it is about 0.72).';
$string['targetparam2'] = 'Target Metric 2 (Secondary Goal)';
$string['targetparam2_help'] = 'Second target. Confusion matrix: minimum precision (e.g. 0.70). Regression: maximum polynomial degree (0 = no limit). K-Means: required K (0 = any). Neural network: maximum number of hidden neurons (0 = no limit; XOR needs at least 2, a circle about 3–4). Q-learning: maximum number of training episodes (0 = no limit, e.g. maze 200, cliff 500, treasure 1000); a policy that reaches the goal but misses a target gives half marks. Data split & bias: maximum sample size (0 = no limit, e.g. 120); meeting only the accuracy gives half marks. Not used for gradient descent, k-NN / decision tree and convolution. Full marks need both targets; meeting only one gives half marks.';
$string['targettolerance'] = 'Acceptable Tolerance';
$string['targettolerance_help'] = 'Gradient descent: a cost up to target + tolerance gives half marks (e.g. 0.05). Convolution: a match down to target − tolerance gives half marks (default 0.10). For k-NN / decision tree, half marks are given for an accuracy up to 0.05 below the target.';
$string['taskgoal'] = 'Task Objective';
