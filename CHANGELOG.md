# Changelog

All notable changes to the **AI Simulation Question Type (`qtype_aisim`)** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.2] - 2026-10-30

### Added
- Backup and restore of the question options (`backup/moodle2`). Before this, a course backup kept the AI Simulation questions but lost their simulation, targets and feedback.
- Restoring an older backup without options gives those questions the default simulation (confusion matrix), so they can still be opened and edited.

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

