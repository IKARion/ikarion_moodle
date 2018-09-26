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

require_once($CFG->dirroot . '/blocks/groupactivity/locallib.php');

class block_groupactivity extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_groupactivity');
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
        $role = groupactivity\get_role($courseid);

        switch ($role) {
            case 'mirroring_guiding':
                //guiding TODO

                //mirroring
                if (groupactivity\show_mirroring($courseid)) {
                    $data = groupactivity\get_group_activity_data($courseid);
                    $template_data['mirroring'] = 1;
                    $template_data['usersdata'] = $data['usersdata'];
                    $template_data['mailtogroup'] = $data['mailtogroup'];

                    $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);
                    break;
                } else {
                    $template_data['text'] = 'Derzeit gibt es keine neuen Informationen zu eurer Gruppenarbeit.';
                    $template_data['default'] = 1;
                    break;
                }

            case 'mirroring':
                if (groupactivity\show_mirroring($courseid)) {
                    $data = groupactivity\get_group_activity_data($courseid);
                    $template_data['mirroring'] = 1;
                    $template_data['usersdata'] = $data['usersdata'];
                    $template_data['mailtogroup'] = $data['mailtogroup'];

                    $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);
                    break;
                } else {
                    $template_data['text'] = 'Derzeit gibt es keine neuen Informationen zu eurer Gruppenarbeit.';
                    $template_data['default'] = 1;
                    break;
                }

            case 'guiding':
                $template_data['text'] = $prompts[rand(1, 4)];
                $template_data['guide'] = 1;

                break;
            default:
                $template_data['text'] = 'Derzeit gibt es keine neuen Informationen zu eurer Gruppenarbeit.';
                $template_data['default'] = 1;
                break;
        }

        $out = $OUTPUT->render_from_template('block_groupactivity/main', $template_data);

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