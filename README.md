# AI Simulation Question Type for Moodle Quizzes (`qtype_aisim`)

[![Moodle Plugin CI](https://github.com/blagojevicboban/moodle-qtype_aisim/actions/workflows/ci.yml/badge.svg)](https://github.com/blagojevicboban/moodle-qtype_aisim/actions/workflows/ci.yml)
[![Moodle](https://img.shields.io/badge/Moodle-4.5%20to%205.3-orange.svg)](https://moodle.org)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](LICENSE)

**AI Simulation (`qtype_aisim`)** is a Moodle question type for quizzes in which **students solve practical Artificial Intelligence / Machine Learning tasks by interactively adjusting models in real-time simulations**. Student solutions (parameters and resulting metrics) are **automatically graded** against teacher-defined targets.

Developed as part of the Erasmus+ **AI for VET** project (*AI for Vocational Education and Training* — [aiforvet.eu](https://aiforvet.eu/)).

> Status: **v0.5.2 (Alpha)**.

---

## ✨ Features

| Feature | Description |
|---|---|
| 🎯 **5 Interactive AI Sim Engines** | Confusion Matrix & Threshold, Gradient Descent Optimization, Polynomial Regression & Overfitting, Decision Boundaries (k-NN / Decision Tree), and K-Means Clustering. |
| ⚡ **Automatic Real-Time Grading** | Evaluates student model parameters and resulting metrics (`recall`, `precision`, `loss`, `val_mse`, `accuracy`, `wcss`) against question targets with partial credit support. |
| 🧑‍🏫 **Interactive Review & Badges** | In quiz review mode, students and teachers see the submitted simulation state locked, accompanied by a status badge (100% Achieved, 50% Partial, 0% Not Met). |
| 💾 **Quiz Autosave Integration** | Automatically writes serialized JSON model state to hidden form input and triggers Moodle quiz autosave on changes. |
| 🌐 **Trilingual Localization** | Full support for English (`en`), Serbian Latin (`sr_lt`), and Serbian Cyrillic (`sr_cr`). |
| 🔒 **GDPR Compliant** | Implements Moodle Privacy API (`null_provider`); answers are managed securely within Moodle's question attempt subsystem. |

---

## 📋 Requirements

- **Moodle**: 4.5 LTS through 5.3+
- **PHP**: 8.2+
- **[filter_aisim](https://github.com/blagojevicboban/moodle-filter_aisim)**: Must be installed (provides the simulation engines).

---

## 🚀 Installation

### Via Git
```bash
# In Moodle root (or public/question/type in Moodle 5.1+)
cd /path/to/moodle/public/question/type
git clone https://github.com/blagojevicboban/moodle-qtype_aisim.git aisim
php admin/cli/upgrade.php
```

---

## 📚 Documentation & Ready-to-Use Resources

- 🇷🇸 **[Pedagoško-tehnički priručnik za nastavnike](docs/PRIRUCNIK_ZA_NASTAVNIKE.md)**
- 🇬🇧 **[Pedagogical and Technical Teacher Guide](docs/TEACHER_GUIDE.md)**
- 📝 **[15-Question Pre-packaged Bank (Moodle XML)](demo/questions_aiforvet.xml)**
- 📦 **[Turnkey Course Backup (.mbz)](https://github.com/blagojevicboban/moodle-qtype_aisim/releases/download/v0.5.2/aiforvet_simulations_demo_course.mbz)**

---

## 📄 License

GNU General Public License v3.0 or later.
Copyright (c) 2026 Boban Blagojević.

