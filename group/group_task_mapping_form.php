<?php
/**
 * Created by PhpStorm.
 * User: Yassin
 * Date: 02.10.2018
 * Time: 10:23
 */

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/cohort/lib.php');

class group_task_mapping_form extends moodleform
{
    function definition()
    {
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;
        $courseid = $this->_customdata["courseid"];
        // Needed Strings
        $group_task_mapping_str = get_string('grouptaskmapping', 'group');
        $group_task_str = get_string('taskname', 'group');

        $params = array('courseid' => $courseid);

        $groups_sql = "SELECT
    g.id, g.name
FROM
    mdl_groups g
WHERE
    g.courseid = :courseid
;";


        $groups_with_tasks_sql = "SELECT
    g.id, g.name, gtm.taskid, gt.taskname
FROM
    mdl_group_task_mapping gtm,
    mdl_groups g,
    mdl_group_task gt
WHERE
    g.courseid = :courseid AND g.id = gtm.groupid AND gt.id = gtm.taskid";

        $tasks_sql = "SELECT
    gt.id, gt.taskname
FROM
    mdl_group_task gt
WHERE
    gt.course = :courseid";

        $groups = $DB->get_records_sql($groups_sql, $params);
        $groups_with_tasks = $DB->get_records_sql($groups_with_tasks_sql, $params);
        $tasks = $DB->get_records_sql($tasks_sql, $params);

        $group_form_options = array();


        foreach ($groups as $group){
            $gid = (int)$group->id;
            $gtaskname = 'No Task';
            if(isset($groups_with_tasks[$gid])){
                $gt_record = $groups_with_tasks[$gid];
                $gtaskname = $gt_record->taskname;
            }
            $group_form_options[$gid] = $group->name.' - '.$gtaskname;
        }

        $task_form_options = array(-1 => "No Task");
        foreach($tasks as $task){
            $task_form_options[$task->id] = $task->taskname;
        }



        $mform->addElement("hidden", "form_courseid", $courseid);
        $mform->setType("form_courseid", PARAM_INT);
        $mform->addElement('select', 'group_id', $group_task_mapping_str, $group_form_options);

        $mform->addElement('select', 'task_id', $group_task_str, $task_form_options);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

//        $this->add_action_buttons($submitlabel="select");

    }
}