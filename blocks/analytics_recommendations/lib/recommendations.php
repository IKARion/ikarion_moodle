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
 * Description of recommendations
 *
 * @author Cristina
 */
class recommendations extends analytics{
    private $reference_course_info;
    
    /**
     * Constructor
     * @global object $CFG
     * @global object $DB
     * @param $course_info 
     */
    public function __construct($course_info) {  
        global $CFG, $DB;   
        parent::__construct($course_info);
        $this->reference_course_info=new course_info($DB->get_record("course", array('id'=>$this->get_reference_course())));        
    }
    
    /**
     * It returns a course_info with the reference course
     * @return course_info 
     */
    public function get_reference_course_info(){
        return $this->reference_course_info;
    }
    
    /**
	 * It returns a summary with user participation and grades
     * @global object $CFG
     * @global object $DB
     * @param $user
     * @param $course
     * @return array 
     */
    private function get_summary($user,$course){
        global $CFG, $DB;    
        $totals_grade=0;
        $results=array();
        $totals=array();
        $sql ='SELECT * FROM '.$CFG->prefix.'analytics_recommendations_'.$course->id.' WHERE user='.$user->id;
        if ($analytics = $DB->get_record_sql($sql)){           
            // Grades over 100 --> x/100
            foreach ($analytics as $key => $value){                              
                $arrayValores=explode('_',$key,2);
                if ($arrayValores[0]!='grade'){
                    if (!isset($results[$arrayValores[0]])){
                        $results[$arrayValores[0]]=0;
                        $totals[$arrayValores[0]]=0; 
                    }
                    $results[$arrayValores[0]]+=$value;
                    $totals[$arrayValores[0]]++;  
                }else{
                    // Not use activities unrated
                    $sql = 'SELECT MAX('.$key.') FROM '.$CFG->prefix.'analytics_recommendations_'.$course->id;
                    $max_grade = $DB->get_record_sql($sql);
                    foreach ($max_grade as $key2 => $value2)
                        $max_grade=$value2;
                    if ($max_grade>0){
                        if (!isset($results[$arrayValores[0]]))
                            $results[$arrayValores[0]]=0;
                        $results[$arrayValores[0]]+=($value*100)/$this->get_max_grade($course,$key);
                        $totals_grade++;
                    }                        
                }                             
            }
            
            // Delete id and user
            unset($results['id']);
            unset($results['user']);
            
            // Do results average
            foreach ($results as $key => $value){
                if ($key!='grade')
                    $results[$key]/=$totals[$key];
                else if ($totals_grade>0)
                    $results[$key]/=$totals_grade;
                else
                    $results[$key]=0;
                $results[$key]=round($results[$key],2);
            } 
            if (!isset($results['grade']))
                $results['grade']=0;
            
            // Order the results from highest to lowest participation
            asort($results,SORT_NUMERIC); // arsort
            return ($results);
        }
    }
    
    /**
     * It returns a summary with user participation and grades for current course
     * @param $user
     * @return array 
     */
    public function get_course_summary($user){
        return $this->get_summary($user, $this->course_info->get_course());
    }
    
    /**
     * It returns a summary with user participation and grades for reference course
     * @param $user
     * @return array 
     */
    public function get_reference_course_summary($user){
        return $this->get_summary($user, $this->reference_course_info->get_course());
    }
    
    /**
     * It transforms a user summary in a independent summary
     * @global object $CFG
     * @global object $DB
     * @param $results
     * @param $course
     * @return array 
     */
    private function get_normal_summary($results,$course){
        global $CFG, $DB;    
        $num_students=$DB->count_records('analytics_recommendations_'.$course->id);
        
        foreach ($results as $key => $value){            
            if ($key!='grade')
                $results[$key]=round($results[$key]*$num_students/100,2);      
            else
                $results[$key]=round($results[$key],2);
        } 
        return $results;
    }
    
    /**
	 * It transforms a user summary in a independent summary for current course 
     * @param $summary
     * @return array 
     */
    public function get_course_normal_summary($summary){
        return $this->get_normal_summary($summary, $this->course_info->get_course());
    }
    
    /**
     * It transforms a user summary in a independent summary for reference course 
     * @param $summary
     * @return array 
     */
    public function get_reference_course_normal_summary($summary){
        return $this->get_normal_summary($summary, $this->reference_course_info->get_course());
    }

    /**
     * It returns a estimated grade for a user
     * @global object $CFG
     * @global object $DB
     * @param $user
     * @return float 
     */
    public function get_estimated_grade($user){
        global $CFG, $DB;    
        // Current user summary
        $resumen_user_actual=$this->get_course_summary($user);
        
        $resumen_normal_user_actual=$this->get_course_normal_summary($resumen_user_actual);

        // Reference course students
        $reference_users=$this->reference_course_info->get_course_students();
        
        // Reference course users summaries
      
        foreach ($reference_users as $userid) {
            $reference_user = $DB->get_record("user", array('id'=>$userid));
            $resumenes[$userid]=$this->get_reference_course_summary($reference_user);
            $resumenes_normales[$userid]=$this->get_reference_course_normal_summary($resumenes[$userid]);
        }
        
        // It calcules the difference
        
        foreach ($resumenes_normales as $key=>$value) {       
            $diferencias[$key]=$this->get_difference($resumen_normal_user_actual,$value);
        }
        // Order
        asort($diferencias);	
        // The most similar user
        $claves=array_keys($diferencias);
        if (isset($resumenes[$claves[0]]['grade']))
            return $resumenes[$claves[0]]['grade'];
        else
            return 0;
    }
    
