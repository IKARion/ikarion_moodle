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
 * @package    block_groupactivity
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing groupactivity block instances.
 */
class block_groupactivity_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $DB, $COURSE;
        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $roles = $DB->get_records_sql_menu('SELECT id, name FROM {role} WHERE archetype = ? AND name NOT LIKE ""', ['student']);

        $select = $mform->addElement('select', 'config_roles_mirroring', 'Rollen Mirroring', $roles);
        $select->setMultiple(true);

        $select = $mform->addElement('select', 'config_roles_selfassess', 'Rollen Selbsteinschätzung', $roles);
        $select->setMultiple(true);
    }
}
