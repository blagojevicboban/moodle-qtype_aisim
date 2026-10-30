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
$string['rightsummary_conv'] = 'Podudaranje sa ciljnom slikom (R²) ≥ {$a->match}%';
$string['rightsummary_gd'] = 'Trošak J(w) ≤ {$a->loss}';
$string['rightsummary_km'] = 'WCSS greška ≤ {$a->wcss} uz K = {$a->k}';
$string['rightsummary_knn'] = 'Tačnost klasifikacije na test skupu ≥ {$a->acc}%';
$string['rightsummary_nn'] = 'Tačnost ≥ {$a->acc}% uz najviše {$a->hidden} skrivenih neurona';
$string['rightsummary_nn_any'] = 'Tačnost ≥ {$a->acc}%';
$string['rightsummary_ql'] = 'Naučena politika stiže do cilja u najviše {$a->steps} koraka, posle najviše {$a->episodes} epizoda treninga';
$string['rightsummary_ql_any'] = 'Naučena politika stiže do cilja u najviše {$a->steps} koraka';
$string['rightsummary_reg'] = 'Validacioni MSE ≤ {$a->mse} uz stepen polinoma ≤ {$a->degree}';
$string['scenario'] = 'Scenario / Predložak';
$string['scenario_help'] = 'Praktični scenario (npr. "quality" za industrijsku kontrolu kvaliteta, "medical" za medicinsku dijagnostiku, ili ključ skupa podataka "sine", "moons", "blobs3"; za neuronsku mrežu "linear", "xor" ili "circles"; za konvoluciju slika "shapes", "stripes", "face" ili "noisy"; za Q-learning mapa "maze", "cliff" ili "treasure").';
$string['sim_cm'] = 'Matrica konfuzije i prag odlučivanja';
$string['sim_conv'] = 'Konvolucija i filteri slike';
$string['sim_gd'] = 'Gradijentni spust (Gradient Descent)';
$string['sim_km'] = 'K-Means klasterovanje';
$string['sim_knn'] = 'Granice odlučivanja (k-NN i Stablo odlučivanja)';
$string['sim_nn'] = 'Perceptron i neuronska mreža';
$string['sim_ql'] = 'Učenje potkrepljivanjem (Q-learning)';
$string['sim_reg'] = 'Polinomska regresija i regularizacija';
$string['simconfig'] = 'Napredna JSON konfiguracija';
$string['simconfig_help'] = 'Opcioni JSON objekat za prosleđivanje početnih težina ili prilagođenih parametara simulatoru. Za konvoluciju se ovde zadaje ciljni filter: {"target": "sobelx"} (takođe "box", "gaussian", "sharpen", "sobely", "laplacian", "emboss"); dodajte "presets": false da sakrijete gotove filtere, pa učenik mora sam da upiše kernel.';
$string['simtype'] = 'Tip simulacije';
$string['simtype_help'] = 'Izaberite modul mašinskog učenja: Matrica konfuzije i prag, Optimizacija gradijentnim spustom, Polinomska regresija, Granice odlučivanja, K-Means klasterovanje, Perceptron i neuronska mreža, Konvolucija i filteri slike ili Učenje potkrepljivanjem (Q-learning).';
$string['simulationsettings'] = 'Podešavanja AI simulacije';
$string['targetparam1'] = 'Ciljna metrika 1 (Glavni cilj)';
$string['targetparam1_help'] = 'Glavni cilj, zavisno od simulacije. Matrica konfuzije: najmanji odziv / Recall (npr. 0.80). Gradijentni spust: najveći trošak J(w); najmanji mogući trošak je 1.00 (konveksna), 0.98 (nekonveksna) i 1.82 (plato), pa npr. 1.01. Regresija: najveća validaciona greška MSE (dostižne vrednosti su oko 0.12–0.20, npr. 0.19). k-NN / stablo odlučivanja: najmanja tačnost (npr. 0.95). K-Means: najveći WCSS (oko 7–25 zavisno od skupa podataka, npr. 10). Neuronska mreža: najmanja tačnost (npr. 0.98). Konvolucija: najmanje podudaranje sa ciljnom slikom, R² (npr. 0.95). Q-learning: najveći broj koraka naučenog puta do cilja (najkraći putevi: lavirint 10, litica 11, blago 4).';
$string['targetparam2'] = 'Ciljna metrika 2 (Dopunski cilj)';
$string['targetparam2_help'] = 'Drugi cilj. Matrica konfuzije: najmanja preciznost / Precision (npr. 0.70). Regresija: najveći stepen polinoma (0 = bez ograničenja). K-Means: traženi broj klastera K (0 = bilo koji). Neuronska mreža: najveći broj skrivenih neurona (0 = bez ograničenja; za XOR su potrebna bar 2, za krug oko 3–4). Q-learning: najveći broj epizoda treninga (0 = bez ograničenja, npr. lavirint 200, litica 500, blago 1000); politika koja stiže do cilja, ali ne ispuni neki cilj, dobija pola poena. Ne koristi se za gradijentni spust, k-NN / stablo odlučivanja i konvoluciju. Za sve poene potrebna su oba cilja; ako je ispunjen samo jedan, dobija se pola poena.';
$string['targettolerance'] = 'Dozvoljena tolerancija';
$string['targettolerance_help'] = 'Gradijentni spust: trošak do cilja + tolerancije donosi pola poena (npr. 0.05). Konvolucija: podudaranje do cilja − tolerancije donosi pola poena (podrazumevano 0.10). Za k-NN / stablo odlučivanja pola poena se dobija za tačnost do 0.05 ispod cilja.';
$string['taskgoal'] = 'Cilj zadatka';
