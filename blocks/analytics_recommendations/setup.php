<?php  
//// This file is part of Moodle - http://moodle.org/
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
 * Analytics Recommendations block
 *
 * @package    contrib
 * @subpackage block_analytics_recommendations
 * @copyright  2012 Cristina Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('libs.php');

// Course id
$id = optional_param('id',0,PARAM_INT);  
if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/setup.php', array('id' => $course->id)));

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
require_capability('block/analytics_recommendations:init', $context);

// To update the log
add_to_log($course->id, 'analytics', 'setup', 'setup.php?id='.$course->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Cursos que tienen el modulo analytics_recommendations instalado
$courses=module_maintenance::get_courses();  
unset($courses[$COURSE->id]);
$courses[0]=get_string('none','block_analytics_recommendations');

// Form
$mform=new setup_form(null, array('id'=>$id, 'courses'=>$courses));
/*******************************************************************************/

// Cancel button
if ($mform->is_cancelled()){
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}else{  
    // Navigation links
    $navlinks = array();
    $navlinks[] = array('name' => get_string('analytics_recommendations','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
    #$navigation = build_navigation($navlinks);
    
    //Header
    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    
    // Page title
    echo $OUTPUT->heading(get_string('initiate_follow-up','block_analytics_recommendations'));
    // Accept button
    if ($data = $mform->get_data()){
        // Create and initialize tables
        $course_info= new course_info($course);
        $module_maintenance=new module_maintenance($course_info);       
        $module_maintenance->create_table();
        $module_maintenance->insert_users();
        $module_maintenance->create_table_total();
        $module_maintenance->insert_reference_course($data->ref_course);
        $module_maintenance->init_table_total(time());
        $module_maintenance->init_table();        
        // Show messaje OK
        echo $OUTPUT->box(get_string('follow-up_ok','block_analytics_recommendations'),'generalbox','notice');
        //display continue button
        $course_page = new moodle_url('/course/view.php?id='.$course->id);
        $continuebutton = $OUTPUT->render(new single_button($course_page, get_string('continue', 'hub')));
        $continuebutton = html_writer::tag('div', $continuebutton, array('class' => 'mdl-align'));
        echo $continuebutton;
    }else{
        // Form
        $mform->display();
    }
    // Page footer
    echo $OUTPUT->footer();
}
?>