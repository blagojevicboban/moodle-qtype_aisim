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
 * Restore of the AI Simulation question options.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restores qtype_aisim_options.
 *
 * Backups made before 0.5.2 have no options: those questions get the default simulation
 * (confusion matrix, quality scenario) so that they can still be opened and edited.
 *
 * @package    qtype_aisim
 * @copyright  2026 Boban Blagojević
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_aisim_plugin extends restore_qtype_plugin {
    /**
     * Returns the paths to be handled by the plugin at question level.
     *
     * @return restore_path_element[]
     */
    protected function define_question_plugin_structure() {
        return [new restore_path_element('aisim', $this->get_pathfor('/aisim'))];
    }

    /**
     * Process the qtype/aisim element.
     *
     * @param array $data the options
     */
    public function process_aisim($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Only questions created by this restore get options; mapped ones already have them.
        if ($this->get_mappingid('question_created', $this->get_old_parentid('question'))) {
            $data->questionid = $this->get_new_parentid('question');
            $newid = $DB->insert_record('qtype_aisim_options', $data);
            $this->set_mapping('qtype_aisim_options', $oldid, $newid);
        }
    }

    /**
     * Give questions from older backups (without options) the default options.
     */
    protected function after_execute_question() {
        global $DB;

        $sql = "SELECT q.id
                  FROM {question} q
                  JOIN {backup_ids_temp} bi ON bi.newitemid = q.id
             LEFT JOIN {qtype_aisim_options} o ON o.questionid = q.id
                 WHERE q.qtype = ? AND o.id IS NULL
                       AND bi.backupid = ? AND bi.itemname = ?";
        $params = ['aisim', $this->get_restoreid(), 'question_created'];
        foreach ($DB->get_fieldset_sql($sql, $params) as $questionid) {
            $DB->insert_record('qtype_aisim_options', (object)[
                'questionid' => $questionid,
                'simtype' => 'confusion_matrix',
                'scenario' => 'quality',
                'targetparam1' => 0.85,
                'targetparam2' => 1.0,
                'targettolerance' => 0.05,
                'simconfig' => '',
                'correctfeedback' => '',
                'correctfeedbackformat' => FORMAT_HTML,
                'partiallycorrectfeedback' => '',
                'partiallycorrectfeedbackformat' => FORMAT_HTML,
                'incorrectfeedback' => '',
                'incorrectfeedbackformat' => FORMAT_HTML,
                'shownumcorrect' => 0,
            ]);
        }
    }

    /**
     * The options id and question id differ between sites and must not change the identity hash.
     *
     * @return array
     */
    protected function define_excluded_identity_hash_fields(): array {
        return ['/options/id', '/options/questionid'];
    }

    /**
     * Links in the combined feedback to decode on restore.
     *
     * @return restore_decode_content[]
     */
    public static function define_decode_contents() {
        $fields = ['correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'];
        return [new restore_decode_content('qtype_aisim_options', $fields, 'qtype_aisim_options')];
    }
}
