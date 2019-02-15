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
 * ikarionxps locallib
 *
 * @package    local_ikarionxps
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_ikarionxps_send_event($data) {
    global $CFG;
    
    $options = array(
        CURLOPT_URL => $CFG->xpsurl,
        CURLOPT_PORT => $CFG->xpsport,
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER => false,     //return headers in addition to content
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING => "",       // handle all encodings
        CURLOPT_AUTOREFERER => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT => 120,      // timeout on response
        CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
        CURLINFO_HEADER_OUT => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Token ' . $CFG->xpstoken,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $rough_content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    /* uncomment for local loggin /classes/event.log
    global $CFG;
    $logfile = $CFG->dirroot . '/local/ikarionxps/classes/event.log';
    file_put_contents($logfile, 'event: ' . $data . PHP_EOL . PHP_EOL, FILE_APPEND);
    */

    /*
    $content = json_decode($rough_content);
    if ($content->size < 0 || $content->add == false) {
        $err = 1;
    }
    */

    return $err;
}

function local_ikarionxps_send_stored_events() {
    global $DB;

    $records = $DB->get_records('ikarionxps');

    foreach ($records as $record) {
        $error = local_ikarionxps_send_event($record->event);

        if ($error == 0) {
            $DB->delete_records('ikarionxps', array('id' => $record->id));
        }
    }
}