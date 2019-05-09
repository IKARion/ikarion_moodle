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

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/blocks/grouplatency/classes/request.php');

class block_grouplatency_external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_data_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(PARAM_INT, 'userid'),
            'courseid' => new external_value(PARAM_INT, 'courseid'),
            'instanceid' => new external_value(PARAM_INT, 'blockinstance')
        ));
    }

    /**
     * Returns status
     * @return array user data
     */
    public static function get_data($userid, $courseid, $instance) {
        global $SESSION;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_data_parameters(),
            array('userid' => $userid, 'courseid' => $courseid, 'instanceid' => $instance));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);

        $request = new grouplatency\request($courseid, $userid);
        $data = $request->get_group_posts_data();
        
        return ['data' => json_encode($data)];
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function get_data_returns() {
        return new \external_single_structure(
            array(
                'data' => new external_value(PARAM_RAW)
            ), 'get data response');
    }
}
