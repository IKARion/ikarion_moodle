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

require_once ('libs.php');

// Course id
$id = required_param('id',PARAM_INT);  

// id del alumno
$user = required_param('user',PARAM_INT); 

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

if (! $user = $DB->get_record("user", array('id'=>$user))) {
    error("User id is incorrect.");
}

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/recommendations2.php', array('id' => $course->id, 'user' => $user->id)));

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_recommendations', 'my_recommendations.php?id='.$course->id.'&user='.$user->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_recommendations=new recommendations($course_info);
$reference_course_info=$my_recommendations->get_reference_course_info();

$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

$mods=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();

// Current user summary
$resumen_user_actual=$my_recommendations->get_course_summary($user);
unset($resumen_user_actual['grade']);

$aprobados=$my_recommendations->get_summary_to_pass();
$aprobados_normal=$my_recommendations->get_reference_course_normal_summary($aprobados);
$esfuerzo_aprobar=$my_recommendations->get_effort_to_pass($user);

/*******************************************************************************/

// Navigation links
$navlinks = array();
if (has_capability('block/analytics_recommendations:viewglobal', $context))
    $navlinks[] = array('name' => get_string('all_recommendations','block_analytics_recommendations'), 'link' => $CFG->wwwroot.'/blocks/analytics_recommendations/all_recommendations.php?id='.$course->id, 'type' => 'misc');
$navlinks[] = array('name' => get_string('my_recommendations','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

//Header
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Tabs
$tabs=$gui->get_my_recommendations_tabs($user,$course);
print_tabs($tabs, '', array('to_pass'), array('to_pass'));

// Page title
echo $OUTPUT->heading(get_string('need_to_pass','block_analytics_recommendations'));

// Information box
echo $OUTPUT->box(get_string('recommendations2_message','block_analytics_recommendations'),'generalbox','notice');

// Table data
$table_data=array(array());
$colours=array(array());

$table_data[0][0]='';
$i=1;
foreach ($resumen_user_actual as $key=>$value) {            
    $table_data[0][$i]=$OUTPUT->pix_icon('icon',$modnamesplural[$key], $key).'&nbsp;&nbsp;'.$modnamesplural[$key];
    $i++;            
}

$table_data[1][0]=format_string($user->firstname)." ".format_string($user->lastname);
$i=1;
foreach ($resumen_user_actual as $key=>$value) {            
    $table_data[1][$i]=round($value,2).'%';
    $colours[1][$i]='#6495ED';
    $i++;            
}

$table_data[2][0]=get_string('participation_to_pass','block_analytics_recommendations');
$i=1;
foreach ($resumen_user_actual as $key=>$value) {
    if (isset($aprobados_normal[$key]))
        $table_data[2][$i]=round($aprobados_normal[$key]*100/$num_users,2).'%';
    else 
        $table_data[2][$i]='0%';
     $colours[2][$i]='#B0E0E6';
    $i++;           
}

$table_data[3][0]=get_string('effort','block_analytics_recommendations');
$i=1;
foreach ($resumen_user_actual as $key=>$value) {
    $table_data[3][$i]=$esfuerzo_aprobar[$key].'%';
    $colours[3][$i]='#FFA23C';
    $i++;                                                       
}    

// Display table data        
echo html_writer::table($gui->get_table($table_data,$colours,true));       

// Graphs
echo $gui->get_recommendations_graphs($course, $user, 2, 3);

// Reference course table
echo html_writer::table($gui->get_reference_course_table($reference_course_info->get_course()->fullname,$reference_course_info->get_course()->shortname));

// Page footer
echo $OUTPUT->footer();
?>