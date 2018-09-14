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
 * Description of estructure
 *
 * @author Cristina
 */
class module_maintenance {
    
    private $course_info;
    
    /**
     * Constructor
     * @param $course_info 
     */
    public function __construct($course_info) {
        $this->course_info=$course_info;
    }

    /**
     * It inserts a column in a database table
     * @global type $DB
     * @param string $table
     * @param string $newcolumn
     * @param string $aftercolumn
     * @param string $typecolumn 
     */
    private static function alter_table($table, $newcolumn, $aftercolumn, $typecolumn){
        global $DB;        
        $table = new xmldb_table($table);
        $field = new xmldb_field($newcolumn);
        if(strcmp($typecolumn,'integer')==0){                    
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, NULL_ALLOWED, false, '0', $aftercolumn);
        }else{
            $field->set_attributes(XMLDB_TYPE_NUMBER, '5,2', null, NULL_ALLOWED, false, '0', $aftercolumn);
        }              
        $dbman = $DB->get_manager();
        $dbman->add_field($table,$field);
    }
    
    /**
     * It returns the table columns
     * @global type $DB
     * @param type $table
     * @return array 
     */
    private static function get_table_columns($table){
        global $DB;
        $columns = $DB->get_columns($table);
        foreach ($columns as $id=>$key){
            $res[]=$id;
        }
        return($res);        
    }
    
    /**
     * It returns the user participation
     * @global type $CFG
     * @global type $DB
     * @param type $userid
     * @param type $start_time
     * @param type $end_time
     * @return type 
     */
    private function get_user_analytics($userid,$start_time=0,$end_time=9999999999){
        global $CFG,$DB; 
		$results='';
        $mods=$this->course_info->get_all_mods();    
        $modules = $this->course_info->get_used_mods();    
        $sections = $this->course_info->get_all_sections();
        $numsections=$this->course_info->get_numsections();       
        for ($i=0; $i<=$numsections; $i++) {            
            if (isset($sections[$i])) {   // should always be true                                 
                $section = $sections[$i];                
                if ($section->sequence) {	            	
                   $sectionmods = explode(",", $section->sequence);					   
                   foreach ($sectionmods as $sectionmod) {					   		                 	
                        if (empty($mods[$sectionmod])) {
                            continue;
                        } 
                        $mod = $mods[$sectionmod];

                        if (empty($mod->visible) || strcmp($mod->modname,'label')==0) {
                            continue;
                        }
                        $instance = $DB->get_record("$mod->modname", array('id'=>$mod->instance));                        
                        $porcentaje=$this->course_info->get_numviews_by_user($userid,$mod,$instance,$start_time,$end_time); 
                        if (!isset($results[$mod->modname][$i]))
                                $results[$mod->modname][$i]=0;
                        $results[$mod->modname][$i]+=$porcentaje;                    							
                   }				   
                }                
            }
        }
        return $results;
    }
 
    /**
     * It returns the total participation
     * @global type $CFG
     * @global type $DB
     * @param type $start_time
     * @param type $end_time
     * @return type 
     */
    private function get_total_analytics($start_time=0,$end_time=9999999999){
        global $CFG,$DB;    
		$results=array();
        $mods=$this->course_info->get_all_mods();
        $sections = $this->course_info->get_all_sections();		
        $numsections=$this->course_info->get_numsections();
        for ($i=0; $i<=$numsections; $i++) {           
            if (isset($sections[$i])) {   // should always be true                   
                $section = $sections[$i];
                if ($section->sequence) {
                    $sectionmods = explode(",", $section->sequence);					   
                    foreach ($sectionmods as $sectionmod) {
                        if (empty($mods[$sectionmod])) {
                            continue;
                        } 
                        $mod = $mods[$sectionmod];
                        if (empty($mod->visible) || strcmp($mod->modname,'label')==0) {
                            continue;
                        }
                        $instance = $DB->get_record("$mod->modname", array('id'=>$mod->instance));

                        $total_analytics_recommendations=$this->course_info->get_numviews($mod,$instance,$start_time,$end_time);
                        if (!isset($results[$mod->modname][$i]))
                                $results[$mod->modname][$i]=0;
                        $results[$mod->modname][$i]+=$total_analytics_recommendations;							
                    }					   
                }                    
            }
        }
        return $results;
    }
   	
    /**
     * It returns true if a table exists
     * @global type $DB
     * @param type $table
     * @return type 
     */
    public static function exist_table($table){
        global $DB;
        $dbman = $DB->get_manager();
        if ($dbman->table_exists($table)) {
            return true;
        }
        return false;
    }   
    
    /**
     * It returns the courses which have the analytics and recommendations module istalled
     * @global type $DB
     * @return type 
     */
    public static function get_courses(){
        global $DB;
        $dbman = $DB->get_manager();
        $courses=get_courses('1');
        $coursenames=array();

        foreach ($courses as $course) {
            if ($dbman->table_exists('analytics_recommendations_total_'.$course->id)){
                $coursenames[$course->id]=$course->fullname.' ('.$course->shortname.')';
            }
        }
        return $coursenames;
    }
    
    /**
     * It returns the reference course
     * @global type $CFG
     * @global type $DB
     * @param type $course
     * @return int 
	 */
    public static function get_reference_course($course){    
        global $CFG, $DB;
        $sql='SELECT reference_course FROM '.$CFG->prefix.'analytics_recommendations_total_'.$course->id;
        if ($reference_course=$DB->get_field_sql($sql))	
                return $reference_course;  
    }
    
    /**
     * It returns the total participation
     * @global type $CFG
     * @global type $DB
     * @param type $course
     * @return type 
     */
    public function get_totals(){
        global $CFG,$DB;
        $sql='SELECT * FROM `'.$CFG->prefix.'analytics_recommendations_total_'.$this->course_info->get_course()->id.'`';

        $res=$DB->get_records_sql($sql);

        return (array)current($res);
    }

    /**
     * It returns the last time modified
     * @global type $CFG
     * @global type $DB
     * @return type 
     */
    public function get_timemodified(){
        global $CFG,$DB;
        $sql='SELECT timemodified FROM `'.$CFG->prefix.'analytics_recommendations_total_'.$this->course_info->get_course()->id.'`';
        $timemodified=$DB->get_field_sql($sql);
        return $timemodified;
    }    
    
    /**
     * It returns a participation percentage
     * @global object $CFG
     * @global object $DB
     * @param $item
     * @param $course
     * @param $userid
     * @return float 
     */
    public function get_percentage($item,$userid){
        global $CFG,$DB;	
        // Obtenemos los alumnos del curso
        $sql='SELECT '.$item.' FROM '.$CFG->prefix.'analytics_recommendations_'.$this->course_info->get_course()->id.' WHERE user='.$userid;
        if ($percentage=$DB->get_field_sql($sql))	
                return $percentage;  
    }            

    /**
     * It updates the last time modified
     * @global object $CFG
     * @global object $DB
     * @param $time   
     */
    public function update_timemodified($time){
        global $CFG,$DB;        
        $DB->set_field('analytics_recommendations_total_'.$this->course_info->get_course()->id,'timemodified',$time);       
    }  
    
    /**
     * It updates the reference course id
     * @global object $CFG
     * @global object $DB
     * @param $reference_course    
     */
    public static function update_reference_course($course,$reference_course){
        global $CFG,$DB;         
        $DB->set_field('analytics_recommendations_total_'.$course->id,'reference_course',$reference_course);        
    }   

    /**
     * It creates the table total
     * @global object $CFG
     * @global object $DB
     * @return boolean 
     */
    public function create_table_total(){
        global $CFG,$DB;
        
        $table = new xmldb_table('analytics_recommendations_total_'.$this->course_info->get_course()->id);

        $field = new xmldb_field('id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true, null, null);
        $table->addField($field);

        $field = new xmldb_field('timemodified');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,false, false, 0, null);
        $table->addField($field);

        $field = new xmldb_field('reference_course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, false, false, 0, null);
        $table->addField($field);

        $mods = $this->course_info->get_all_mods();    
        $sections = $this->course_info->get_all_sections();
        
        // Only one instance per module and section
        $course_mods=array();
        $numsections=$this->course_info->get_numsections();
        for ($i=0; $i<=$numsections; $i++){			
            $section = $sections[$i];
            $sectionmods = explode(",", $section->sequence);
            // For each section mod
            foreach ($sectionmods as $sectionmod) {	
                if (empty($mods[$sectionmod])) {
                    continue;
                }
                // modname + sectionid
                $mod = $mods[$sectionmod];
                if (empty($mod->visible) || strcmp($mod->modname,'label')==0) {
                        continue;
                }
                $id=$mod->modname."_".$i;
                
                if (!isset($course_mods[$id])){
                    $field = new xmldb_field($id);
                    $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, false, false, '0', null);
                    $table->addField($field);
                }
                $course_mods[$id]=$id;				
                $instance = $DB->get_record("$mod->modname", array('id'=>$mod->instance));
                // If the mod has grade ...
                if (isset ($instance->grade)){
                    $field = new xmldb_field('grade_'.$mod->modname.'_'.$instance->id);
                    $field->set_attributes(XMLDB_TYPE_NUMBER, '5,2', null, false, false, '0', null);
                    $table->addField($field);
                }
            }
        }
        $key = new xmldb_key('PRIMARY');
        $key->set_attributes(XMLDB_KEY_PRIMARY, array('id'));
        $table->addKey($key);
        $dbman = $DB->get_manager();
        $status = $dbman->create_table($table);

        if (!$status){
            return true;
        }
        return false;		
    }
    
    /**
     * It inserts the course students
     * @global type $CFG
     * @global type $DB 
     */
    public function insert_users(){
        global $CFG,$DB;
        
        // Get course students
        $users=$this->course_info->get_course_students();
        foreach ($users as $userid) {            
            $row = new stdClass();
            $row->user = $userid;
            $DB->insert_record('analytics_recommendations_'.$this->course_info->get_course()->id, $row);            
        }
    }
    
    private function insert_user($userid){
        global $CFG,$DB;
        $row = new stdClass();
        $row->user = $userid;
        $DB->insert_record('analytics_recommendations_'.$this->course_info->get_course()->id, $row); 
    }
    
    private function exist_user($userid){
         global $CFG,$DB;
         return $DB->record_exists('analytics_recommendations_'.$this->course_info->get_course()->id, array('user'=>$userid));
    }
    
    /**
     * It inserts the refernce course
     * @global object $DB
     * @param $courseid 
     */
     public function insert_reference_course($courseid){
        global $DB,$CFG;
        $row = new stdClass();
        $row->timemodified = time();
        $row->reference_course = $courseid;	
        $DB->insert_record('analytics_recommendations_total_'.$this->course_info->get_course()->id, $row);		
    }
    
    /**
     * It initializes the analytics and recommendation table
     * @global object $CFG
     * @global object $DB 
     */
    public function init_table(){
        global $CFG,$DB;  
		$results=array();
        $users=$this->course_info->get_course_students();        
        $modulos = $this->course_info->get_used_mods();
	$numsections=$this->course_info->get_numsections();
        // If the course has users and modules
        if (isset($users) and isset($modulos) and $users!=NULL and $modulos!=NULL){
            //Get time modified	
            $end_time=$this->get_timemodified();           
            $totales_act=$this->get_totals();            
            foreach ($users as $userid) {
                $results=$this->get_user_analytics($userid,0,$end_time);                
                for($i=0;$i<count($modulos);$i++){	
                    for($j=0;$j<=$numsections;$j++){    
                        if (isset($results[$modulos[$i]][$j])){
                            $res=$results[$modulos[$i]][$j];
                            $tot=$totales_act[$modulos[$i].'_'.$j];
                            if (isset($tot) && $tot!=0 && ($results[$modulos[$i]][$j]=="\0" || $results[$modulos[$i]][$j]!="0")){
                                $DB->set_field('analytics_recommendations_'.$this->course_info->get_course()->id,$modulos[$i].'_'.$j,$res*100/$tot,array('user'=>$userid));
                            }else if(isset($tot) && $tot==0){
                                $DB->set_field('analytics_recommendations_'.$this->course_info->get_course()->id,$modulos[$i].'_'.$j,0,array('user'=>$userid));
                            }
                        }
                    }
                }
            }            
            // Uptate grade activities
            $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'courseid'=>$this->course_info->get_course()->id));
         
            foreach ($grade_items as $grade_item) {
                $items[$grade_item->id]=$grade_item;								
            }
            // a iterador for grade_grades
            $gui = new graded_users_iterator($this->course_info->get_course(), $grade_items);
            $gui->init();
            while ($user_grades=$gui->next_user()){
                    foreach ($user_grades->grades as $grade) {                           
                        $DB->set_field('analytics_recommendations_'.$this->course_info->get_course()->id,'grade_'.$items[$grade->itemid]->itemmodule.'_'.$items[$grade->itemid]->iteminstance,round($grade->finalgrade,2),array('user'=>$grade->userid));						   
                    }
            }			
        }                    
    }
    
    /**
     * It initializes the total analytics and recommendations table
     * @global object $CFG
     * @global object $DB
     * @param $course
     * @param $end_time 
     */
    public function init_table_total($end_time){
        global $CFG,$DB;        
        $start_time=$this->get_timemodified();         
        $modulos = $this->course_info->get_used_mods();
        $results=$this->get_total_analytics(0,$end_time);		
	$numsections=$this->course_info->get_numsections();        
        for($i=0;$i<count($modulos);$i++)	
            for($j=0;$j<=$numsections;$j++){
				if (isset($results[$modulos[$i]][$j]) && !is_null($results[$modulos[$i]][$j])){
                    $DB->set_field('analytics_recommendations_total_'.$this->course_info->get_course()->id,$modulos[$i].'_'.$j,$results[$modulos[$i]][$j]);
            }					
        }	
        // Update mod grades
        $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'courseid'=>$this->course_info->get_course()->id));
        foreach ($grade_items as $grade_item) {
            $DB->set_field('analytics_recommendations_total_'.$this->course_info->get_course()->id,'grade_'.$grade_item->itemmodule.'_'.$grade_item->iteminstance,round($grade_item->grademax,2));
        }
    }
    
    /**
     * It updates the analytics and recommendations table
     * @global object $CFG
     * @global object $DB
     * @param $start_time
     * @param $end_time
     * @param $totales_ant 
     */
    public function update_table_cron($start_time,$end_time,$totales_ant) {
        global $CFG,$DB;
        $totales_act=$this->get_totals();        
        $users=$this->course_info->get_course_students();
        $modulos = $this->course_info->get_used_mods();
	$numsections=$this->course_info->get_numsections();

            // Check if the course have users, mods and the table exists
        if (isset($users) and isset($modulos) and !is_null($users) and !is_null($modulos) and self::exist_table("analytics_recommendations_{$this->course_info->get_course()->id}")) {
            foreach ($users as $userid) {
                if (!$this->exist_user($userid)){
                    $this->insert_user($userid);
                }
                $results = $this->get_user_analytics($userid,$start_time,$end_time);
                for ($i = 0; $i < count($modulos); $i++) {
                    for ($j = 0; $j <= $numsections; $j++) {
                        if (isset($totales_act[$modulos[$i].'_'.$j])){
                            $t_ant=$totales_ant[$modulos[$i].'_'.$j]/100;
                            if ($totales_act[$modulos[$i].'_'.$j]!=0)
                                $t_act=100/$totales_act[$modulos[$i].'_'.$j];   
                            else
                                $t_act=0;
                            $percentage=$this->get_percentage($modulos[$i]."_".$j,$userid);
                            $DB->set_field('analytics_recommendations_'.$this->course_info->get_course()->id,
                                    $modulos[$i].'_'.$j,round((($percentage*$t_ant)+$results[$modulos[$i]][$j])*$t_act,2),
                                    array('user'=>$userid));									
                        }
                    }
                }
            }
            // Update mod grades
            $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'courseid'=>$this->course_info->get_course()->id));
            // ids of grade_items
            foreach ($grade_items as $grade_item) {
                $items[$grade_item->id] = $grade_item;
            }
            // new iterador for grade_grades
            $gui = new graded_users_iterator($this->course_info->get_course(), $grade_items);
            $gui->init();
            while ($user_grades = $gui->next_user()) {
                foreach ($user_grades->grades as $grade) {
                    if ($grade->finalgrade != NULL) {
                        $DB->set_field('analytics_recommendations_'.$this->course_info->get_course()->id,'grade_'.$items[$grade->itemid]->itemmodule.'_'.$items[$grade->itemid]->iteminstance,round($grade->finalgrade,2),array('user'=>$grade->userid));		   		
                    }
                }
            }
        }	
    }
    
    /**
     * It updates the analytics and recommendations total table
     * @global object $CFG
     * @global object $DB
     * @param $course
     * @param $start_time
     * @param $end_time 
     */
    public function update_table_total_cron($start_time,$end_time) {
        global $CFG,$DB;
        $modulos = $this->course_info->get_used_mods();
        $numsections=$this->course_info->get_numsections();
        // if the table exists
        if (self::exist_table("analytics_recommendations_total_{$this->course_info->get_course()->id}")) {
            $results = $this->get_total_analytics($start_time, $end_time);
            for ($i = 0; $i < count($modulos); $i++){
                for ($j = 0; $j <= $numsections; $j++) {
                   if (isset($results[$modulos[$i]][$j]) && !is_null($results[$modulos[$i]][$j]) && $results[$modulos[$i]][$j]!=0) {
                        $ant=$DB->get_field_sql("SELECT {$modulos[$i]}_{$j} FROM `{$CFG->prefix}analytics_recommendations_total_{$this->course_info->get_course()->id}`");
                        $total=$ant+$results[$modulos[$i]][$j];                                                
                        $DB->set_field('analytics_recommendations_total_'.$this->course_info->get_course()->id,$modulos[$i].'_'.$j,$total);                        
                    }
                }
            }
            
            // Update grades
            $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'courseid'=>$this->course_info->get_course()->id));
            foreach ($grade_items as $grade_item) { 
                $DB->set_field('analytics_recommendations_total_'.$this->course_info->get_course()->id,
                        'grade_'.$grade_item->itemmodule.'_'.$grade_item->iteminstance,round($grade_item->grademax,2));
            }
            
         }
    }

    /**
     * It updates the analytics and recommendations structure table
     * @global object $CFG
     * @global object $DB 
     */
    public function update_structure_table(){
        global $CFG,$DB;       
              
        // Course mods
        $mods = $this->course_info->get_all_mods();       
        
        $sections = $this->course_info->get_all_sections();

        // table columns
        $columns=self::get_table_columns("analytics_recommendations_".$this->course_info->get_course()->id);
        
        // Array for check and don`t insert the mod twice.
        // if there are more than one instance per section
        $course_mods=array();
        // After column 2 -> insert new columns
        $anterior=$columns[2];
        $numsections=$this->course_info->get_numsections();
        for ($i=0; $i<=$numsections; $i++){			
            $section = $sections[$i];
            $sectionmods = explode(",", $section->sequence);
            // For each module for each section
            foreach ($sectionmods as $sectionmod) {	
                
                if (empty($mods[$sectionmod])) {
                        continue;
                }
                // modname + sectionid
                $mod = $mods[$sectionmod];
                
                if (empty($mod->visible) || strcmp($mod->modname,'label')==0) {
                        continue;
                }
                $id=$mod->modname."_".$i;
                // if it isn't in the database
                if (!isset($course_mods[$id]) and !array_search ($id,$columns) ){
                    self::alter_table('analytics_recommendations_'.$this->course_info->get_course()->id, $id, $anterior, 'number');
                    self::alter_table('analytics_recommendations_total_'.$this->course_info->get_course()->id, $id, $anterior, 'integer');
                    $course_mods[$id]=$id;
                }
                $anterior=$id;
                $instance = $DB->get_record("$mod->modname", array('id'=>$mod->instance));
                // For this intance, if the mod has grade
                if (isset ($instance->grade)){
                    if (!array_search ('grade_'.$mod->modname.'_'.$instance->id,$columns) ){
                            self::alter_table('analytics_recommendations_'.$this->course_info->get_course()->id, 'grade_'.$mod->modname.'_'.$instance->id, $anterior,'number');				
                            self::alter_table('analytics_recommendations_total_'.$this->course_info->get_course()->id, 'grade_'.$mod->modname.'_'.$instance->id, $anterior,'number');
                            $anterior='grade_'.$mod->modname.'_'.$instance->id;
                    }					
                }
            }
        }	
    }
    
    /**
     * It creates a new analytics and recommendations table
     * @global object $CFG
     * @global object $DB
     * @return boolean 
     */
    public function create_table(){
        global $CFG,$DB;
        $table = new xmldb_table('analytics_recommendations_'.$this->course_info->get_course()->id);
        $field = new xmldb_field('id');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true, null, null);
        $table->addField($field);
        $field = new xmldb_field('user');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, '0', null);
        $table->addField($field);
        $mods = $this->course_info->get_all_mods();    
        $sections = $this->course_info->get_all_sections();
        
        // Array for check and don`t insert the mod twice.
        // if there are more than one instance per section
        $course_mods=array();
        $numsections=$this->course_info->get_numsections();
		
        for ($i=0; $i<=$numsections; $i++){			
            $section = $sections[$i];
            $sectionmods = explode(",", $section->sequence);
            // for each module and for each section
            foreach ($sectionmods as $sectionmod) {	
                if (empty($mods[$sectionmod])) {
                        continue;
                }
                // modname + sectionid
                $mod = $mods[$sectionmod];
                if (empty($mod->visible) || strcmp($mod->modname,'label')==0) {
                        continue;
                }
                $id=$mod->modname."_".$i;
                // Add a new column
                if (!isset($course_mods[$id])){
                    $field = new xmldb_field($id);
                    $field->set_attributes(XMLDB_TYPE_NUMBER, '5,2', null, NULL_ALLOWED, false, '0', null);
                    $table->addField($field);                                    
                }
                $course_mods[$id]=$id;				
                $instance = $DB->get_record("$mod->modname", array('id'=>$mod->instance));
                // For this instance if the mod has grade ...
                if (isset ($instance->grade)){
                    $field = new xmldb_field('grade_'.$mod->modname.'_'.$instance->id);
                    $field->set_attributes(XMLDB_TYPE_NUMBER, '5,2', null, NULL_ALLOWED, false, '0', null);
                    $table->addField($field);                                        
                }
            }
        }
        $key = new xmldb_key('PRIMARY');
        $key->set_attributes(XMLDB_KEY_PRIMARY, array('id'));
        $table->addKey($key);
        $dbman = $DB->get_manager();
        $status = $dbman->create_table($table);

        if (!$status){
           return true;
        }
        return false;	
    }
}
?>