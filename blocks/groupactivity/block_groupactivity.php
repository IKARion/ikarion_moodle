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

require_once($CFG->dirroot . '/blocks/groupactivity/classes/request.php');
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
        $request = new groupactivity\request($courseid, $USER->id);
        $role = $request->getrole();
        $out = null;

        switch ($role) {
            case 'mgo':
                //guiding
                if ($request->showguiding()) {
                    $template_data['text'] = $prompts[rand(0, 3)];
                    $template_data['guide'] = 1;
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                }

                groupactivity\trigger_event($this->context->id, $this->instance->id, $request->showguiding(), $template_data['text']);

                //mirroring
                $data = $request->get_group_activity_data();
                $template_data['mirroring'] = 1;
                $template_data['usersdata'] = $data['usersdata'];
                $template_data['mailtogroup'] = $data['mailtogroup'];

                if ($courseid == 2) {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivitynew', 'init', [$data['usersdata']]);
                } else {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                }

                $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);

                break;

            case 'mgma':
                //guiding
                if ($request->showguiding()) {
                    $template_data['text'] = $prompts[rand(0, 3)];
                    $template_data['guide'] = 1;
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                }

                groupactivity\trigger_event($this->context->id, $this->instance->id, $request->showguiding(), $template_data['text']);

                //mirroring
                $data = $request->get_group_activity_data();
                $template_data['mirroring'] = 1;
                $template_data['usersdata'] = $data['usersdata'];
                $template_data['mailtogroup'] = $data['mailtogroup'];

                if ($courseid == 2) {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivitynew', 'init', [$data['usersdata']]);
                } else {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                }

                $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);

                $out = $OUTPUT->render_from_template('block_groupactivity/mgma', $template_data);

                break;

            case 'mgmb':
                //guiding
                if ($request->showguiding()) {
                    $template_data['text'] = $prompts[rand(0, 3)];
                    $template_data['guide'] = 1;
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                }

                groupactivity\trigger_event($this->context->id, $this->instance->id, $request->showguiding(), $template_data['text']);

                //mirroring
                $data = $request->get_group_activity_data();
                $template_data['mirroring'] = 1;
                $template_data['usersdata'] = $data['usersdata'];
                $template_data['mailtogroup'] = $data['mailtogroup'];

                if ($courseid == 2) {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivitynew', 'init', [$data['usersdata']]);
                } else {
                    $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                }

                $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);

                $out = $OUTPUT->render_from_template('block_groupactivity/mgmb', $template_data);

                break;

            case 'mirroring_guiding':
                //guiding
                if ($request->showguiding() && $request->activitycount() > 3) {
                    $template_data['text'] = $prompts[rand(0, 3)];
                    $template_data['guide'] = 1;
                    groupactivity\trigger_event($this->context->id, $this->instance->id, $request->showguiding(), $template_data['text']);
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                    groupactivity\trigger_event($this->context->id, $this->instance->id, false, $template_data['text']);
                }

                //mirroring
                if ($request->activitycount() > 3) {
                    $data = $request->get_group_activity_data();
                    $template_data['mirroring'] = 1;
                    $template_data['usersdata'] = $data['usersdata'];
                    $template_data['mailtogroup'] = $data['mailtogroup'];

                    if ($courseid == 2) {
                        $this->page->requires->js_call_amd('block_groupactivity/groupactivitynew', 'init', [$data['usersdata']]);
                    } else {
                        $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                    }

                    $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);
                }
                break;

            case 'mirroring':
                if ($request->activitycount() > 3) {
                    $data = $request->get_group_activity_data();
                    $template_data['mirroring'] = 1;
                    $template_data['usersdata'] = $data['usersdata'];
                    $template_data['mailtogroup'] = $data['mailtogroup'];

                    if ($courseid == 2) {
                        $this->page->requires->js_call_amd('block_groupactivity/groupactivitynew', 'init', [$data['usersdata']]);
                    } else {
                        $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data['usersdata']]);
                    }

                    $this->page->requires->js_call_amd('block_groupactivity/forumactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/wikiactivity', 'init', [$data['usersdata']]);
                    $this->page->requires->js_call_amd('block_groupactivity/charthandler', 'init', []);
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                }
                break;

            case 'guiding':
                if ($request->showguiding() && $request->activitycount() > 3) {
                    $template_data['text'] = $prompts[rand(0, 3)];
                    $template_data['guide'] = 1;
                    groupactivity\trigger_event($this->context->id, $this->instance->id, $request->showguiding(), $template_data['text']);
                } else {
                    $template_data['text'] = get_string('noactivity', 'block_groupactivity');
                    $template_data['default'] = 1;
                    groupactivity\trigger_event($this->context->id, $this->instance->id, false, $template_data['text']);
                }
                break;
            default:
                $template_data['nothing'] = 1;
                break;
        }

        if ($out == null) {
            $out = $OUTPUT->render_from_template('block_groupactivity/main', $template_data);
        }

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