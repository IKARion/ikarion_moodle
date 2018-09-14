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

$id = required_param('id',PARAM_INT);  
$ord = optional_param('ord', 'user', PARAM_TEXT);
$sent = optional_param('sent', '0', PARAM_INT);

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/all_recommendations.php', array('id' => $course->id)));


$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'all_recommendations', 'all_recommendations.php?id='.$course->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_recommendations=new recommendations($course_info);
$reference_course_info=$my_recommendations->get_reference_course_info();

$mods=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();

$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

$students=array();
foreach($coursestudents as $coursestudent){
   $user = $DB->get_record("user", array('id'=>$coursestudent));
   $students_names[$user->id]=$user->lastname.', '.$user->firstname;
   $expected_grades[$coursestudent]=$my_recommendations->get_estimated_grade($user);    
}

// Order the results
if ($ord=='user'){
    if ($sent=='0')
        asort($students_names);
    else
        arsort($students_names);
}else{
    if ($sent=='0')
        asort($expected_grades,SORT_NUMERIC);
    else    
        arsort($expected_grades,SORT_NUMERIC);
}

/*******************************************************************************/

// Navigation links
$navlinks = array();
$navlinks[] = array('name' => get_string('all_recommendations','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

//Header
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Page title
echo $OUTPUT->heading(get_string('all_recommendations','block_analytics_recommendations').': '.get_string('expected_grades','block_analytics_recommendations'));

// Information box
echo $OUTPUT->box(get_string('all_recommendations_message','block_analytics_recommendations'),'generalbox','notice');

$colours=array(array());
$table_data=array(array());
$table_data[0][0]=get_string('students','block_analytics_recommendations').'<br/><a href="?id='.$course->id.'&amp;ord=user&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;ord=user&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';
$table_data[0][1]=get_string('grade','block_analytics_recommendations').' / 10<br/><a href="?id='.$course->id.'&amp;ord=grade&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;ord=grade&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';
$table_data[0][2]=get_string('grade','block_analytics_recommendations').' % <br/><a href="?id='.$course->id.'&amp;ord=grade&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;ord=grade&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';

$i=1;
if ($ord=='user'){
    foreach($students_names as $userid => $username){ 
        $table_data[$i][0]='<a href="recommendations1.php?id='.$course->id.'&user='.$userid.'">'.$username.'</a>';
        $table_data[$i][1]=round($expected_grades[$userid]/10,2);
        $table_data[$i][2]=$expected_grades[$userid].'%';
        $colours[$i][1]=$gui->get_colour_cell_grade($expected_grades[$userid]);
        $colours[$i][2]=$colours[$i][1];
        $i++;
    }
}else{    
    foreach($expected_grades as $userid => $expected_grade){ 
        $table_data[$i][0]='<a href="recommendations1.php?id='.$course->id.'&user='.$userid.'">'.$students_names[$userid].'</a>';
        $table_data[$i][1]=($expected_grade/10);
        $table_data[$i][2]=$expected_grade.'%';
        $colours[$i][1]=$gui->get_colour_cell_grade($expected_grade);
        $colours[$i][2]=$colours[$i][1];
        $i++;
    }
}

// Display table data
echo html_writer::table($gui->get_table($table_data, $colours,true,false));

// Display table legend
echo html_writer::table($gui->get_legend_table_grade());

// Reference course table
echo html_writer::table($gui->get_reference_course_table($reference_course_info->get_course()->fullname,$reference_course_info->get_course()->shortname));

// Page footer
echo $OUTPUT->footer();
?>