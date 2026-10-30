# Changelog

All notable changes to the **AI Simulation Question Type (`qtype_aisim`)** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.9.0] - 2026-10-30

### Added
- Data Split & Bias simulation type (`data_bias`, needs filter_aisim 0.9.0). The scenario is the population
  ("shifted" / "similar"). Full marks when the real-world accuracy of the weaker factory reaches target 1 with at
  most target 2 samples (0 = no limit); meeting only the accuracy gives half marks. A review redraws the student's
  own sample.
- Three data split & bias questions in `demo/questions_aiforvet.xml` (now 27), with measured targets.

## [0.8.0] - 2026-10-30

### Added
- Reinforcement Learning (Q-learning) simulation type (`q_learning`, needs filter_aisim 0.8.0). The scenario is the
  map (maze / cliff / treasure). Full marks when the learned policy reaches the goal in at most target 1 steps after
  at most target 2 training episodes (0 = no limit); reaching the goal otherwise gives half marks. A review re-trains
  the agent to the student's saved episodes and shows the student's own policy.
- Three Q-learning questions in `demo/questions_aiforvet.xml` (now 24), with measured targets.

## [0.7.0] - 2026-10-30

### Added
- Convolution & Image Filters simulation type (`convolution`, needs filter_aisim 0.7.0). The scenario is the image,
  the target filter goes into the simulation config (`{"target": "sobelx"}`, optionally `"presets": false`). Graded on
  the match with the target image (R² ≥ target 1); down to target − tolerance (default 0.10) gives half marks.
  A review shows the student's own kernel.
- Three convolution questions in `demo/questions_aiforvet.xml` (now 21), with measured targets.

## [0.6.0] - 2026-10-30

### Added
- Perceptron & Neural Network simulation type (`neural_network`, needs filter_aisim 0.6.0). Graded on accuracy
  (target 1) and the number of hidden neurons (target 2, 0 = no limit); meeting one of the two gives half marks.
  A review re-trains the network to the student's saved epochs, so it shows the student's own boundary.
- Three neural network questions in `demo/questions_aiforvet.xml` (now 18), with measured targets.

### Fixed
- The response summary of the confusion matrix showed "F1: ?"; F1 is now computed from recall and precision.

## [0.5.2] - 2026-10-30

### Added
- Backup and restore of the question options (`backup/moodle2`). Before this, a course backup kept the AI Simulation questions but lost their simulation, targets and feedback.
- Restoring an older backup without options gives those questions the default simulation (confusion matrix), so they can still be opened and edited.

### Fixed
- Demo question bank (`demo/questions_aiforvet.xml`) and the demo setup scripts: most targets could not be reached or did not match the grading (gradient descent cost ≤ 0.05 while the minimum is 1.00, datasets `linear`, `noisy` and `rings` that do not exist, K-Means WCSS ≤ 0.85, R² in the text but MSE in the grading). All 15 questions now use real datasets and targets measured on the simulations.
- Help for the target fields and the teacher guides explain what each target means for each simulation, with reachable values.

### Removed
- The demo course backup (`demo/aiforvet_simulations_demo_course.mbz`) and the teacher guide section on restoring it; use `demo/questions_aiforvet.xml` for the ready-made questions.

## [0.5.1] - 2026-10-30

### Fixed
- Adding or saving a question failed with "mysqli::real_escape_string(): Argument #1 ($string) must be of type string, array given": combined feedback is now saved with `save_combined_feedback_helper()`.
- Hints from the "Multiple tries" section are now saved.
- Moodle XML import of the demo bank (`demo/questions_aiforvet.xml`) failed; import and export now use the standard combined feedback and hint format, and still read the older plain-text feedback.

## [0.5.0] - 2026-10-28

### Added
- Initial release of `qtype_aisim` for Moodle 4.5 through 5.3+.
- Automated grading engine (`question_graded_automatically`) supporting 5 machine learning simulations:
  - Confusion Matrix & Decision Threshold (Recall/Precision evaluation).
  - Gradient Descent Optimization (Loss curve minimization).
  - Polynomial Regression & Regularization (Validation MSE & overfitting constraint).
  - Decision Boundaries (k-NN & Decision Tree test accuracy).
  - K-Means Unsupervised Clustering (WCSS inertia & convergence).
- Quiz editing form with simulation picker, domain scenario preset, target metric criteria, tolerance margin, and advanced JSON configuration.
- Interactive student attempt interface with real-time feedback and Moodle quiz autosave integration.
- Readonly review mode with visual status badges (100% Achieved, 50% Partial, 0% Not Met).
- Trilingual localization: English (`en`), Serbian Latin (`sr_lt`), and Serbian Cyrillic (`sr_cr`).
- GDPR Null Privacy Provider (`\core_privacy\local\metadata\null_provider`).
- GitHub Actions CI workflow with `moodle-plugin-ci`.

