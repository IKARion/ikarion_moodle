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

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/blocks/groupactivity/classes/request.php');

class block_groupactivity_external extends external_api {
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
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_data_parameters(),
            array('userid' => $userid, 'courseid' => $courseid, 'instanceid' => $instance));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);

        $request = new groupactivity\request($courseid, $userid);
        $data = $request->get_group_activity_data($instance);

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

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_self_assess_parameters() {
        return new external_function_parameters(array(
            'items' => new external_value(PARAM_TEXT, 'items serialized')
        ));
    }

    /**
     * Returns status
     * @return array user data
     */
    public static function set_self_assess($items) {
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::set_self_assess_parameters(),
            array('items' => $items));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);

        //$request = new groupactivity\request($courseid, $userid);
        //$data = $request->get_group_posts_data();

        $params = explode('&', $items);
        $items = [];
        foreach ($params as $param) {
            list($id, $value) = explode('=', $param);

            if ($id != 'instance') {
                $items[] = ['id' => $id, 'value' => $value];
            } else {
                $instance = $value;
            }
        }

        $context = context_block::instance($instance);
        $event = \block_groupactivity\event\groupactivity_selfassess_completed::create(array('contextid' => $context->id, 'other' => array('items' => $items)));

        $event->trigger();

        global $CFG;
        $logfile = $CFG->dirroot . '/blocks/groupactivity/req.log';
        file_put_contents($logfile, 'items: ' . serialize($event) . PHP_EOL . PHP_EOL, FILE_APPEND);

        $success = true;

        return ['success' => $success];
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function set_self_assess_returns() {
        return new \external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL)
            ), 'set self assess response');
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_selfassess_items_parameters() {
        return new external_function_parameters(array(
            'instance' => new external_value(PARAM_INT, 'block instance id')
        ));
    }

    /**
     * Returns status
     * @return array user data
     */
    public static function get_selfassess_items($instance) {
        global $DB;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_selfassess_items_parameters(),
            array('instance' => $instance));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = \context_system::instance();
        self::validate_context($context);

        $items = $DB->get_records('groupactivity_items', ['blockinstance' => $instance, 'deleted' => 0]);
        $items_data = [];
        $item_counter = 1;
        foreach ($items as $item) {
            $skala = [];

            for ($i = 0; $i <= $item->length; $i++) {
                if ($i == 0) {
                    $skala_item = [
                        'value' => $i,
                        'label' => get_string('not-specified', 'block_groupactivity'),
                        'notspecified' => true
                    ];
                } else {
                    $skala_item = [
                        'value' => $i,
                        'label' => $i,
                        'notspecified' => false
                    ];
                }

                array_push($skala, $skala_item);
            }

            $i_data = [
                'id' => $item->id,
                'content' => $item->content,
                'skala' => $skala,
                'counter' => $item_counter++
            ];

            array_push($items_data, $i_data);
        }

        return $items_data;
    }

    /**
     * Returns description of method result value
     * @return external_multiple_structure
     */
    public static function get_selfassess_items_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_TEXT, 'item id'),
                    'content' => new external_value(PARAM_TEXT, 'item content'),
                    'skala' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'value' => new external_value(PARAM_INT, 'skala item value'),
                                'label' => new external_value(PARAM_TEXT, 'skala item label'),
                                'notspecified' => new external_value(PARAM_BOOL, 'skala item specified'),
                            )
                        ), 'skala'),
                    'counter' => new external_value(PARAM_INT, 'item counter'),
                )
            )
        );

    }
}
