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
 * Prints a particular instance of groupformation
 *
 * @package mod_groupformation
 * @author Eduard Gallwas, Johannes Konert, Rene Roepke, Nora Wester, Ahmed Zukic
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require('header.php');

// Get data for HTML output.
require_once($CFG->dirroot . '/mod/groupformation/classes/moodle_interface/storage_manager.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/controller/grouping_controller.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/view_controller/grouping_view_controller.php');

$filename = substr(__FILE__, strrpos(__FILE__, '\\') + 1);
$url = new moodle_url('/mod/groupformation/' . $filename, $urlparams);

// Set PAGE config.
$PAGE->set_url($url);
$PAGE->set_title(format_string($groupformation->name));
$PAGE->set_heading(format_string($course->fullname));

// Import jQuery and js file.
groupformation_add_jquery($PAGE, 'survey_functions.js');

if (!has_capability('mod/groupformation:editsettings', $context)) {
    $returnurl = new moodle_url ('/mod/groupformation/view.php', array(
            'id' => $id, 'do_show' => 'view'));
    redirect($returnurl);
} else {
    $currenttab = $doshow;
}

$store = new mod_groupformation_storage_manager ($groupformation->id);
$controller = new mod_groupformation_grouping_controller($groupformation->id, $cm);

if ((data_submitted()) && confirm_sesskey()) {
    $start = optional_param('start', null, PARAM_BOOL);
    $abort = optional_param('abort', null, PARAM_BOOL);
    $adopt = optional_param('adopt', null, PARAM_BOOL);
    $edit = optional_param('edit', null, PARAM_BOOL);
    $delete = optional_param('delete', null, PARAM_BOOL);

    if (isset ($start) && $start == 1) {
        $controller->start($cm);
    } else if (isset ($abort) && $abort == 1) {
        $controller->abort();
    } else if (isset ($adopt) && $adopt == 1) {
        $controller->adopt();
    } else if (isset ($edit) && $edit == 1) {
        $controller->edit($cm);
    } else if (isset ($delete) && $delete == 1) {
        $controller->delete();
    }
    $returnurl = new moodle_url ('/mod/groupformation/grouping_view.php', array(
        'id' => $id, 'do_show' => 'grouping'));
    redirect($returnurl);
}

require('research_actions.php');

echo $OUTPUT->header();

// Print the tabs.
require('tabs.php');

echo $debugbuttons;

if (groupformation_get_current_questionnaire_version() > $store->get_version()) {
    echo '<div class="alert">'.get_string('questionnaire_outdated', 'groupformation').'</div>';
}
if ($store->is_archived() && has_capability('mod/groupformation:editsettings', $context)) {
    echo '<div class="alert" id="commited_view">'.get_string('archived_activity_admin', 'groupformation').'</div>';
} else {
    groupformation_check_for_cron_job();

    echo '<form action="'.htmlspecialchars($_SERVER ["PHP_SELF"]).'" method="post" autocomplete="off">';

    echo '<input type="hidden" name="id" value="'.$id.'"/>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

    $viewcontroller = new mod_groupformation_grouping_view_controller($groupformation->id, $controller);
    echo $viewcontroller->render();

    echo '</form>';
}
echo $OUTPUT->footer();
