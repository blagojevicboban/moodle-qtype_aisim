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
 * Strings for component 'qtype_aisim', language 'sr_cr'.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['correctansweris'] = 'Задати критеријум је био: {$a}';
$string['goalfailed'] = '✗ Циљ није остварен (0%)';
$string['goalpartial'] = '⚠ Делимично остварено (50%)';
$string['goalsatisfied'] = '✓ Циљ успешно остварен! (100%)';
$string['invalidjson'] = 'Неисправан JSON формат у конфигурацији симулације.';
$string['noresponse'] = 'Није послата конфигурација симулације.';
$string['pleaseinteractwithsim'] = 'Пре предаје одговора подесите параметре на интерактивној симулацији.';
$string['pluginname'] = 'АИ Симулација';
$string['pluginname_help'] = 'Ученици решавају практичне задатке машинског учења интерактивним подешавањем симулација у реалном времену. Одговори се оцењују аутоматски на основу постигнутих метрика модела.';
$string['pluginname_link'] = 'question/type/aisim';
$string['pluginnameadding'] = 'Додавање питања АИ симулације';
$string['pluginnameediting'] = 'Уређивање питања АИ симулације';
$string['pluginnamesummary'] = 'Интерактивно питање у којем ученици подешавају параметре вештачке интелигенције, уз аутоматско бодовање према задатом циљу.';
$string['privacy:metadata'] = 'Тип питања АИ Симулација не чува никакве личне податке.';
$string['rightsummary_cm'] = 'Опозив (Recall) ≥ {$a->rec}% и Прецизност (Precision) ≥ {$a->prec}%';
$string['rightsummary_gd'] = 'Трошак J(w) ≤ {$a->loss}';
$string['rightsummary_km'] = 'WCSS грешка ≤ {$a->wcss} уз K = {$a->k}';
$string['rightsummary_knn'] = 'Тачност класификације на тест скупу ≥ {$a->acc}%';
$string['rightsummary_nn'] = 'Тачност ≥ {$a->acc}% уз највише {$a->hidden} скривених неурона';
$string['rightsummary_nn_any'] = 'Тачност ≥ {$a->acc}%';
$string['rightsummary_reg'] = 'Валидациони MSE ≤ {$a->mse} уз степен полинома ≤ {$a->degree}';
$string['scenario'] = 'Сценарио / Предложак';
$string['scenario_help'] = 'Практични сценарио (нпр. "quality" за индустријску контролу квалитета, "medical" за медицинску дијагностику, или кључ скупа података "sine", "moons", "blobs3"; за неуронску мрежу "linear", "xor" или "circles").';
$string['sim_cm'] = 'Матрица конфузије и праг одлучивања';
$string['sim_gd'] = 'Градијентни спуст (Gradient Descent)';
$string['sim_km'] = 'K-Means кластеровање';
$string['sim_knn'] = 'Границе одлучивања (k-NN и Стабло одлучивања)';
$string['sim_nn'] = 'Перцептрон и неуронска мрежа';
$string['sim_reg'] = 'Полиномска регресија и регуларизација';
$string['simconfig'] = 'Напредна JSON конфигурација';
$string['simconfig_help'] = 'Опциони JSON објекат за прослеђивање почетних тежина или прилагођених параметара симулатору.';
$string['simtype'] = 'Тип симулације';
$string['simtype_help'] = 'Изаберите модул машинског учења: Матрица конфузије и праг, Оптимизација градијентним спустом, Полиномска регресија, Границе одлучивања, K-Means кластеровање или Перцептрон и неуронска мрежа.';
$string['simulationsettings'] = 'Подешавања АИ симулације';
$string['targetparam1'] = 'Циљна метрика 1 (Главни циљ)';
$string['targetparam1_help'] = 'Главни циљ, зависно од симулације. Матрица конфузије: најмањи одзив / Recall (нпр. 0.80). Градијентни спуст: највећи трошак J(w); најмањи могући трошак је 1.00 (конвексна), 0.98 (неконвексна) и 1.82 (плато), па нпр. 1.01. Регресија: највећа валидациона грешка MSE (достижне вредности су око 0.12–0.20, нпр. 0.19). k-NN / стабло одлучивања: најмања тачност (нпр. 0.95). K-Means: највећи WCSS (око 7–25 зависно од скупа података, нпр. 10). Неуронска мрежа: најмања тачност (нпр. 0.98).';
$string['targetparam2'] = 'Циљна метрика 2 (Допунски циљ)';
$string['targetparam2_help'] = 'Други циљ. Матрица конфузије: најмања прецизност / Precision (нпр. 0.70). Регресија: највећи степен полинома (0 = без ограничења). K-Means: тражени број кластера K (0 = било који). Неуронска мрежа: највећи број скривених неурона (0 = без ограничења; за XOR су потребна бар 2, за круг око 3–4). Не користи се за градијентни спуст и k-NN / стабло одлучивања. За све поене потребна су оба циља; ако је испуњен само један, добија се пола поена.';
$string['targettolerance'] = 'Дозвољена толеранција';
$string['targettolerance_help'] = 'Само за градијентни спуст: трошак до циља + толеранције доноси пола поена (нпр. 0.05). За k-NN / стабло одлучивања пола поена се добија за тачност до 0.05 испод циља.';
$string['taskgoal'] = 'Циљ задатка';
