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

// Hace referencia a los Users
$users=optional_param('users','',PARAM_TEXT);
  
$users=unserialize(urldecode($users));    

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/analytics5.php', array('id' => $course->id)));


$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_analytics', 'analytics5.php?id='.$course->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);

$modulos=$course_info->get_used_mods();

$modnames=$course_info->get_used_mod_names();
$modnamesplural=$course_info->get_mod_names_plural();

$defaultmod = optional_param('defaultmod',$modulos[0],PARAM_FILE); // id modulo

$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

$medias=$my_analytics->get_average_analytics_by_module();

$section=$course_info->get_course_format();
/*******************************************************************************/

// Navigation links
$navlinks = array();
$navlinks[] = array('name' => get_string('all_analytics','block_analytics_recommendations'), 'link' => $CFG->wwwroot.'/blocks/analytics_recommendations/all_analytics.php?id='.$course->id, 'type' => 'misc');
$navlinks[] = array('name' => get_string('comparative_analytics','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

//Header
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Tabs
$tabs = $gui->get_comparative_analytics_tabs($users, $course);
print_tabs($tabs, '', array('a22'), array('a22'));

// Page title
echo $OUTPUT->heading(get_string('average_participation_all_activities','block_analytics_recommendations'));

// Information box
echo $OUTPUT->box(get_string('analytics5_message','block_analytics_recommendations'),'generalbox','notice');

// Table data
$table_data=array(array());
$colours=array(array());
$table_data[0][0]='';
for($i=0;$i<count($modulos);$i++)
    $table_data[0][$i+1]=$OUTPUT->pix_icon('icon',$modnamesplural[$modulos[$i]], $modulos[$i]).'&nbsp;&nbsp;<a href="analytics4.php?id='.$course->id.'&defaultmod='.$modulos[$i].'&users='.urlencode(serialize($users)).'">'.$modnamesplural[$modulos[$i]].'</a>'; 

$i=1;
foreach ($users as $key => $value){
    $user = $DB->get_record("user", array('id'=>$value));
    $resultados=$my_analytics->get_my_analytics_by_module($user);
    $table_data[$i][0]='<a href="analytics1.php?id='.$course->id.'&user='.$user->id.'">'.format_string($user->firstname)." ".format_string($user->lastname).'</a>';    
    for($j=0;$j<count($modulos);$j++){
        if (isset($resultados[$modulos[$j]]))
            $table_data[$i][$j+1]=$resultados[$modulos[$j]].'%';  
        else
            $table_data[$i][$j+1]='';
        $colours[$i][$j+1]=$gui->get_colour_cell($resultados[$modulos[$j]], $num_users);
    }
    $i++;
}
$table_data[$i][0]=get_string('participation_average','block_analytics_recommendations');
for($j=0;$j<count($modulos);$j++){
    if (isset($medias[$modulos[$j]]))
        $table_data[$i][$j+1]=$medias[$modulos[$j]].'%';
    else
        $table_data[$i][$j+1]='';
    $colours[$i][$j+1]=$gui->get_colour_cell($medias[$modulos[$j]], $num_users);
}

// Display table data
echo html_writer::table($gui->get_table($table_data,$colours,true,true));

// Graph
echo $gui->get_analytics_graph($course, 4, null,null,$users);

// Page footer
echo $OUTPUT->footer();
?>
