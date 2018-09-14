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
 * Webservice
 *
 * @package    block_ikarionconsent
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author	   Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class block_ikarionconsent_external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_choice_parameters() {
        return new external_function_parameters(array(
            'choice' => new external_value(PARAM_INT, 'user policy choice')
        ));
    }

    /**
     * Returns status
     * @return array response
     */
    public static function set_choice($choice) {
        global $DB, $USER;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::set_choice_parameters(),
            array('choice' => $choice));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);

        $record_exist = $DB->get_record('ikarionconsent', array('userid' => $USER->id));

        if ($record_exist) {
            $record_exist->choice = $choice;
            $record_exist->timemodified = time();
            $DB->update_record('ikarionconsent', $record_exist);
        } else {
            $record = new stdClass();
            $record->userid = $USER->id;
            $record->choice = $choice;
            $record->timecreated = time();
            $record->timemodified = time();
            $DB->insert_record('ikarionconsent', $record);
        }

        $response = array(
            'status' => 'set'
        );

        return $response;
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function set_choice_returns() {
        $keys = [
            'status' => new \external_value(PARAM_TEXT, 'Set successfully', VALUE_REQUIRED)
        ];

        return new \external_single_structure($keys, 'Set policy choice');
    }
}

