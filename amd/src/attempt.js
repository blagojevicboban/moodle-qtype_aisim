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
 * Question attempt simulation driver module for AI Simulation question type.
 *
 * @module     qtype_aisim/attempt
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {GradientDescentSim} from 'filter_aisim/sims/gradient_descent';
import {ConfusionMatrixSim} from 'filter_aisim/sims/confusion_matrix';
import {RegressionSim} from 'filter_aisim/sims/regression';
import {KnnTreeSim} from 'filter_aisim/sims/knn_tree';
import {KMeansSim} from 'filter_aisim/sims/kmeans';

/**
 * Per simulation type: the engine, the option that receives the question's scenario (and its default),
 * and which saved answer parameters restore which engine options.
 */
const SIMS = {
    'confusion_matrix': {
        Engine: ConfusionMatrixSim,
        scenario: ['scenario', 'quality'],
        restore: {threshold: 'threshold'},
    },
    'gradient_descent': {
        Engine: GradientDescentSim,
        scenario: ['profile', 'convex'],
        restore: {w: 'w0', learningRate: 'lr'},
    },
    'regression': {
        Engine: RegressionSim,
        scenario: ['dataset', 'sine'],
        restore: {degree: 'degree', lambda: 'lambda'},
    },
    'knn_tree': {
        Engine: KnnTreeSim,
        scenario: ['dataset', 'moons'],
        restore: {model: 'model', k: 'k', depth: 'depth'},
    },
    'kmeans': {
        Engine: KMeansSim,
        scenario: ['dataset', 'blobs3'],
        restore: {k: 'k'},
    },
};

/**
 * Initialise the question attempt.
 *
 * @param {string} containerId ID of the element that receives the simulation.
 * @param {string} inputId ID of the hidden input that carries the answer.
 * @param {object} config Configuration parameters passed from renderer.
 */
export const init = (containerId, inputId, config = {}) => {
    const container = document.getElementById(containerId);
    const input = document.getElementById(inputId);
    if (!container || !input) {
        return;
    }

    let savedState = null;
    if (input.value) {
        try {
            savedState = JSON.parse(input.value);
        } catch (e) {
            savedState = null;
        }
    }

    const onChange = (state) => {
        const json = JSON.stringify(state);
        if (input.value !== json) {
            input.value = json;
            input.dispatchEvent(new Event('change', {bubbles: true}));
        }
    };

    const simType = config.simtype || 'confusion_matrix';
    const readonly = !!config.readonly;
    const lang = config.lang || (document.documentElement && document.documentElement.lang) || 'en';

    const simOptions = {
        lang: lang,
        readonly: readonly,
        onChange: readonly ? null : onChange
    };

    // If advanced JSON config is provided, merge it safely.
    if (config.simconfig) {
        try {
            const extra = JSON.parse(config.simconfig);
            Object.assign(simOptions, extra);
        } catch (e) {
            // Ignore malformed custom config.
        }
    }

    if (!Object.prototype.hasOwnProperty.call(SIMS, simType)) {
        return;
    }
    const sim = SIMS[simType];
    const [scenarioOption, scenarioDefault] = sim.scenario;
    simOptions[scenarioOption] = config.scenario || scenarioDefault;

    // Restore the parameters from the student's saved answer.
    const saved = (savedState && savedState.parameters) || {};
    Object.entries(sim.restore).forEach(([param, option]) => {
        if (saved[param] !== undefined) {
            simOptions[option] = saved[param];
        }
    });

    new sim.Engine(container, simOptions);
};

