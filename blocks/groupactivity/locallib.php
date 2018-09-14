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
 * @package    block_groupactivity
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace groupactivity;

function curl_request() {
    $data = array('user' => '3', 'course' => '99');
    $data_string = json_encode($data);

    $url = \get_config('groupactivity', 'curl_url');
    $port = \get_config('groupactivity', 'curl_port');
    $token = \get_config('groupactivity', 'secrettoken');

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_PORT => $port,
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
        CURLOPT_POST => false,
        CURLOPT_POSTFIELDS => $data_string,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Token ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $rough_content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    return array(
        "header" => $header,
        "content" => $rough_content,
    );
}

function get_guiding_text($levels, $data_count) {
    global $USER;

    $value = 0.15;
    $last_val = 0;

    foreach ($levels as $key => $leveltext) {
        if ($value >= $last_val && $value <= $key) {
            $return = str_replace('{g_user}', $USER->firstname, $leveltext);
            $return = str_replace('{n}', $data_count, $return);

            $last_val = $key;
        }
    }

    return $return;
}

function dump_data() {
    $data = [
        ['categories' => 'KW32',
            'values' => [
                [
                    'value' => '150',
                    'rate' => 'Eugen Ebel'
                ],
                [
                    'value' => '50',
                    'rate' => 'Max Msuter'
                ],
                [
                    'value' => '20',
                    'rate' => 'Jenny Johe'
                ],
            ]
        ],
        ['categories' => 'KW33',
            'values' => [
                [
                    'value' => '175',
                    'rate' => 'Eugen Ebel'
                ],
                [
                    'value' => '80',
                    'rate' => 'Max Msuter'
                ],
                [
                    'value' => '20',
                    'rate' => 'Jenny Johe'
                ],
            ]
        ]
    ];

    return array(
        "header" => 'head',
        "content" => $data,
    );
}

function get_data() {
    global $SESSION;

    if (!isset($SESSION->groupactivity) || time_to_poll()) {
        //$request = curl_request();
        $request = dump_data();
        $SESSION->groupactivity->data = $request['content'];
        $SESSION->groupactivity->pollstamp = time();
    }

    return $SESSION->groupactivity->data;
}

function time_to_poll() {
    global $SESSION;

    $now = time();
    $intervall = \get_config('groupactivity', 'intervall');
    $last_poll = $SESSION->groupactivity->pollstamp;

    if (($last_poll + $intervall) < $now) {
        return true;
    } else {
        return false;
    }
}

function format_data($data) {
    for ($i = 0; $i < count($data); $i++) {
        $timestamp = $data[$i]['date'];
        $data[$i]['date'] = date('d.m.', $timestamp);

        $timestamp_diff = time() - $timestamp;
        $diff = round(($timestamp_diff / 3600));
        $data[$i]['since'] = '+' . $diff . 'Std.';
    }

    return $data;
}