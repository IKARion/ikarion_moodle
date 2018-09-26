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
        $this->content->footer = '';

        $prompts = array(
            $this->config->prompt_1,
            $this->config->prompt_2,
            $this->config->prompt_3,
            $this->config->prompt_4
        );

        $courseid = $this->page->course->id;
        $role = grouplatency\get_role($courseid);

        switch ($role) {
            case 'mirroring_guiding':
                // guiding TODO

                // mirroring
                if (grouplatency\show_mirroring($courseid)) {
                    $data = grouplatency\get_group_posts_data($courseid);

                    $template_data['data'] = $data;
                    $template_data['mirroring'] = 1;
                    $this->page->requires->js_call_amd('block_grouplatency/grouplatency', 'init', [$data]);
                    break;
                } else {
                    $template_data['text'] = 'Derzeit gibt es keine neuen Informationen zu eurer Gruppenarbeit.';
                    $template_data['default'] = 1;
                    break;
                }

            case 'mirroring':
                if (grouplatency\show_mirroring($courseid)) {
                    $data = grouplatency\get_group_posts_data($courseid);

                    $template_data['data'] = $data;
                    $template_data['mirroring'] = 1;
                    $this->page->requires->js_call_amd('block_grouplatency/grouplatency', 'init', [$data]);
                    break;
                } else {
                    $template_data['text'] = 'Derzeit gibt es keine neuen Informationen zu eurer Gruppenarbeit.';
                    $template_data['default'] = 1;
                    break;
                }

            case 'guiding':
                $template_data['text'] = $prompts[rand(0, 3)];
                $template_data['guide'] = 1;

                break;
            default :
                $template_data['none'] = 1;
                break;
        }

        $out = $OUTPUT->render_from_template('block_grouplatency/main', $template_data);

        $this->content->text = $out;
        $this->content->footer = '';

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