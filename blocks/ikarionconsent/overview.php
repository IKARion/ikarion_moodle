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
 * Overview
 *
 * @package    block_ikarionconsent
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author       Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

global $PAGE, $DB, $CFG, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url(new \moodle_url('/blocks/ikarionconsent/overview.php', array('courseid' => $courseid)));
$PAGE->set_title(get_string('overview', 'block_ikarionconsent'));
$PAGE->set_heading(get_string('overview', 'block_ikarionconsent'));

$context = context_course::instance($courseid);

$enrolled_users = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname', 'u.lastname');
$users = array();

if (!empty($enrolled_users)) {
    foreach ($enrolled_users as $key => $user) {
        $consent = $DB->get_record('ikarionconsent', array('userid' => $user->id, 'choice' => 1));

        if (!empty($consent)) {
            $enrolled_users[$key]->id = base64_encode(openssl_encrypt($user->id, $CFG->encryptmethod, $CFG->encryptkey));
            array_push($users, $user);
        }
    }

    $has_users = 1;
} else {
    $has_users = 0;
}

$data = array(
    'hasusers' => $has_users,
    'users' => $users,
    'backurl' => new moodle_url('/course/view.php', array('id' => $courseid))
);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_ikarionconsent/overview', $data);
echo $OUTPUT->footer();