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

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/analytics1.php', array('id' => $course->id, 'user' => $user->id)));

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_analytics', 'analytics1.php?id='.$course->id.'&user='.$user->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);
$resultados=$my_analytics->get_my_analytics($user);

$modulos=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();

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
print_tabs($tabs, '', array('a11'), array('a11'));

// Page title
echo $OUTPUT->heading(get_string('my_participation','block_analytics_recommendations').': '.format_string($user->firstname)." ".format_string($user->lastname));

// Information box
echo $OUTPUT->box(get_string('analytics1_message','block_analytics_recommendations'),'generalbox','notice');

// Table data
$table_data=array(array());
$colours=array(array());
$table_data[0][0]=get_string('activities','block_analytics_recommendations');
//Header
for($j=0;$j<=$course_info->get_numsections();$j++)
    $table_data[0][$j+1]=$section.'&nbsp;'.$j; 

for($i=0;$i<count($modulos);$i++){
    // // First column
   $table_data[$i+1][0]=$OUTPUT->pix_icon('icon',$modnamesplural[$modulos[$i]], $modulos[$i]).'&nbsp;&nbsp;<a href="analytics2.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$modulos[$i].'">'.$modnamesplural[$modulos[$i]].'</a>';
   for($j=0;$j<=$course_info->get_numsections();$j++){       
		if (isset($resultados[$modulos[$i]][$j]))
			$table_data[$i+1][$j+1]=$resultados[$modulos[$i]][$j].'%';  
		else
			$table_data[$i+1][$j+1]='';
                if (isset($resultados[$modulos[$i]][$j]))
                    $colours[$i+1][$j+1]=$gui->get_colour_cell($resultados[$modulos[$i]][$j],$num_users); 
    }
}

// Display table data
echo html_writer::table($gui->get_table($table_data,$colours,true));   

// Display table legend
echo html_writer::table($gui->get_legend_table());

// Page footer
echo $OUTPUT->footer();
?>