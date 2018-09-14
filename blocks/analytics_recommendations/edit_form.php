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
class block_analytics_recommendations_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
        global $CFG,$COURSE;        
        require_once($CFG->dirroot.'/blocks/analytics_recommendations/lib/module_maintenance.php');
        if (module_maintenance::exist_table('analytics_recommendations_'.$COURSE->id)){
            $courses=module_maintenance::get_courses(); 
            
            unset($courses[$COURSE->id]);
            $courses[0]=get_string('none','block_analytics_recommendations');

            // Header 
            $mform->addElement('header','cabecera', get_string('analytics_recommendations','block_analytics_recommendations'));        

            // Reference course
            $mform->addElement('select', 'config_ref_course', get_string('reference_course','block_analytics_recommendations'), $courses);
            $mform->setDefault('config_ref_course', module_maintenance::get_reference_course($COURSE));
            $mform->addHelpButton('config_ref_course','setup_form','block_analytics_recommendations');  

            $mform->addElement('selectyesno', 'config_show_recommendations', get_string('show_recommendations', 'block_analytics_recommendations'));
            $mform->setDefault('config_show_recommendations', 1);
            $mform->addHelpButton('config_show_recommendations','show_recommendations','block_analytics_recommendations');  
        } 
    }
}
