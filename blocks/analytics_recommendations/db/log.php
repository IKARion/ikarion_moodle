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

defined('MOODLE_INTERNAL') or die("Direct access to this location is not allowed.");

$logs = array(
   array('module'=>'analytics', 'action'=>'setup', 'mtable'=>'analytics_recommendations', 'field'=>''),
   array('module'=>'analytics', 'action'=>'all_analytics', 'mtable'=>'analytics_recommendations', 'field'=>''),
   array('module'=>'analytics', 'action'=>'my_analytics', 'mtable'=>'analytics_recommendations', 'field'=>''),
   array('module'=>'analytics', 'action'=>'all_recommendations', 'mtable'=>'analytics_recommendations', 'field'=>''),
   array('module'=>'analytics', 'action'=>'my_recommendations', 'mtable'=>'analytics_recommendations', 'field'=>'')
);

?>