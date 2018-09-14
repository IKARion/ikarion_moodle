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
 * Description of courseinfo
 *
 * @author cristina
 */
class course_info {
    
    private $course;
    
    /**
     * Constructor
     * @param $course
     */
    public function __construct($course) {
        $this->course=$course;
    }
    
    /**
     * It returns the course object
     * 
     */
    public function get_course(){
        return $this->course;
    }
    
    /**
     * It returns the course students
     * @return array
     */
    public function get_course_students(){        
        $student_ids=array();	
        $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
                   
        // Students have role 5
        $students = get_role_users(5, $context, true, 'u.id', 'u.id ASC');
        
        foreach ($students as $key => $value)
		$student_ids[]=$key;
        
	return $student_ids;        
    }
   
    /**
     * It returns a string with the course students
     * @return string
     */
   private function get_course_students_string(){        
        $students=$this->get_course_students();
        $cad='';
        if (isset ($students)&& count($students)>0){
            for ($i=0;$i<count($students);$i++){
		$cad.=$students[$i].',';
            }
            $cad=substr($cad,0,strlen($cad)-1);
        }
	return $cad; 
    }
    
    /**
     * It returns the used mods
     * @return array
     */
    public function get_used_mods(){        
        get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
        $modulos=array_keys($this->get_used_mod_names());
	return $modulos;	
    }
    
    /**
     * It returns the used mod names
     * @return array
     */
    public function get_used_mod_names(){        
        get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
        unset($modnamesused['label']);
        return $modnamesused;
    }
    
    /**
     * It returns the plural mod names
     * @return array
     */
    public function get_mod_names_plural(){        
        get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
        unset($modnamesplural['label']);
        return $modnamesplural;
    }
    
     /**
     * It returns all installed mods
     * @return array
     */
    public function get_all_mods(){        
        get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
	
	return $mods;	
    }

    /**
     * It returns the course sections
     * @return array
     */
    public function get_all_sections(){        
        return get_all_sections($this->course->id);		
	
    }
    
    /**
     * It returns the course sections format
     * @return string
     */
    public function get_course_format(){
		$section="";
		switch ($this->course->format) {
			case "weeks": 
				$section=get_string("week","block_analytics_recommendations"); 
				break;
			case "topics": 
				$section=get_string("topic","block_analytics_recommendations"); 
				break;
			default: 
				$section=get_string("section","block_analytics_recommendations"); 
				break;
		}        
        return $section;		
    }
    
    /**
     * It returns the num of course sections
     * @return int
     */
    public function get_numsections(){        
        return $this->course->numsections;		
    }
    
    /**
     * It returns the number of logs from moodle log table
     *    
     * @global object $CFG $CFG
     * @global object $DB $DB
     * @param type $mod
     * @param type $instance
     * @param type $start_time
     * @param type $end_time
     * @return type 
     */
    public function get_numviews($mod,$instance,$start_time=0,$end_time=9999999999){
        global $CFG,$DB;
        $students=$this->get_course_students_string();
        $numviewstotales=0;
        
        if (isset($students) && strlen($students)>0){
            $sql='SELECT l.id FROM '.$CFG->prefix.'log l WHERE l.userid IN ('.$students.') AND l.course='.$this->course->id.' AND l.module="'.$mod->modname.'" AND l.info="'.$instance->id.'" AND l.time > '.$start_time.' AND l.time <= '.$end_time;
                   
            if($logstotales = $DB->get_records_sql($sql)){
                $numviewstotales=count($logstotales);	
            }           	
        }        
        return $numviewstotales;
    }
    
    /**
     * It returns the number of student logs from moodle log table
     * @global object $CFG
     * @global object $DB
     * @param $userid
     * @param $mod
     * @param $instance
     * @param $start_time
     * @param $end_time
     * @return int 
     * @return int
     */   
    public function get_numviews_by_user($userid,$mod,$instance,$start_time=0,$end_time=9999999999){
        global $CFG, $DB;   
        // Dado USUARIO,  MODULO Y INSTANCIA  obtengo el porcentaje TOTAL de interaccion del USUARIO  con la INSTANCIA DEL MODULO
        $sql='SELECT l.id FROM '.$CFG->prefix.'log l WHERE l.userid='.$userid.' AND l.course='.$this->course->id.' AND l.module="'.$mod->modname.'" AND l.info="'.$instance->id.'" AND l.time > '.$start_time.' AND l.time <= '.$end_time;

        if ($logs = $DB->get_records_sql($sql)) {
            $numviews = count($logs);
        }
        else $numviews=0;	 

        return $numviews;
    }  
}

?>
