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
 * @package    block_grouplatency
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing grouplatency block instances.
 */
class block_grouplatency_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $DB, $COURSE;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Level 0
        $mform->addElement('text', 'config_level_0', get_string('level_0', 'block_grouplatency'));
        $mform->setDefault('config_level_0', 0.33);
        $mform->setType('config_level_0', PARAM_TEXT);

        $mform->addElement('textarea', 'config_level_0_text', get_string('level_0_text', 'block_grouplatency'));
        $mform->setType('config_level_0_text', PARAM_RAW);

        // Level 1
        $mform->addElement('text', 'config_level_1', get_string('level_1', 'block_grouplatency'));
        $mform->setDefault('config_level_1', 0.66);
        $mform->setType('config_level_1', PARAM_TEXT);

        $mform->addElement('textarea', 'config_level_1_text', get_string('level_1_text', 'block_grouplatency'));
        $mform->setType('config_level_1_text', PARAM_RAW);

        // Level 2
        $mform->addElement('text', 'config_level_2', get_string('level_2', 'block_grouplatency'));
        $mform->setDefault('config_level_2', 1);
        $mform->setType('config_level_2', PARAM_TEXT);

        $mform->addElement('textarea', 'config_level_2_text', get_string('level_2_text', 'block_grouplatency'));
        $mform->setType('config_level_2_text', PARAM_RAW);

        $groupings_raw = groups_get_all_groupings($COURSE->id);
        $groupings = array();

        if (!empty($groupings_raw)) {
            foreach ($groupings_raw as $grouping) {
                $groupings[$grouping->id] = $grouping->name;
            }
        } else {
            $groupings[0] = 'No Groupings';
        }

        $mform->addElement('select', 'config_mirroring', get_string('mirroring', 'block_grouplatency'), $groupings);
        $mform->addElement('select', 'config_guiding', get_string('guiding', 'block_grouplatency'), $groupings);
        $mform->addElement('select', 'config_mirroring_guiding', get_string('mirroring_guiding', 'block_grouplatency'),
            $groupings);
        $mform->addElement('select', 'config_nothing', get_string('nothing', 'block_grouplatency'), $groupings);
    }
}