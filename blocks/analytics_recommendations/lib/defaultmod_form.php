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
class defaultmod_form extends moodleform{
   
    /**
    * Form definition
    * @global object $CFG 
    */
   function definition() {
        global $CFG;        
        $mform    = $this->_form;        
        // Header
        //$mform->addElement('header','setup', get_string('analytics_recommendations','block_analytics_recommendations'));
        //$mform->addElement('html', '<div class="qheader">');
        $mform->addElement('html', '<div style="margin:0 0 0 160px;">');
        // Reference course
        $mform->addElement('select', 'defaultmod',get_string('choose_activity','block_analytics_recommendations'), $this->_customdata['mods'],'onchange="submit();"');
        $mform->setDefault('defaultmod', $this->_customdata['default']);
        $mform->addHelpButton('defaultmod','defaultmod_form','block_analytics_recommendations');           
        
        // Current course id
        $mform->addElement('hidden', 'id');
        $mform->setDefault('id',$this->_customdata['id']);
        
        // User id
        if (isset($this->_customdata['user'])){
            $mform->addElement('hidden', 'user');
            $mform->setDefault('user',$this->_customdata['user']);
        }               
        // Users
        if (isset($this->_customdata['users'])){
            $mform->addElement('hidden', 'users',urlencode(serialize($this->_customdata['users'])));
            $mform->setType('users', PARAM_FILE);   
        }
        $mform->addElement('html', '</div>');
    }
}

?>
