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
$user=required_param('user',PARAM_INT);

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

if (! $user = $DB->get_record("user", array('id'=>$user))) {
    error("User id is incorrect.");
}

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/analytics2.php', array('id' => $course->id, 'user' => $user->id)));

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_analytics', 'analytics2.php?id='.$course->id.'&user='.$user->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);

$resultados=$my_analytics->get_my_analytics($user);

$medias=$my_analytics->get_average_analytics();

$modulos=$course_info->get_used_mods();

$modnames=$course_info->get_used_mod_names();
$modnamesplural=$course_info->get_mod_names_plural();

$defaultmod = optional_param('defaultmod',$modulos[0],PARAM_FILE); // id modulo

$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

$section=$course_info->get_course_format();
/*******************************************************************************/

// Navigation links
$navlinks = array();
if (has_capability('block/analytics_recommendations:viewglobal', $context))
    $navlinks[] = array('name' => get_string('all_analytics','block_analytics_recommendations'), 'link' => $CFG->wwwroot.'/blocks/analytics_recommendations/all_analytics.php?id='.$course->id, 'type' => 'misc');
$navlinks[] = array('name' => get_string('single_analytics','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

//Header
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Tabs
$tabs = $gui->get_single_analytics_tabs($user,$course);
print_tabs($tabs, '', array('a12'), array('a12'));

// Page title
echo $OUTPUT->heading($OUTPUT->pix_icon('icon',$modnamesplural[$defaultmod], $defaultmod).'&nbsp;'.get_string('participation_in','block_analytics_recommendations').$modnamesplural[$defaultmod].': '.format_string($user->firstname)." ".format_string($user->lastname));

// Information box
echo $OUTPUT->box(get_string('analytics2_message','block_analytics_recommendations'),'generalbox','notice');

// Form
$mform=new defaultmod_form(null, array('id'=>$id, 'user'=>$user->id,'mods'=>$modnames,'default'=>$defaultmod),'get');
$mform->display();

// Table data
$table_data=array(array());
$colours=array(array());
$table_data[0][0]='';

for($j=0;$j<=$course_info->get_numsections();$j++)
    $table_data[0][$j+1]=$section.'&nbsp;'.$j; 

$table_data[1][0]=format_string($user->firstname)." ".format_string($user->lastname);

for($j=0;$j<=$course_info->get_numsections();$j++){
	if (isset($resultados[$defaultmod][$j]))
            $table_data[1][$j+1]=$resultados[$defaultmod][$j].'%';
	else
            $table_data[1][$j+1]='';
        if (isset($resultados[$defaultmod][$j]))
            $colours[1][$j+1]=$gui->get_colour_cell($resultados[$defaultmod][$j],$num_users); 
}
    
   
$table_data[2][0]=get_string('participation_average','block_analytics_recommendations').':&nbsp;'.$OUTPUT->pix_icon('icon',$modnamesplural[$defaultmod], $defaultmod).'&nbsp;'.$modnamesplural[$defaultmod];

for($j=0;$j<=$course_info->get_numsections();$j++){
    if (isset($medias[$defaultmod][$j]))
        $table_data[2][$j+1]=$medias[$defaultmod][$j].'%';
    else
        $table_data[2][$j+1]='';
    if (isset($medias[$defaultmod][$j]))
    $colours[2][$j+1]=$gui->get_colour_cell($medias[$defaultmod][$j], $num_users);
}
    	
// Display table data
echo html_writer::table($gui->get_table($table_data,$colours,true,true));

// Graph
echo $gui->get_analytics_graph($course, 1, $user, $defaultmod);

// Page footer
echo $OUTPUT->footer();
?>