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
global $PAGE, $OUTPUT, $DB;

$blockinstance = required_param('blockinstance', PARAM_INT);

require_login();

$url = new moodle_url('/blocks/groupactivity/items/view.php');

$context = context_block::instance($blockinstance);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(format_string(get_string('pluginname', 'block_groupactivity')));

$items = $DB->get_records('groupactivity_items', ['blockinstance' => $blockinstance, 'deleted' => 0]);
$items = array_values($items);

$course = $DB->get_record_sql('SELECT c.instanceid FROM {context} as c JOIN {block_instances} as bc ON c.id = bc.parentcontextid WHERE bc.id = ?', [$blockinstance]);

$data = array(
    'hasitems' => count($items),
    'items' => $items,
    'editurl' => (new moodle_url('/blocks/groupactivity/items/item.php'))->out(),
    'deleteurl' => (new moodle_url('/blocks/groupactivity/items/item.php?delete=1&blockinstance=' . $blockinstance))->out(),
    'newurl' => (new moodle_url('/blocks/groupactivity/items/item.php', ['blockinstance' => $blockinstance]))->out(),
    'backurl' => (new moodle_url('/course/view.php', ['id' => $course->instanceid]))->out()
);

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_groupactivity/items_view', $data);

echo $OUTPUT->footer();