    /**
     * It returns the user effort to pass
     * @global object $CFG
     * @global object $DB
     * @param $user
     * @return int 
     */
    public function get_effort_to_pass($user){
        global $CFG,$DB;
        $effort_to_pass=array();
        $num_students=$DB->count_records('analytics_recommendations_'.$this->course_info->get_course()->id);
        $user_summary=$this->get_course_summary($user);
        unset($user_summary['grade']);
        $user_normal_summary=$this->get_course_normal_summary($user_summary);
        $to_pass_summary=$this->get_summary_to_pass();
        $to_pass_normal_summary=$this->get_reference_course_normal_summary($to_pass_summary);
        foreach ($user_normal_summary as $key=>$value) {
            if (isset($to_pass_normal_summary[$key]) && ($to_pass_normal_summary[$key]-$value)>0)
                $effort_to_pass[$key]=round(($to_pass_normal_summary[$key]*100/$num_students)-$user_summary[$key],2);
            else	
                $effort_to_pass[$key]=0;
        }
        arsort($effort_to_pass,SORT_NUMERIC); 
        return $effort_to_pass;       
    }
    
    /**
     * It returns the user effort to get the best grade
     *
     * @global object $CFG
     * @global object $DB
     * @param $user
     * @return int 
     */
    public function get_effort_to_get_best_grade($user){
        global $CFG,$DB;
        $effort_best_grade=array();
        $num_students=$DB->count_records('analytics_recommendations_'.$this->course_info->get_course()->id);
        $user_summary=$this->get_course_summary($user);
        unset($user_summary['grade']);
        $user_normal_summary=$this->get_course_normal_summary($user_summary);
        $best_grade_summary=$this->get_summary_to_get_best_grade();
        $best_grade_normal_summary=$this->get_reference_course_normal_summary($best_grade_summary);
        foreach ($user_normal_summary as $key=>$value) {
            if (isset($best_grade_normal_summary[$key]) && ($best_grade_normal_summary[$key]-$value)>0)
                $effort_best_grade[$key]=round(($best_grade_normal_summary[$key]*100/$num_students)-$user_summary[$key],2);
            else	
                $effort_best_grade[$key]=0;
        }
        arsort($effort_best_grade,SORT_NUMERIC); 
        return $effort_best_grade;       
    }
    
    /**
     * It returns the summary to pass
     * @global object $CFG
     * @global object $DB
     * @return array 
     */
    public function get_summary_to_pass(){
        global $CFG,$DB;
        $to_pass_summary=array();
        $rc_summaries=array();
        // Reference course students
        $reference_users=$this->reference_course_info->get_course_students();

        // Reference course student summaries
        foreach ($reference_users as $userid) {
            $reference_user = $DB->get_record("user", array('id'=>$userid));
            $rc_summaries[$userid]=$this->get_reference_course_summary($reference_user);
        }
        $cont=0;
        foreach ($rc_summaries as $rc_summary) {
            // to do
            if (floatval($rc_summary['grade'])>=50 and floatval($rc_summary['grade'])<=65){
                foreach ($rc_summary as $key=> $value){ 
                    if (!isset($to_pass_summary[$key]))
                        $to_pass_summary[$key]=0;
                    $to_pass_summary[$key]+=$value;
                }
            $cont++;
            }			
        }        
        if ($cont==0){
            $best_grade_summary=$this->get_summary_to_get_best_grade();
            foreach ($best_grade_summary as $key=> $value){
                $to_pass_summary[$key]=$value*6/10;		
            }
        }else{
            unset($to_pass_summary['grade']);
            foreach ($to_pass_summary as $key=> $value){
                $to_pass_summary[$key]=$value/$cont;		
            }            
        }
        arsort($to_pass_summary,SORT_NUMERIC);
        return $to_pass_summary;
    }
    
    /**
     * It returns the summary to get the best grade
     * @global object $CFG
     * @global object $DB
     * @return array 
     */
    public function get_summary_to_get_best_grade(){
        global $CFG,$DB;
        // Reference course students
        $reference_users=$this->reference_course_info->get_course_students();

        // Reference course student summaries
        foreach ($reference_users as $userid) {
            $reference_user = $DB->get_record("user", array('id'=>$userid));
            $rc_summaries[$userid]=$this->get_reference_course_summary($reference_user);
        }
        $max_grade=0;
        foreach ($rc_summaries as $rc_summary) {
            if (floatval($rc_summary['grade'])>=$max_grade){
                $max_grade=$rc_summary['grade'];
                $best_grade_summary=$rc_summary;
            }			
        }
        unset($best_grade_summary['grade']);        
        arsort($best_grade_summary,SORT_NUMERIC);
        return $best_grade_summary;
    }
    
    /*
     * It returns the difference between two arrays
     * @param $array1
     * @param $array2
     * @return array
     */
    private function get_difference($array1, $array2){
        $resultado=0;
        foreach ($array1 as $key => $value){
            // Si no es una calificación (grade) y si existe esa clave en el array2
            if ($key!="grade" and isset($array2[$key])){
                $resultado+=abs($value-$array2[$key]);	                
            }
        }
        return round($resultado);
    } 
}

?>
