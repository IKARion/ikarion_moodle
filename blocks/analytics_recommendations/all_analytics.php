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
$a = optional_param('a', 'a1', PARAM_TEXT);
$ord = optional_param('ord', 'user', PARAM_TEXT);
$sent = optional_param('sent', '0', PARAM_INT);

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

$PAGE->set_url(new moodle_url('/blocks/analytics_recommendations/setup.php', array('id' => $course->id)));

// Authenticated users only
require_login($course);
require_capability('block/analytics_recommendations:viewglobal', $context);

// To update the log
add_to_log($course->id, 'analytics', 'all_analytics', 'all_analytics.php?id='.$course->id, $course->id, $course->id, $USER->id);

/*******************************************************************************/
// Data 
$gui=new gui();
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);

$mods=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();

$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

$students=array();
foreach($coursestudents as $coursestudent){
   $user = $DB->get_record("user", array('id'=>$coursestudent));
   $students[$user->id]=$user->lastname.', '.$user->firstname;
}
// Orden ascendente
asort($students);

/*******************************************************************************/

// Navigation links
$navlinks = array();
$navlinks[] = array('name' => get_string('all_analytics','block_analytics_recommendations'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

//Header
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Tabs
$tabs=$gui->get_all_analytics_tabs($course);
print_tabs($tabs, array($a), array($a), array($a));

switch ($a){
    case 'a1':
        // Page title
        echo $OUTPUT->heading(get_string('single_analytics','block_analytics_recommendations'));
        // Form
        $action=$CFG->wwwroot.'/blocks/analytics_recommendations/my_progress.php';
        $mform=new choose_user_form($action, array('id'=>$id,'students'=>$students),'get');
        $mform->display();

        break;
    case 'a2':
        // Page title
        echo $OUTPUT->heading(get_string('comparative_analytics','block_analytics_recommendations'));
        $action=$CFG->wwwroot.'/blocks/analytics_recommendations/analytics4.php';
        $mform=new choose_users_form($action, array('id'=>$id,'students'=>$students),'get');
        $mform->display();
        
        break;
    case 'a3':
        // Page title
        echo $OUTPUT->heading(get_string('global_analytics','block_analytics_recommendations'));
        
        $medias=$my_analytics->get_average_analytics_by_module();
	
	$num_mods=count($mods);
	
	foreach ($mods as $key => $value){	   		
		$med[$key]=$medias[$value];			
	}
	
        if ($ord=='user' && $sent==1)           
            arsort($students);        
       
	foreach($students as $id=>$name){
            $user = $DB->get_record("user", array('id'=>$id));
            $resultados=$my_analytics->get_my_analytics_by_module($user);
            $media=0;
            foreach ($mods as $key => $value){		
                $datos[$id][$value]=$resultados[$value];	
                $media+=$resultados[$value];
            }
            $datos[$id]['avg']=round($media/count($mods),2);                
	}
        	
	if ($ord!='user')
            $datos=$my_analytics->order_multidimensional_array($datos,$ord,$sent);
        
	// Information box
	echo $OUTPUT->box(get_string('all_analytics_message','block_analytics_recommendations'),'generalbox','notice');
	
	$table_data=array(array());
	$colours=array(array());
	$table_data[0][0]='<br/>'.get_string('students','block_analytics_recommendations').'<br/><a href="?id='.$course->id.'&amp;a=a3&amp;ord=user&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;a=a3&amp;ord=user&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';
	
        for($i=0;$i<count($mods);$i++)
            $table_data[0][$i+1]=$OUTPUT->pix_icon('icon',$modnamesplural[$mods[$i]], $mods[$i]).'<br/>'.$modnamesplural[$mods[$i]].'<br/><a href="?id='.$course->id.'&amp;a=a3&amp;ord='.$mods[$i].'&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;a=a3&amp;ord='.$mods[$i].'&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';
	
        $table_data[0][$i+1]='<br/>'.get_string('avg','block_analytics_recommendations').'<br/><a href="?id='.$course->id.'&amp;a=a3&amp;ord=avg&amp;sent=0">'.print_arrow('down',get_string('sort_asc','block_analytics_recommendations')).'</a><a href="?id='.$course->id.'&amp;a=a3&amp;ord=avg&amp;sent=1">'.print_arrow('up',get_string('sort_desc','block_analytics_recommendations')).'</a>';
	
        $i=1;
	foreach($datos as $userid => $resultados){ 	
            $user = $DB->get_record("user", array('id'=>$userid));
            $table_data[$i][0]='<a href="analytics1.php?id='.$course->id.'&user='.$user->id.'&amp;a=a3">'.$user->lastname.', '.$user->firstname.'</a>';
            $media=0;
            $j=1;
            foreach ($resultados as $key => $value){	 
                if (!strcmp($key,'avg')){
                    $table_links[$i][$j]='location.href=\'analytics3.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$key.'&amp;a=a3\'';
                    $table_data[$i][$j]=$value.'%';
                    $colours[$i][$j]=$gui->get_colour_cell($value, $num_users);
                }else{
                    $table_links[$i][$j]='location.href=\'analytics2.php?id='.$course->id.'&user='.$user->id.'&defaultmod='.$key.'&amp;a=a3\'';                                                    
                    $table_data[$i][$j]=$value.'%';
                    $colours[$i][$j]=$gui->get_colour_cell($value, $num_users);
                }
                $j++;
            }
            $i++;
	}
	$table_data[$i][0]=get_string('avg','block_analytics_recommendations');        
        $media=0;
        $j=1;
	foreach ($mods as $key => $value){
            $media+=$med[$key];		
            $table_data[$i][$j]=$med[$key].'%';
            $colours[$i][$j]=$gui->get_colour_cell($med[$key], $num_users);
            $j++;
        }
        $media=round($media/$num_mods,2);	
	$table_data[$i][$j]=$media.'%';
        $colours[$i][$j]=$gui->get_colour_cell($media, $num_users);
        // Display table data
        echo html_writer::table($gui->get_table($table_data,$colours,true,false,$table_links));
        // Display table legend
        echo html_writer::table($gui->get_legend_table());
        break;        
}

// Page footer
echo $OUTPUT->footer();
?>
