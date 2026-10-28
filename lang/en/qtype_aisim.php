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
$string['rightsummary_reg'] = 'Validation MSE ≤ {$a->mse} with polynomial degree ≤ {$a->degree}';
$string['scenario'] = 'Scenario / Preset';
$string['scenario_help'] = 'Domain scenario (e.g. "quality" for industrial quality control, "medical" for screening, or dataset key like "sine", "moons", "blobs3").';
$string['sim_cm'] = 'Confusion Matrix & Classification Threshold';
$string['sim_gd'] = 'Gradient Descent Optimization';
$string['sim_km'] = 'K-Means Clustering';
$string['sim_knn'] = 'Decision Boundaries (k-NN & Decision Tree)';
$string['sim_reg'] = 'Polynomial Regression & Regularization';
$string['simconfig'] = 'Advanced Simulation Configuration (JSON)';
$string['simconfig_help'] = 'Optional JSON object to pass custom initial values or constraints to the simulation engine.';
$string['simtype'] = 'Simulation Type';
$string['simtype_help'] = 'Select the machine learning engine: Confusion Matrix & Threshold, Gradient Descent Optimization, Polynomial Regression, Decision Boundaries, or K-Means Clustering.';
$string['simulationsettings'] = 'AI Simulation Settings';
$string['targetparam1'] = 'Target Metric 1 (Primary Goal)';
$string['targetparam1_help'] = 'Primary numeric target: minimum Recall (e.g. 0.90) for Confusion Matrix, maximum Loss (e.g. 0.05) for Gradient Descent, maximum Val MSE for Regression, or minimum Accuracy for k-NN.';
$string['targetparam2'] = 'Target Metric 2 (Secondary Goal)';
$string['targetparam2_help'] = 'Secondary numeric target: minimum Precision (e.g. 0.75) for Confusion Matrix, maximum Degree for Regression, or target K for K-Means.';
$string['targettolerance'] = 'Acceptable Tolerance';
$string['targettolerance_help'] = 'Tolerance margin for partial credit (e.g. 0.02 for loss, 0.05 for accuracy).';
$string['taskgoal'] = 'Task Objective';
