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
$type    = required_param('type',PARAM_INT);     // user id

if (! $course = $DB->get_record("course", array('id'=>$id))) {
    error("Course id is incorrect.");
}

if ($user!=0 && !$user = $DB->get_record("user", array('id'=>$user))) {
    error("User ID is incorrect");
}

$context  = get_context_instance(CONTEXT_COURSE, $course->id);

// Authenticated users only
require_login($course);
if ($USER->id==$user->id)
    require_capability('block/analytics_recommendations:viewsingle', $context);
else
    require_capability('block/analytics_recommendations:viewglobal', $context);

// Graph data
$course_info=new course_info($course);
$my_recommendations=new recommendations($course_info);
$reference_course_info=$my_recommendations->get_reference_course_info();
$mods=$course_info->get_used_mods();
$modnamesplural=$course_info->get_mod_names_plural();
$coursestudents = $course_info->get_course_students();
$num_users=count($coursestudents);

// Current user summary
$resumen_user_actual=$my_recommendations->get_course_summary($user);
unset($resumen_user_actual['grade']);
$resumen_normal_user_actual=$my_recommendations->get_course_normal_summary($resumen_user_actual);

// Graph
$my_graph= new my_graph(390,290); 

if($type!=1){
    if($type==2 || $type==3){
        $aprobados=$my_recommendations->get_summary_to_pass();
        $aprobados_normal=$my_recommendations->get_reference_course_normal_summary($aprobados);
        $esfuerzo_aprobar=$my_recommendations->get_effort_to_pass($user);
    }else{
        $mejores=$my_recommendations->get_summary_to_get_best_grade();
        $mejores_normal=$my_recommendations->get_reference_course_normal_summary($mejores);
        $esfuerzo_mejor=$my_recommendations->get_effort_to_get_best_grade($user);
    }     
}else{
$my_graph= new my_graph(850,350);
}

$my_graph->set_legend(6); // Legend position
switch ($type){
    case '1':      
        $my_graph->set_legend(1); // Legend position
        foreach ($resumen_user_actual as $key=> $value){
            $xdata[]=$modnamesplural[$key];
            $ydata[]=$value;
        }
        $legend=get_string('your_participation','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations');
        $ylabel=get_string('percentage','block_analytics_recommendations');     
        $my_graph->one_series_graph($xdata, $ydata, $legend, $xlabel, $ylabel,'navy');
        break;
    case '2': 
        $my_graph->set_legend(5); // Legend position
        foreach ($resumen_user_actual as $key=> $value){
            $xdata[]=$modnamesplural[$key];
            $ydata1[]=$value;
            if (isset($aprobados_normal[$key]))
                $ydata2[]=round($aprobados_normal[$key]*100/$num_users);
            else
                $ydata2[]=0;
        }
        $legend1=get_string('your_participation','block_analytics_recommendations');
        $legend2=get_string('participation_to_pass','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations');
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        
        $colours=array('navy','ltblue');
        $lines=array(0,0);
        $points=array(0,0);
        $bars=array(1,1);
         
        $my_graph->two_series_graph($xdata, $ydata1, $ydata2, $legend1, $legend2, $xlabel, $ylabel, $colours, $points, $lines,$bars);        
   case '3':            
        foreach ($esfuerzo_aprobar as $key=> $value){
            $xdata[]=$modnamesplural[$key];
            $ydata[]=$value;
        }
        $legend=get_string('effort','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations');
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        
        $my_graph->one_series_graph($xdata, $ydata, $legend, $xlabel, $ylabel,'orange'); 
        break;
  case '4':       
        $my_graph->set_legend(5); // Legend position
        foreach ($resumen_user_actual as $key=>$value){
            if (isset($mejores_normal[$key]))
                $participation=round($mejores_normal[$key]*100/$num_users);
            else 
                $participation=0;
            if ($participation>100) $participation=100;
            $xdata[]=$modnamesplural[$key];
            $ydata1[]=$value;
            $ydata2[]=$participation;
        }
        $legend1=get_string('your_participation','block_analytics_recommendations');
        $legend2=get_string('best_participation','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations');
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        
        $colours=array('navy','ltblue');
        $lines=array(0,0);
        $points=array(0,0);
        $bars=array(1,1);
                  
        $my_graph->two_series_graph($xdata, $ydata1, $ydata2, $legend1, $legend2, $xlabel, $ylabel,$colours,$points, $lines, $bars);
   case '5':            
        foreach ($esfuerzo_mejor as $key=> $value){
            if ($value>100) $value=100;
            $xdata[]=$modnamesplural[$key];
            $ydata[]=$value;
        }
        $legend=get_string('effort','block_analytics_recommendations');
        $xlabel=get_string('activities','block_analytics_recommendations');
        $ylabel=get_string('percentage','block_analytics_recommendations');   
        
        $my_graph->one_series_graph($xdata, $ydata, $legend, $xlabel, $ylabel,'orange'); 
        break;
    } 