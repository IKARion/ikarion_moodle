<?php
// This file is part of Moodle - http://moodle.org/
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
 * ikarionxps task send failed events
 *
 * @package    local_ikarionxps
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ikarionxps\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/ikarionxps/locallib.php');

class send_stored_events extends \core\task\scheduled_task {
    /**
     * (non-PHPdoc)
     *
     * @see \core\task\scheduled_task::get_name()
     */
    public function get_name() {
        return get_string('send_events_task', 'local_ikarionxps');
    }

    /**
     * (non-PHPdoc)
     *
     * @see \core\task\task_base::execute()
     */
    public function execute() {
        local_ikarionxps_send_stored_events();
    }
}