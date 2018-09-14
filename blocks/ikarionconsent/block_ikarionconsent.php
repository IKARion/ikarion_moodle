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
 * Block ikarionconsent.
 *
 * @package    block_ikarionconsent
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author       Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_ikarionconsent extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_ikarionconsent');
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('pluginname', 'block_ikarionconsent'));
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $USER, $OUTPUT, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        $policy_text = $this->config->policy;
        $record = $DB->get_record('ikarionconsent', array('userid' => $USER->id));

        if (!$record) {
            $this->page->requires->js_call_amd('block_ikarionconsent/modal', 'init', array($policy_text, 1));
        } else {
            if($record->choice == 1) {
                $selected = 'yes';
            } else {
                $selected = 'no';
            }

            $this->page->requires->js_call_amd('block_ikarionconsent/modal', 'init', array($policy_text, 0,
                'selected' => [$selected => $selected]));
        }

        $out = $OUTPUT->render_from_template('block_ikarionconsent/modalbutton', '');

        $this->content->text = $out;

        return $this->content;
    }
}