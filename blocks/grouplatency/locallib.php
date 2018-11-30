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

namespace grouplatency;

function trigger_event($contextid, $instance, $showguiding, $message) {
    global $DB, $USER;

    $record = $DB->get_record('grouplatency', array('blockinstance' => $instance, 'userid' => $USER->id));
    $type = $prompt = ($showguiding) ? 'note' : 'default';
    $event = \block_grouplatency\event\grouplatency_prompt_viewed::create(array('contextid' => $contextid, 'other' => array('type' => $type, 'message' => $message)));

    if ($record) {
        $prompt = ($showguiding) ? 1 : 0;

        if ($record->prompt != $prompt) {
            $record->prompt = $prompt;

            $DB->update_record('grouplatency', $record);
            $event->trigger();
        }
    } else {
        $entry = new \stdClass();
        $entry->userid = $USER->id;
        $entry->blockinstance = $instance;
        $entry->prompt = ($showguiding) ? 1 : 0;
        $entry->timemodified = time();

        $DB->insert_record('grouplatency', $entry);
        $event->trigger();
    }
}