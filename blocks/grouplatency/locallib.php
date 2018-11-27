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
 * @package    block_grouplatency
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace grouplatency;

function get_role($courseid) {
    global $DB, $USER;

    $req = [
        'session' => '0',
        'query' => 'roles_member',
        'course' => "$courseid",
        'id' => anonymize($USER->id)
    ];

    $response = curl_request($req);
    $data = serialize($response);
    $roleid = '';

    foreach ($data as $item) {
        $roleid = $item->role->id;
    }

    $role = $DB->get_record('role', array('id' => $roleid));

    return $role->shortname;
}

function get_group_posts_data($courseid) {
    global $DB;

    $posts = get_group_posts($courseid);
    $data = array();
    $counter = 0;

    ksort($posts, SORT_NUMERIC);

    foreach ($posts as $post) {
        $userid = deanonymize($post->post->members[0]->member->id);
        $user = $DB->get_record('user', array('id' => $userid), 'id, firstname, lastname');

        $postmsg = \html_to_text($post->post->value);
        $postmsg = (strlen($postmsg) > 120) ? substr($postmsg, 0, 120) . '...' : $postmsg;

        $item = [
            'id' => $counter,
            'date' => $post->post->time,
            'postid' => $post->post->id,
            'postmsg' => $postmsg,
            'discussionid' => $post->post->discussion->id,
            'userid' => $userid,
            'user' => $user->firstname . ' ' . $user->lastname,
            'courseid' => $courseid
        ];

        array_push($data, $item);
        $counter++;

    }

    $data = array_slice($data, -3, 3);
    $data = format_date($data);

    return $data;
}

function get_group_posts($courseid) {
    $groupid = get_group($courseid);
    $group_posts = array();

    $req = [
        'session' => '0',
        'query' => 'posts_group',
        'course' => "$courseid",
        'id' => "$groupid"
    ];

    $response = curl_request($req);
    $posts = serialize($response);

    foreach ($posts as $post) {
        $group_posts[$post->post->time] = $post;
    }

    return $group_posts;
}

function get_activity_count($courseid) {
    $total = 0;
    $groupid = get_group($courseid);

    $req = [
        'session' => '0',
        'query' => 'contents_group',
        'course' => "$courseid",
        'id' => $groupid
    ];

    $response = curl_request($req);
    $data = serialize($response);

    foreach ($data as $item) {
        if (isset($item->post)) {
            foreach ($item->post->members as $pm) {
                $patches = count($pm->patches);
                $total += $patches;
            }
        }
        if (isset($item->page)) {
            foreach ($item->page->members as $pm) {
                $patches = count($pm->patches);
                $total += $patches;
            }
        }
    }

    return $total;
}

function show_mirroring($courseid) {
    $activities = get_activity_count($courseid);

    if ($activities >= 3) {
        return true;
    } else {
        return false;
    }
}

function show_guiding($courseid) {
    $groupid = get_group($courseid);
    $activities = get_activity_count($courseid);

    $req = [
        'session' => '0',
        'query' => 'response',
        'course' => "$courseid",
        'id' => "$groupid"
    ];

    $response = curl_request($req);
    $data = serialize($response);

    if ($data[1]->response->value != 1) {
        return false;
    } else {
        if ($activities >= 3) {
            return true;
        } else {
            return false;
        }
    }
}

function get_group($courseid) {
    global $USER;

    $req = [
        'session' => '0',
        'query' => 'groups_member',
        'course' => "$courseid",
        'id' => anonymize($USER->id)
    ];

    $response = curl_request($req);
    $data = serialize($response);
    $groupid = '';
    $temp_group = 0;

    foreach ($data as $item) {
        if ($item->group->id > $temp_group) {
            $groupid = $temp_group = $item->group->id;
        }
    }

    return $groupid;
}

function anonymize($data) {
    global $CFG;

    return openssl_encrypt($data, $CFG->encryptmethod, $CFG->encryptkey);
}

function deanonymize($data) {
    global $CFG;

    return openssl_decrypt($data, $CFG->encryptmethod, $CFG->encryptkey);
}

function serialize($response) {
    $data = explode(PHP_EOL, $response['content']);

    unset($data[0]);

    foreach ($data as $key => $item) {
        if (empty($item)) {
            unset($data[$key]);
        } else {
            $data[$key] = json_decode($item);
        }
    }

    return $data;
}

function curl_request($data) {
    $data_string = json_encode($data);

    $url = \get_config('grouplatency', 'curl_url');
    $port = \get_config('grouplatency', 'curl_port');
    $token = \get_config('grouplatency', 'secrettoken');

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
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data_string,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Token ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
        )
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

function get_data() {
    global $SESSION;

    if (!isset($SESSION->grouplatency) || time_to_poll()) {
        //$request = curl_request();
        $request = dump_data();
        $SESSION->grouplatency->data = $request['content'];
        $SESSION->grouplatency->pollstamp = time();
    }

    return $SESSION->grouplatency->data;
}

function time_to_poll() {
    global $SESSION;

    $now = time();
    $intervall = \get_config('grouplatency', 'intervall');
    $last_poll = $SESSION->grouplatency->pollstamp;

    if (($last_poll + $intervall) < $now) {
        return true;
    } else {
        return false;
    }
}

function format_date($data) {
    for ($i = 0; $i < count($data); $i++) {
        $timestamp = $data[$i]['date'];
        $data[$i]['date_formatted'] = date('d.m.', $timestamp);

        $timestamp_diff = time() - $timestamp;
        $diff = round(($timestamp_diff / 3600));

        if ($diff < 1) {
            $data[$i]['since'] = '+ ' . round(($timestamp_diff / 60)) . ' Min.';
        } else {
            $data[$i]['since'] = '+ ' . $diff . ' Std.';
        }
    }

    return $data;
}