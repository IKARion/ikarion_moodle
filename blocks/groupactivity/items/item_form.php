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

namespace groupactivity\form;

require_once("$CFG->libdir/formslib.php");

class item_form extends \moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'shortname', 'Kurzname');
        $mform->setType('shortname', PARAM_RAW);
        $mform->addRule('shortname', 'Bitte einen Kurznamen angeben', 'required', '', 'client', false, false);

        $mform->addElement('textarea', 'content', 'Fragetext');
        $mform->setType('content', PARAM_TEXT);
        $mform->addRule('content', 'Bitte Inhalt angeben', 'required', '', 'client', false, false);

        $mform->addElement('text', 'length', 'Skalenstufen (0 bis ...)');
        $mform->setType('length', PARAM_RAW);
        $mform->addRule('length', 'Bitte eine Skalenstufe angeben', 'required', '', 'client', false, false);

        $mform->addElement('advcheckbox', 'isactive', get_string('active'), '', [], array(0, 1));
        $mform->setDefault('isactive', 1);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'blockinstance');
        $mform->setType('blockinstance', PARAM_INT);
        $mform->setDefault('blockinstance', $this->_customdata['blockinstance']);

        $mform->addElement('hidden', 'position');
        $mform->setType('position', PARAM_INT);
        $mform->setDefault('position', 0);

        $mform->addElement('hidden', 'deleted');
        $mform->setType('position', PARAM_INT);
        $mform->setDefault('position', 0);

        $this->add_action_buttons();
    }
}