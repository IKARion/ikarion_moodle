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

        $guiding_config = array(
            $this->config->level_0 => $this->config->level_0_text,
            $this->config->level_1 => $this->config->level_1_text,
            $this->config->level_2 => $this->config->level_2_text,
        );

        $data = groupactivity\get_data();
        $users = array(
            0 => array(
                'name' => 'Eugen Ebel',
                'online' => 1,
                'mailto' => 'eugen.ebel@fh-luebeck.de',
                'color' => '#a05d56'
            ),
            1 => array(
                'name' => 'Max Muster',
                'online' => 0,
                'mailto' => 'max.muster@fh-luebeck.de',
                'color' => '#d0743c'
            ),
            2 => array(
                'name' => 'Jenny Johe',
                'online' => 1,
                'mailto' => 'jenny.johe@fh-luebeck.de',
                'color' => '#ff8c00'
            )
        );
        $display = 'mirroring_guiding';

        switch ($display) {
            case 'mirroring_guiding':
                $template_data['text'] = groupactivity\get_guiding_text($guiding_config, count($data));
                $template_data['users'] = $users;

                $template_data['guide'] = 1;
                $template_data['mirroring'] = 1;
                $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data]);
                break;

            case 'mirroring':
                $template_data['mirroring'] = 1;
                $template_data['users'] = $users;

                $this->page->requires->js_call_amd('block_groupactivity/groupactivity', 'init', [$data]);
                break;

            case 'guiding':
                $template_data['text'] = groupactivity\get_guiding_text($guiding_config, count($data));
                $template_data['guide'] = 1;

                break;
            case 'nothing' :
                $template_data['nothing'] = 1;
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