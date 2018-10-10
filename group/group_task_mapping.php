<?php
/**
 * Created by PhpStorm.
 * User: Yassin
 * Date: 11.09.2018
 * Time: 09:34
 */

require_once('../config.php');
require_once('lib.php');
require_once('group_task_mapping_form.php');


$courseid = optional_param('courseid', false, PARAM_INT);
//$data = $editform->get_data();
//if(!$courseid) {
//    $courseid = $data->form_courseid;
//}

if (isset($_POST["form_courseid"])){
    $courseid = $_POST["form_courseid"];
}

$PAGE->set_url('/group/group_task_mapping.php', array('courseid' => $courseid));

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

if (!$course) {
    print_error('invalidcourseid');
}

require_login($course);
$context       = context_course::instance($courseid);
require_capability('moodle/course:managegroups', $context);
$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id;

// str values for page
$strgroups           = get_string('groups');
$group_task_mapping_str = get_string('grouptaskmapping', 'group');


$editform = new group_task_mapping_form(null, array('courseid' => $courseid));

// Redirect
if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()){

    $group_id = $data->group_id;
    $task_id = $data->task_id;

    $transaction = $DB->start_delegated_transaction();
//    redirect(new moodle_url('/group/group_task_mapping_edit.php',
//        array('courseid' => $courseid, 'group_id' => $group_id )));
    $gt_table = 'group_task_mapping';
    $DB->delete_records($gt_table, array('groupid' => $group_id));
    if($task_id != -1) {
        $record = new stdClass();
        $record->groupid = $group_id;
        $record->taskid = $task_id;
        $DB->insert_record($gt_table,$record);
    }
    $transaction->allow_commit();
    redirect($returnurl);
}

//Print Page and Form

$PAGE->set_title($group_task_mapping_str);
$PAGE->set_heading($course->fullname. ': '.$group_task_mapping_str);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/group/index.php', array('id' => $courseid)));

$PAGE->navbar->add($group_task_mapping_str);

echo $OUTPUT->header();
echo $OUTPUT->heading($group_task_mapping_str);

$editform->display();


echo $OUTPUT->footer();
