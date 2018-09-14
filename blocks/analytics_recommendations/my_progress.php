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

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/my_progress.php', array('id' => $course->id, 'user' => $user->id)));


$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'my_progress', 'my_progress.php?id='.$course->id.'&user='.$user->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);
$grades=$my_analytics->get_grades($user);
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
print_tabs($tabs, '', array('p11'), array('p11'));

// Page title
echo $OUTPUT->heading(get_string('my_progress', 'block_analytics_recommendations').': '.format_string($user->firstname)." ".format_string($user->lastname));

// Information box
echo $OUTPUT->box(get_string('my_progress_message', 'block_analytics_recommendations'),'generalbox','notice');
$y=0;
$table_data[$y][0]=$section;
$table_data[$y][1]=get_string('activity', 'block_analytics_recommendations'); 
$table_data[$y][2]=get_string('participation','block_analytics_recommendations'); 
$table_data[$y][3]=get_string('grade','block_analytics_recommendations'); 
$y++;
// Table data
$avg=0;$cont=0;
for($j=0;$j<=$course_info->get_numsections();$j++){
    for($i=0;$i<count($modulos);$i++){
        if (isset($resultados[$modulos[$i]][$j])){
            $table_data[$y][0]=$section.'&nbsp;'.$j; 
            $table_data[$y][1]=$OUTPUT->pix_icon('icon',get_string('pluginname', $modulos[$i]),$modulos[$i]).'&nbsp;<a href="analytics2.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$modulos[$i].'">'.get_string('pluginname', $modulos[$i]).'</a>';
            $table_data[$y][2]=round($resultados[$modulos[$i]][$j],2).'%';  
            $table_data[$y][3]='';
            $colours[$y][2]=$gui->get_colour_cell($resultados[$modulos[$i]][$j], $num_users); 
            $colours[$y][0]='#D8D8D8';            
            $align[$y][0]=$align[$y][2]=$align[$y][3]='center';
            $align[$y][1]='left';
            if (isset($grades[$j][$modulos[$i]])){
                $table_data[$y][3]=round($grades[$j][$modulos[$i]],2).'/100';
                $colours[$y][3]='#D8D8D8';
            }
            $y++;
        }
    }
    if (isset($grades[$j]['grade'])){
        $table_data[$y][0]=get_string('avg_grade','block_analytics_recommendations').'&nbsp;'.$section.'&nbsp;'.$j;
        $table_data[$y][1]=$gui->get_progress_bar($grades[$j]['grade']); 
        $table_data[$y][2]=round($grades[$j]['grade'],2).'%';
        $table_data[$y][3]='';        
        $colours[$y][0]=$colours[$y][1]=$colours[$y][2]=$colours[$y][3]='#F0F0F0';  
        $align[$y][0]=$align[$y][3]='center';
        $align[$y][1]=$align[$y][2]='left';
        $avg+=$grades[$j]['grade'];
        $cont++;
        $y++;
    }
}
if ($cont!=0)
    $final_grade=round($avg/$cont,2);
else 
    $final_grade=0;
$table_data[$y][0]=get_string('avg_grade','block_analytics_recommendations');
$table_data[$y][1]=$gui->get_progress_bar($final_grade);  
$table_data[$y][2]=$final_grade.'%';
$table_data[$y][3]='';
$colours[$y][0]=$colours[$y][1]=$colours[$y][2]=$colours[$y][3]='#E8E8E8'; 
$align[$y][0]=$align[$y][3]='center';
$align[$y][1]=$align[$y][2]='left';

// Display table data
echo html_writer::table($gui->get_table_progress($table_data,$colours,$align)); 

// Display table legend
echo html_writer::table($gui->get_legend_table());

// Page footer
echo $OUTPUT->footer();
?>