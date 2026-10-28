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
 * Strings for component 'qtype_aisim', language 'sr_lt'.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['correctansweris'] = 'Zadati kriterijum je bio: {$a}';
$string['goalfailed'] = '✗ Cilj nije ostvaren (0%)';
$string['goalpartial'] = '⚠ Delimično ostvareno (50%)';
$string['goalsatisfied'] = '✓ Cilj uspešno ostvaren! (100%)';
$string['invalidjson'] = 'Neispravan JSON format u konfiguraciji simulacije.';
$string['noresponse'] = 'Nije poslata konfiguracija simulacije.';
$string['pleaseinteractwithsim'] = 'Pre predaje odgovora podesite parametre na interaktivnoj simulaciji.';
$string['pluginname'] = 'AI Simulacija';
$string['pluginname_help'] = 'Učenici rešavaju praktične zadatke mašinskog učenja interaktivnim podešavanjem simulacija u realnom vremenu. Odgovori se ocenjuju automatski na osnovu postignutih metrika modela.';
$string['pluginname_link'] = 'question/type/aisim';
$string['pluginnameadding'] = 'Dodavanje pitanja AI simulacije';
$string['pluginnameediting'] = 'Uređivanje pitanja AI simulacije';
$string['pluginnamesummary'] = 'Interaktivno pitanje u kojem učenici podešavaju parametre veštačke inteligencije, uz automatsko bodovanje prema zadatom cilju.';
$string['privacy:metadata'] = 'Tip pitanja AI Simulacija ne čuva nikakve lične podatke.';
$string['rightsummary_cm'] = 'Opoziv (Recall) ≥ {$a->rec}% i Preciznost (Precision) ≥ {$a->prec}%';
$string['rightsummary_gd'] = 'Trošak J(w) ≤ {$a->loss}';
$string['rightsummary_km'] = 'WCSS greška ≤ {$a->wcss} uz K = {$a->k}';
$string['rightsummary_knn'] = 'Tačnost klasifikacije na test skupu ≥ {$a->acc}%';
$string['rightsummary_reg'] = 'Validacioni MSE ≤ {$a->mse} uz stepen polinoma ≤ {$a->degree}';
$string['scenario'] = 'Scenario / Predložak';
$string['scenario_help'] = 'Praktični scenario (npr. "quality" za industrijsku kontrolu kvaliteta, "medical" za medicinsku dijagnostiku, ili ključ skupa podataka "sine", "moons", "blobs3").';
$string['sim_cm'] = 'Matrica konfuzije i prag odlučivanja';
$string['sim_gd'] = 'Gradijentni spust (Gradient Descent)';
$string['sim_km'] = 'K-Means klasterovanje';
$string['sim_knn'] = 'Granice odlučivanja (k-NN i Stablo odlučivanja)';
$string['sim_reg'] = 'Polinomska regresija i regularizacija';
$string['simconfig'] = 'Napredna JSON konfiguracija';
$string['simconfig_help'] = 'Opcioni JSON objekat za prosleđivanje početnih težina ili prilagođenih parametara simulatoru.';
$string['simtype'] = 'Tip simulacije';
$string['simtype_help'] = 'Izaberite modul mašinskog učenja: Matrica konfuzije i prag, Optimizacija gradijentnim spustom, Polinomska regresija, Granice odlučivanja ili K-Means klasterovanje.';
$string['simulationsettings'] = 'Podešavanja AI simulacije';
$string['targetparam1'] = 'Ciljna metrika 1 (Glavni cilj)';
$string['targetparam1_help'] = 'Primarna numerička granica: minimalni opoziv / Recall (npr. 0.90) za matricu konfuzije, maksimalna greška / Loss (npr. 0.05) za gradijentni spust, maksimalni Val MSE za regresiju, ili minimalna tačnost za k-NN.';
$string['targetparam2'] = 'Ciljna metrika 2 (Dopunski cilj)';
$string['targetparam2_help'] = 'Sekundarna granica: minimalna preciznost / Precision (npr. 0.75) za matricu konfuzije, maksimalni stepen polinoma za regresiju, ili ciljni broj klastera K za K-Means.';
$string['targettolerance'] = 'Dozvoljena tolerancija';
$string['targettolerance_help'] = 'Tolerancija za delimično priznavanje poena (npr. 0.02 za trošak, 0.05 za tačnost).';
$string['taskgoal'] = 'Cilj zadatka';
