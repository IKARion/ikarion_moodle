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
$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/analytics3.php', array('id' => $course->id, 'user' => $user->id)));

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_analytics', 'analytics3.php?id='.$course->id.'&user='.$user->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);

$resultados=$my_analytics->get_my_analytics_by_module($user);

$medias=$my_analytics->get_average_analytics_by_module();

$modulos=$course_info->get_used_mods();

$modnames=$course_info->get_used_mod_names();
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
print_tabs($tabs, '', array('a13'), array('a13'));

// Page title
echo $OUTPUT->heading(get_string('average_participation_all_activities','block_analytics_recommendations').': '.format_string($user->firstname)." ".format_string($user->lastname));

// Information box
echo $OUTPUT->box(get_string('analytics3_message','block_analytics_recommendations'),'generalbox','notice');

// Table data
$table_data=array(array());
$colours=array(array());
$table_data[0][0]='';

for($i=0;$i<count($modulos);$i++)
    $table_data[0][$i+1]=$OUTPUT->pix_icon('icon',$modnamesplural[$modulos[$i]], $modulos[$i]).'&nbsp;&nbsp;<a href="analytics2.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$modulos[$i].'">'.$modnamesplural[$modulos[$i]].'</a>';
//$table_data[0][$i+1]=$OUTPUT->pix_icon('icon',$modnamesplural[$modulos[$i]], $modulos[$i]).'&nbsp;&nbsp;<a href="analytics2.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$modulos[$i].'">'.$modnamesplural[$modulos[$i]].'</a>';

$table_data[1][0]=format_string($user->firstname)." ".format_string($user->lastname);
for($i=0;$i<count($modulos);$i++){
    if (isset($resultados[$modulos[$i]]))
        $table_data[1][$i+1]=$resultados[$modulos[$i]].'%';
    else
        $table_data[1][$i+1]=$resultados[$modulos[$i]].'%';
    $colours[1][$i+1]=$gui->get_colour_cell($resultados[$modulos[$i]], $num_users);
}
    

$table_data[2][0]=get_string('participation_average','block_analytics_recommendations');

for($i=0;$i<count($modulos);$i++){
    $table_data[2][$i+1]=$medias[$modulos[$i]].'%';
    $colours[2][$i+1]=$gui->get_colour_cell($medias[$modulos[$i]], $num_users);
}
    

// Display table data
echo html_writer::table($gui->get_table($table_data,$colours,true,true));

// Graph
echo $gui->get_analytics_graph($course, 2, $user);

// Page footer
echo $OUTPUT->footer();
?>