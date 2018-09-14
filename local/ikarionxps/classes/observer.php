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
 * Event observer ikarionxps
 *
 * @package    local_ikarionxps
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author       Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/ikarionxps/locallib.php');

class local_ikarionxps_observer {

    /**
     * Handle base events
     *
     * @param \core\event\base
     */
    public static function event_triggered($event) {
        global $DB, $CFG;

        $dbman = $DB->get_manager();
        $data = $event->get_data();
        $send_data = true;

        if ($dbman->table_exists('ikarionconsent')) {
            $consent = $DB->get_record('ikarionconsent', array('userid' => $data['userid']));

            if ($consent) {
                if ($consent->choice == 0) $send_data = false;
            }
        }

        if ($send_data) {
            $data['userid'] = openssl_encrypt($data['userid'], $CFG->encryptmethod, $CFG->encryptkey);

            if (!empty($data['relateduserid'])) {
                $data['relateduserid'] = openssl_encrypt($data['relateduserid'], $CFG->encryptmethod, $CFG->encryptkey);
            }

            /*
            if (!empty($data['other'])) {
                $data['other'] = null;
            }*/

            $data = json_encode($data);
            $error = local_ikarionxps_send_event($data);

            if ($error != 0) {
                $record = new stdClass();
                $record->event = $data;
                $record->timecreated = time();

                $DB->insert_record('ikarionxps', $record);
            }
        }
    }
}
