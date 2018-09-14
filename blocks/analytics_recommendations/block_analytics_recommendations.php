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

/**
 * 
 */
class block_analytics_recommendations extends block_list {
    /**
     * To init the block
     */
    function init() {        
        $this->title = get_string('analytics_recommendations','block_analytics_recommendations');
        $this->version = 2012040300;        // The current plugin version (Date: YYYYMMDDXX)
        $this->cron = 86400; // Every 24 hours
        
    }    
    
    /**
     *
     * @global object $CFG
     * @global object $COURSE
     * @global object $USER
     * @return object 
     */
    function get_content() {        
        global $CFG, $COURSE, $USER;
        require_once ($CFG->dirroot.'/config.php');  
        require_once($CFG->dirroot.'/course/lib.php');        
        require_once $CFG->libdir.'/gradelib.php';
        require_once $CFG->dirroot.'/grade/lib.php';
        require_once $CFG->dirroot.'/grade/report/grader/lib.php';
        
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/course_info.php');
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/module_maintenance.php');
        
        if (!isset($this->config->show_recommendations))
            $this->config->show_recommendations=true;
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }
               
        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        
        if (empty($this->instance->pinned)) {
            $blockcontext = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        } else {
            $blockcontext = get_context_instance(CONTEXT_SYSTEM);
        }
        
        if (has_capability('block/analytics_recommendations:init', $this->context) && !module_maintenance::exist_table('analytics_recommendations_'.$COURSE->id)){
            // To init tracking
            $this->content->items[] = '<a title="'.get_string('initiate_follow-up','block_analytics_recommendations').'" href="'.$CFG->wwwroot.'/blocks/analytics_recommendations/setup.php?id='.$COURSE->id.'">'.get_string('initiate_follow-up','block_analytics_recommendations').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/setup.gif" class="icon" alt="setup" />';	
        }else if (has_capability('block/analytics_recommendations:viewglobal', $this->context)) {
            // Teacher
            $this->content->footer =get_string('teacher','block_analytics_recommendations');
            
            $this->content->items[] ='<a title="'.get_string('analytics','block_analytics_recommendations').'" href="'.$CFG->wwwroot.'/blocks/analytics_recommendations/all_analytics.php?id='.$COURSE->id.'">'.get_string('analytics','block_analytics_recommendations').'</a>';
            if (module_maintenance::get_reference_course($COURSE)!=0)
            $this->content->items[] ='<a title="'.get_string('recommendations','block_analytics_recommendations').'" href="'.$CFG->wwwroot.'/blocks/analytics_recommendations/all_recommendations.php?id='.$COURSE->id.'">'.get_string('recommendations','block_analytics_recommendations').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/my_analytics.gif" class="icon" alt="analytics" />';        
            if (module_maintenance::get_reference_course($COURSE)!=0)
            $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/my_recommendations.gif" class="icon" alt="recommendations" />';
        }else if (has_capability('block/analytics_recommendations:viewsingle', $this->context)){
            // Student
            $this->content->footer =get_string('student','block_analytics_recommendations');       
            
            $this->content->items[] = '<a title="'.get_string('my_analytics','block_analytics_recommendations').'" href="'.$CFG->wwwroot.'/blocks/analytics_recommendations/my_progress.php?id='.$COURSE->id.'&user='.$USER->id.'">'.get_string('my_analytics','block_analytics_recommendations').'</a></center>';
            if (module_maintenance::get_reference_course($COURSE)!=0 && $this->config->show_recommendations==true)
            $this->content->items[] = '<a title="'.get_string('my_recommendations','block_analytics_recommendations').'" href="'.$CFG->wwwroot.'/blocks/analytics_recommendations/recommendations1.php?id='.$COURSE->id.'&user='.$USER->id.'">'.get_string('my_recommendations','block_analytics_recommendations').'</a></center>';
            $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/my_analytics.gif" class="icon" alt="analytics" />';
            if (module_maintenance::get_reference_course($COURSE)!=0 && $this->config->show_recommendations==true)
            $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/my_recommendations.gif" class="icon" alt="recommendations" />';
        }
        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }
	
    function cron (){
        global $CFG, $DB;           
        
        require_once ($CFG->dirroot.'/config.php');        
        require_once($CFG->dirroot.'/course/lib.php');        
        require_once $CFG->libdir.'/gradelib.php';
        require_once $CFG->dirroot.'/grade/lib.php';
        require_once $CFG->dirroot.'/grade/report/grader/lib.php';
        
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/course_info.php');
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/module_maintenance.php');
        
        mtrace(' ');
        mtrace('Start update analytics_recommendations cron ... ');
        
        // Last update date
        $end_time=time();
        mtrace('Last time update: '.date("d/m/Y H:i:s",$end_time));
        
        // Courses which works with analytics and recommendations
      	$course_names=module_maintenance::get_courses();
                
        foreach ($course_names as $id=>$name) {
            $course = $DB->get_record("course", array('id'=>$id));
            $course_info= new course_info($course);
            $module_maintenance=new module_maintenance($course_info);
            $module_maintenance->update_structure_table();
            mtrace('Updated structure tables for course '.$name);
            $start_time=$module_maintenance->get_timemodified();	
            $totales_ant=$module_maintenance->get_totals();			
            $module_maintenance->update_table_total_cron($start_time,$end_time);
            mtrace('Updated table total for course '.$name);
            $module_maintenance->update_table_cron($start_time,$end_time,$totales_ant);
            mtrace('Updated table total for course '.$name);
            $module_maintenance->update_timemodified($end_time);
        }
        mtrace('Update analytics_recommendations done.');
        return true;
    }
    
    // to configure settings
    public function specialization() {
        global $CFG,$COURSE;
        
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/module_maintenance.php');
        if (isset($this->config->ref_course)) {
            module_maintenance::update_reference_course($COURSE, $this->config->ref_course);
        }
    }
    
    // Only one instance per course
    public function instance_allow_multiple() {
        return false;
    }  
    
}

?>
