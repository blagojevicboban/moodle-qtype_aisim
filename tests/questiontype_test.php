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

namespace qtype_aisim;

use question_bank;
use qformat_xml;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');

/**
 * Unit tests for the AI Simulation question type: saving, loading and Moodle XML import/export.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_aisim
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_aisim::class)]
final class questiontype_test extends \advanced_testcase {
    /**
     * Create the neural network test question through the editing-form path.
     *
     * @return \stdClass the saved question
     */
    private function create_question(): \stdClass {
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        return $generator->create_question('aisim', 'nn', ['category' => $category->id]);
    }

    /**
     * The simulation settings, combined feedback and hints survive saving and loading.
     */
    public function test_save_and_load(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $saved = $this->create_question();

        $q = question_bank::load_question($saved->id);
        $this->assertSame('neural_network', $q->simtype);
        $this->assertSame('xor', $q->scenario);
        $this->assertEqualsWithDelta(0.98, $q->targetparam1, 0.0001);
        $this->assertEqualsWithDelta(4, $q->targetparam2, 0.0001);
        $this->assertEqualsWithDelta(0.05, $q->targettolerance, 0.0001);
        $this->assertSame('{"layers":0}', $q->simconfig);
        $this->assertSame('Well done.', $q->correctfeedback);
        $this->assertSame('Almost.', $q->partiallycorrectfeedback);
        $this->assertSame('Not yet.', $q->incorrectfeedback);
        $this->assertCount(1, $q->hints);
        $this->assertSame('Add a hidden layer.', reset($q->hints)->hint);
    }

    /**
     * Import a Moodle XML file into a new question category.
     *
     * @param string $filename
     * @return array [bool success, \stdClass category]
     */
    private function import_file(string $filename): array {
        $category = $this->getDataGenerator()->get_plugin_generator('core_question')->create_question_category();
        $importer = new qformat_xml();
        $importer->setCategory($category);
        $importer->setContexts([\context::instance_by_id($category->contextid)]);
        $importer->setFilename($filename);
        $importer->setCatfromfile(false);
        $importer->setContextfromfile(false);
        $importer->setStoponerror(true);
        $importer->set_display_progress(false);
        ob_start();
        $ok = $importer->importpreprocess() && $importer->importprocess() && $importer->importpostprocess();
        ob_end_clean();
        return [$ok, $category];
    }

    /**
     * Moodle XML export followed by import gives the same question.
     */
    public function test_xml_round_trip(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $saved = $this->create_question();
        $data = question_bank::load_question_data($saved->id);

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($data);
        $this->assertStringContainsString('<simtype>neural_network</simtype>', $xml);

        $file = make_request_directory() . '/question.xml';
        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?><quiz>' . $xml . '</quiz>');
        [$ok, $category] = $this->import_file($file);
        $this->assertTrue($ok);

        $id = $DB->get_field_sql('SELECT q.id FROM {question} q
                JOIN {question_versions} v ON v.questionid = q.id
                JOIN {question_bank_entries} e ON e.id = v.questionbankentryid
               WHERE e.questioncategoryid = ?', [$category->id]);
        $imported = question_bank::load_question($id);
        $this->assertSame('neural_network', $imported->simtype);
        $this->assertSame('xor', $imported->scenario);
        $this->assertEqualsWithDelta(0.98, $imported->targetparam1, 0.0001);
        $this->assertEqualsWithDelta(4, $imported->targetparam2, 0.0001);
        $this->assertSame('{"layers":0}', $imported->simconfig);
        $this->assertSame('Well done.', $imported->correctfeedback);
        $this->assertSame('Add a hidden layer.', reset($imported->hints)->hint);
    }

    /**
     * The demo question bank shipped with the plugin imports completely.
     */
    public function test_demo_bank_imports(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        [$ok] = $this->import_file($CFG->dirroot . '/question/type/aisim/demo/questions_aiforvet.xml');
        $this->assertTrue($ok);
        $this->assertEquals(27, $DB->count_records('qtype_aisim_options'));
        $this->assertCount(9, $DB->get_fieldset_sql('SELECT DISTINCT simtype FROM {qtype_aisim_options}'));
    }
}
