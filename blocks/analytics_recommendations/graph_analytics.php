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

$id      = required_param('id',PARAM_INT);       // course id
$user    = optional_param('user',0,PARAM_INT);     // user id
$users    = optional_param('users',new stdClass(),PARAM_TEXT);     // array de users id
$type    = required_param('type',PARAM_INT);     // user id
$mod     = optional_param('mod',0,PARAM_TEXT);     // mod

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

if ($user!=0 && !$user = $DB->get_record("user", array('id'=>$user))) {
    error("User ID is incorrect");
}

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if (isset($user->id) && $USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

 
$course_info=new course_info($course);
$my_analytics=new analytics($course_info);
$mods=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();

// Graph data
$section=$course_info->get_course_format();

switch ($type){
    case '1':
        $resultados=$my_analytics->get_my_analytics($user);
        $medias=$my_analytics->get_average_analytics();
        for ($i=0; $i<=$course_info->get_numsections(); $i++){
            $xdata[$i]=$i;     
            if (!isset($resultados[$mod][$i])){
                $resultados[$mod][$i]=0;
                $medias[$mod][$i]=0;
            }                
        }
        $ydata1=$resultados[$mod];        
        $ydata2=$medias[$mod];       
        $legend1=format_string($user->firstname)." ".format_string($user->lastname);
        $legend2=get_string('participation_average','block_analytics_recommendations');
        $xlabel=$section; 
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        $my_graph = new my_graph(850,350);
        $my_graph->two_series_graph($xdata, $ydata1, $ydata2, $legend1, $legend2, $xlabel, $ylabel,array($my_graph->colours[array_search($mod, $mods)%18],'navy'));
        break;
    case '2':
        $resultados=$my_analytics->get_my_analytics_by_module($user);
        $medias=$my_analytics->get_average_analytics_by_module();
        foreach ($mods as $key => $value){
            $ydata1[$key]=$resultados[$value];
            $ydata2[$key]=$medias[$value];
            $xdata[$key]=$modnamesplural[$value];
        }       
        $legend1=format_string($user->firstname).' '.format_string($user->lastname);
        $legend2=get_string('participation_average','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations'); 
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        $my_graph = new my_graph(850,350);
        $my_graph->two_series_graph($xdata, $ydata1, $ydata2, $legend1, $legend2, $xlabel, $ylabel);        
        break;
    case '3':   
        $users=unserialize(urldecode($users));
        $medias=$my_analytics->get_average_analytics();        
        for ($i=0; $i<=$course_info->get_numsections(); $i++){
            $xdata[$i]=$i;  
            if (!isset($medias[$mod][$i]))
                $medias[$mod][$i]=0;
        }
        $k=0;    
        foreach ($users as $key => $value){
            $user = $DB->get_record("user", array('id'=>$value));
            $resultados=$my_analytics->get_my_analytics($user);
            for ($i=0; $i<=$course_info->get_numsections(); $i++){
                if (!isset($resultados[$mod][$i]))
                    $resultados[$mod][$i]=0;
            }            
            $ydatas[$k]=$resultados[$mod];
            $legends[$k]=format_string($user->firstname)." ".format_string($user->lastname);    
            $lines[$k]=1;
            $points[$k]=1;
            $bars[$k]=0;
            $k++;
        }
        $ydatas[$k]=$medias[$mod];
        $legends[$k]=get_string('participation_average','block_analytics_recommendations');  
        $lines[$k]=1;
        $points[$k]=1;
        $bars[$k]=0;
        
        $xlabel=$section; 
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        $my_graph = new my_graph(850,350);
        $my_graph->multiple_series_graph($xdata, $ydatas, $legends, $xlabel, $ylabel, $points, $lines, $bars);
        break;
        
    case '4':   
        $users=unserialize(urldecode($users));
        $medias=$my_analytics->get_average_analytics_by_module();        
        foreach ($mods as $key => $value){            
            $xdata[$key]=$modnamesplural[$value];
            $medias2[$key]=$medias[$value];            
        }          
        foreach ($users as $key => $value){
            $user = $DB->get_record("user", array('id'=>$value));
            $resultados=$my_analytics->get_my_analytics_by_module($user);
             foreach ($mods as $key2 => $value2){  
                $ydatas[$key][$key2]=$resultados[$value2];
             }
            $legends[$key]=format_string($user->firstname)." ".format_string($user->lastname);    
            $lines[$key]=1;
            $points[$key]=1;
            $bars[$key]=0;             
        }
        $ydatas[$key+1]=$medias2;
        $legends[$key+1]=get_string('participation_average','block_analytics_recommendations');  
        $lines[$key+1]=1;
        $points[$key+1]=1;
        $bars[$key+1]=0;       
        $xlabel=get_string('activities','block_analytics_recommendations'); 
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        $my_graph = new my_graph(850,350);
        $my_graph->multiple_series_graph($xdata, $ydatas, $legends, $xlabel, $ylabel, $points, $lines, $bars);
        break;         
    } 