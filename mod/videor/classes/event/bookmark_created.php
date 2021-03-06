<?php
/**
 * Created by PhpStorm.
 * User: hecking
 * Date: 30.03.2015
 * Time: 09:59
 */

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
 * The EVENTNAME event.
 *
 * @package    FULLPLUGINNAME
 * @copyright  2014 Tobias Hecking
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_videor\event;
defined('MOODLE_INTERNAL') || die();
/**
 * The EVENTNAME event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - PUT INFO HERE
 * }
 *
 * @since     Moodle MOODLEVERSION
 * @copyright 2014 YOUR NAME
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class EVENTNAME extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'videor';
    }

    public static function get_name() {
        return get_string('eventbookmarkcreated', 'bookmark_created');
    }

    public function get_description() {
        return "The user with id {$this->userid} created a bookmark with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/mod/videor/addbookmark.php', array('parameter' => 'id', $this->objectid));
    }

    public function get_legacy_logdata() {
        // Override if you are migrating an add_to_log() call.
        return array($this->courseid, 'videor', 'videor',
            'bookmark created',
            $this->objectid, $this->contextinstanceid);
    }

    public static function get_legacy_eventname() {
        // Override ONLY if you are migrating events_trigger() call.
        return 'add bookmark';
    }

    protected function get_legacy_eventdata() {
        // Override if you migrating events_trigger() call.
        $data = new \stdClass();
        $data->id = $this->objectid;
        $data->userid = $this->relateduserid;
        return $data;
    }
}