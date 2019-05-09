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

require_once($CFG->dirroot . '/blocks/grouplatency/classes/request.php');
require_once($CFG->dirroot . '/blocks/grouplatency/locallib.php');

class block_grouplatency extends block_base {

    public function init() {
        $this->title = $this->title = get_string('pluginname', 'block_grouplatency');
    }

    public function get_content() {
        global $OUTPUT, $USER, $SESSION, $CFG, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $OUTPUT->render_from_template('block_grouplatency/loading', []);
        $this->content->footer = '';

        $data = [
            'courseid' => $this->page->course->id,
            'userid' => $USER->id,
            'instanceid' => $this->context->instanceid
        ];

        $this->page->requires->js_call_amd('block_grouplatency/grouplatency-lazy', 'init', [$data]);

        return $this->content;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function applicable_formats() {
        return array('course-view' => true);
    }

    public function has_config() {
        return true;
    }

    public function hide_header() {
        return true;
    }
}
