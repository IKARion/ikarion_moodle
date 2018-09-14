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
 * Analytics Recommendations block capability setup
 *
 * @package    contrib
 * @subpackage block_analytics_recommendations
 * @copyright  2012 Cristina Fern�ndez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../course/lib.php');

require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

require_once($CFG->libdir.'/formslib.php');
require_once('lib/setup_form.php');
require_once('lib/defaultmod_form.php');
require_once('lib/choose_user_form.php');
require_once('lib/choose_users_form.php');

include_once($CFG->libdir.'/graphlib.php');
require_once('lib/my_graph.php');

require_once('lib/gui.php');
require_once('lib/course_info.php');
require_once('lib/module_maintenance.php');
require_once('lib/analytics.php');
require_once('lib/recommendations.php');
?>
