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

    if (simType === 'confusion_matrix') {
        simOptions.scenario = config.scenario || 'quality';
        if (savedState && savedState.parameters && savedState.parameters.threshold !== undefined) {
            simOptions.threshold = savedState.parameters.threshold;
        }
        new ConfusionMatrixSim(container, simOptions);
    } else if (simType === 'gradient_descent') {
        simOptions.profile = config.scenario || 'convex';
        if (savedState && savedState.parameters) {
            if (savedState.parameters.w !== undefined) {
                simOptions.w0 = savedState.parameters.w;
            }
            if (savedState.parameters.learningRate !== undefined) {
                simOptions.lr = savedState.parameters.learningRate;
            }
        }
        new GradientDescentSim(container, simOptions);
    } else if (simType === 'regression') {
        simOptions.dataset = config.scenario || 'sine';
        if (savedState && savedState.parameters) {
            if (savedState.parameters.degree !== undefined) {
                simOptions.degree = savedState.parameters.degree;
            }
            if (savedState.parameters.lambda !== undefined) {
                simOptions.lambda = savedState.parameters.lambda;
            }
        }
        new RegressionSim(container, simOptions);
    } else if (simType === 'knn_tree') {
        simOptions.dataset = config.scenario || 'moons';
        if (savedState && savedState.parameters) {
            if (savedState.parameters.model !== undefined) {
                simOptions.model = savedState.parameters.model;
            }
            if (savedState.parameters.k !== undefined) {
                simOptions.k = savedState.parameters.k;
            }
            if (savedState.parameters.depth !== undefined) {
                simOptions.depth = savedState.parameters.depth;
            }
        }
        new KnnTreeSim(container, simOptions);
    } else if (simType === 'kmeans') {
        simOptions.dataset = config.scenario || 'blobs3';
        if (savedState && savedState.parameters && savedState.parameters.k !== undefined) {
            simOptions.k = savedState.parameters.k;
        }
        new KMeansSim(container, simOptions);
    }
};

