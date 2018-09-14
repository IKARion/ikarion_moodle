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
 * Description of setup_form
 *
 * @author Cristina
 */
class choose_users_form extends moodleform{
   /**
    * Form definition
    * @global object $CFG 
    */
    function definition() {
        global $CFG;
        
        $mform    = $this->_form;
        
        // Header
        $mform->addElement('header','cabecera', get_string('choose_students','block_analytics_recommendations'));
        $mform->addHelpButton('cabecera','chooseusers_form','block_analytics_recommendations');               
        // Reference course
        $select = &$mform->addElement('select', 'users', get_string('choose_students','block_analytics_recommendations'), $this->_customdata['students'],'size="'.count($this->_customdata['students']).'"');
        $select->setMultiple(true);
        $mform->addRule('users',null,'required', null,'client');
        
        
        // Current course id
        $mform->addElement('hidden', 'id');
        $mform->setDefault('id',$this->_customdata['id']);
        
        // Current tab
        $mform->addElement('hidden', 'a');        
        $mform->setDefault('a','a2');
        
        $mform->addElement('hidden', 'form');        
        $mform->setDefault('form','chooseusers');
     
        $this->add_action_buttons(false,  get_string('show','block_analytics_recommendations'));
     
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}

?>
