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
 * Description of analytics
 *
 * @author Cristina
 */
class analytics {
    
    protected $course_info;
    
    /**
     * Constructor
     * @param type $course_info 
     */
    public function __construct($course_info) {       
        $this->course_info=$course_info;
    }
    
    /**
     * It returns the max grade
     * @global object $CFG
     * @global object $DB
     * @param $course
     * @param $grade
     * @return float 
     */
    protected function get_max_grade($course,$grade){
        global $CFG, $DB;
        $sql = 'SELECT `'.$grade.'` FROM '.$CFG->prefix.'analytics_recommendations_total_'.$course->id;
        
        if ($max_grade = $DB->get_field_sql($sql))
            return $max_grade;
        return false;
    }
    
    /**
     * It returns the reference course id
     * @return int 
     */
    public function get_reference_course(){    
        global $CFG, $DB;
        // Obtenemos los alumnos del curso
        $sql='SELECT reference_course FROM '.$CFG->prefix.'analytics_recommendations_total_'.$this->course_info->get_course()->id;
        if ($reference_course=$DB->get_field_sql($sql))	
            return $reference_course;  
    }
    
    /**
     * It returns the user analytics
     * @param $user
     * @return array 
     */
    public function get_my_analytics($user){
        global $CFG, $DB;
        $res=array();
        $sql='SELECT * FROM '.$CFG->prefix.'analytics_recommendations_'.$this->course_info->get_course()->id.' WHERE user='.$user->id;
        if ($logs = $DB->get_record_sql($sql)) {
            foreach ($logs as $key => $value){
                if ($key!='id' && $key!='user' && substr($key,0,5)!='grade'){
                        $key=explode("_",$key,2);
                        if (!isset($res[$key[0]][$key[1]]))
                            $res[$key[0]][$key[1]]=0;
                        $res[$key[0]][$key[1]]+=$value;			
                }		
            }
            return $res;
        }
        return false;
    }
    
    /**
     * It returns the user grades
     * @param $user
     * @return array 
     */
    public function get_grades($user){
        global $CFG, $DB;
        $sql='SELECT * FROM '.$CFG->prefix.'analytics_recommendations_'.$this->course_info->get_course()->id.' WHERE user='.$user->id;
        $grades=array();
        $counter=array();
        $section=0;
        if ($logs = $DB->get_record_sql($sql)) {
           foreach ($logs as $key => $value){
                if ($key!='id' && $key!='user'){
                    if(substr($key,0,5)!='grade'){
                        $key=explode("_",$key,2);
                        $section=$key[1];                        		
                    }else{
                        $key2=explode("_",$key,3);
                        if (!isset($grades[$section][$key2[1]])){
                            $grades[$section][$key2[1]]=0;
                            $counter[$section][$key2[1]]=0;
                        }
                        $grades[$section][$key2[1]]+=($value*100)/$this->get_max_grade($this->course_info->get_course(),$key);
                        $counter[$section][$key2[1]]++;
                    }		
                }
           }
           
           foreach ($grades as $section => $modules){
               $cont=0;$avg=0;
               foreach($modules as $id =>$total){
                   $grades[$section][$id]=round($total/$counter[$section][$id],2);
                   $avg+=$grades[$section][$id];
                   $cont++;
               }   
               if($cont>0)
               $grades[$section]['grade']=round($avg/$cont,2);
           }
          return $grades;
        }
        return false;
    }
    
    /**
     * It returns the user analytics by module
     * @param $user
     * @return type 
     */
    public function get_my_analytics_by_module($user){
        global $CFG, $DB;    
        $res=array();
        $cont=array();
        $sql='SELECT * FROM '.$CFG->prefix.'analytics_recommendations_'.$this->course_info->get_course()->id.' WHERE user='.$user->id;
        if ($logs = $DB->get_record_sql($sql)) {
            foreach ($logs as $key => $value){
                if ($key!='id' && $key!='user' && substr($key,0,5)!='grade'){
                    $key=explode("_",$key,2);
                    if (!isset($res[$key[0]])){
                        $res[$key[0]]=0;
                        $cont[$key[0]]=0;
                    }
                    $res[$key[0]]+=$value;	
                    $cont[$key[0]]++;		
                }		
            }
            foreach ($res as $key => $value){
                $res[$key]=round($res[$key]/$cont[$key],2);
            }
            return $res;
        }
        return false;
    }
    
    /**
     * It returns the average analytics
     * @return type 
     */
    public function get_average_analytics(){
        global $CFG, $DB; 
        $res=array();
        $num_students=$DB->count_records('analytics_recommendations_'.$this->course_info->get_course()->id);
        $sql='SELECT * FROM '.$CFG->prefix.'analytics_recommendations_'.$this->course_info->get_course()->id;
        if ($logs = $DB->get_records_sql($sql)){
            foreach ($logs as $key => $value){
                foreach ($value as $key1 => $value1){
                    if ($key1!='id' && $key1!='user' && substr($key1,0,5)!='grade'){
                            $key1=explode("_",$key1,2);
                            if (!isset($res[$key1[0]][$key1[1]]))
                                $res[$key1[0]][$key1[1]]=0;
                            if ($value1>$res[$key1[0]][$key1[1]])
                                $res[$key1[0]][$key1[1]]=$value1;			
                    }	
                }				
            }            
            foreach ($res as $key => $value){
                foreach ($value as $key1 => $value1){
                    if ($value1>0)
                        $res[$key][$key1]=round(100/$num_students,2);
                    else  $res[$key][$key1]=0;
                }
            }
            return $res;		
        }
        return false;
    }	
    
    /**
     * It returns the average analytics by module
     * @return type 
     */
    public function get_average_analytics_by_module(){
        global $CFG, $DB; 
        $averages_by_module=array();
        $counter=array();
        $averages=$this->get_average_analytics();
        foreach ($averages as $module => $sections){
            $averages_by_module[$module]=0;
            $counter[$module]=0;
            foreach ($sections as $section => $avg){
                $averages_by_module[$module]+=$avg;
                $counter[$module]++;
            }
        }
        foreach ($averages_by_module as $id => $total){
            $averages_by_module[$id]=round($total/$counter[$id],2);
        }
        return $averages_by_module;       
    }
    
    /**
     * It orders an array
     * @param $toOrderArray
     * @param $field
     * @param $inverse
     * @return array 
     */
    public function order_multidimensional_array ($toOrderArray, $field, $inverse = false) {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
                $position[$key]  = $row[$field];
                $newRow[$key] = $row;
        }
        if ($inverse) {
            arsort($position);
        }
        else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {     
            $returnArray[$key] = $newRow[$key];
        }
        return $returnArray;
    }
}

?>
