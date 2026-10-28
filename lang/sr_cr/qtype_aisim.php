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
$string['rightsummary_reg'] = 'Валидациони MSE ≤ {$a->mse} уз степен полинома ≤ {$a->degree}';
$string['scenario'] = 'Сценарио / Предложак';
$string['scenario_help'] = 'Практични сценарио (нпр. "quality" за индустријску контролу квалитета, "medical" за медицинску дијагностику, или кључ скупа података "sine", "moons", "blobs3").';
$string['sim_cm'] = 'Матрица конфузије и праг одлучивања';
$string['sim_gd'] = 'Градијентни спуст (Gradient Descent)';
$string['sim_km'] = 'K-Means кластеровање';
$string['sim_knn'] = 'Границе одлучивања (k-NN и Стабло одлучивања)';
$string['sim_reg'] = 'Полиномска регресија и регуларизација';
$string['simconfig'] = 'Напредна JSON конфигурација';
$string['simconfig_help'] = 'Опциони JSON објекат за прослеђивање почетних тежина или прилагођених параметара симулатору.';
$string['simtype'] = 'Тип симулације';
$string['simtype_help'] = 'Изаберите модул машинског учења: Матрица конфузије и праг, Оптимизација градијентним спустом, Полиномска регресија, Границе одлучивања или К-Means кластеровање.';
$string['simulationsettings'] = 'Подешавања АИ симулације';
$string['targetparam1'] = 'Циљна метрика 1 (Главни циљ)';
$string['targetparam1_help'] = 'Примарна нумеричка граница: минимални опозив / Recall (нпр. 0.90) за матрицу конфузије, максимална грешка / Loss (нпр. 0.05) за градијентни спуст, максимални Val MSE за регресију, или минимална тачност за k-NN.';
$string['targetparam2'] = 'Циљна метрика 2 (Допунски циљ)';
$string['targetparam2_help'] = 'Секундарна граница: минимална прецизност / Precision (нпр. 0.75) за матрицу конфузије, максимални степен полинома за регресију, или циљни број кластера K за K-Means.';
$string['targettolerance'] = 'Дозвољена толеранција';
$string['targettolerance_help'] = 'Толеранција за делимично признавање поена (нпр. 0.02 за трошак, 0.05 за тачност).';
$string['taskgoal'] = 'Циљ задатка';
