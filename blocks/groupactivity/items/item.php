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

require_once('../../../config.php');
require_once('item_form.php');
global $PAGE, $OUTPUT, $DB;

$blockinstance = optional_param('blockinstance', '', PARAM_INT);
$itemid = optional_param('id', '', PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

require_login();

$url = new moodle_url('/blocks/groupactivity/items/item.php');
$viewurl = new moodle_url('/blocks/groupactivity/items/view.php', ['blockinstance' => $blockinstance]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title(format_string(get_string('pluginname', 'block_groupactivity')));

if (!empty($itemid) && $delete === 1) {
    $record = $DB->get_record('groupactivity_items', array('id' => $itemid));
    $record->deleted = 1;

    $DB->update_record('groupactivity_items', $record);

    redirect($viewurl, 'Item erfolgreich gelöscht');
}

$itemform = new groupactivity\form\item_form(null, ['blockinstance' => $blockinstance]);

//Form processing and displaying is done here
if ($itemform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($viewurl);
} else if ($data = $itemform->get_data()) {
    unset($data->submitbutton);

    $data->timemodified = time();

    if (isset($data->id) && !empty($data->id)) {
        $DB->update_record('groupactivity_items', $data);

        redirect($viewurl, 'Item erfolgreich aktualisiert');

    } else {
        $insertid = $DB->insert_record('groupactivity_items', $data);

        if ($insertid) {
            redirect($viewurl, 'Item erfolgreich angelegt');
        } else {
            redirect($viewurl, 'Beim Anlegen ist ein Fehler aufgetreten. Bitte kontaktieren Sie den Administrator.');
        }
    }
} else {
    if (isset($itemid) && !empty($itemid)) {
        $task_data = $DB->get_record('groupactivity_items', array('id' => $itemid));

        $itemform->set_data($task_data);
    }
}

echo $OUTPUT->header();

$itemform->display();

echo $OUTPUT->footer();