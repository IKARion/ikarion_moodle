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

function get_group_activity_data($courseid) {
    global $DB;

    $members = get_group_members($courseid);
    $data = array();
    $all_mails = '';
    $symbols = [9632, 9733, 9650, 10010, 9670];
    $symbolid = 1;

    foreach ($members as $member) {
        $words = get_wordcount($courseid, $member);
        $user = $DB->get_record('user', array('id' => deanonymize($member)));

        if ($user->lastaccess > (time() - 5 * 60)) {
            $online = 1;
        } else {
            $online = 0;
        }

        $user_data = [
            'symbolid' => $symbolid,
            'name' => $user->firstname . ' ' . $user->lastname,
            'mailto' => $user->email,
            'chatto' => (new \moodle_url('/message/index.php?id=' . $user->id))->out(),
            'online' => $online,
            'usymbol' => '&#' . array_shift($symbols) . ';',
            'profile' => (new \moodle_url('/user/view.php?id=' . $user->id . '&course=' . $courseid))->out(),
            'words_total' => $words['total'],
            'words_forum' => $words['forum'],
            'words_wiki' => $words['wiki'],
        ];

        $all_mails .= $user->email . ';';

        array_push($data, $user_data);
        $symbolid++;
    }

    return [
        'usersdata' => $data,
        'mailtogroup' => $all_mails
    ];
}

function get_wordcount($courseid, $member) {
    $req = [
        'session' => '0',
        'query' => 'contents_member',
        'course' => "$courseid",
        'id' => $member
    ];

    $response = curl_request($req);
    $data = serialize($response);
    $wordcount_total = 0;
    $wordcount_forum = 0;
    $wordcount_wiki = 0;

    foreach ($data as $item) {
        if (isset($item->post)) {
            foreach ($item->post->members as $pm) {
                if ($pm->member->id == $member) {
                    $wordcount_forum += $pm->words->insert;
                    $wordcount_total += $pm->words->insert;
                }
            }
        }
        if (isset($item->page)) {
            foreach ($item->page->members as $pm) {
                if ($pm->member->id == $member) {
                    $wordcount_wiki += $pm->words->insert;
                    $wordcount_total += $pm->words->insert;
                }
            }
        }
    }

    return [
        'total' => $wordcount_total,
        'forum' => $wordcount_forum,
        'wiki' => $wordcount_wiki
    ];
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
        'query' => 'participation',
        'course' => "$courseid",
        'id' => "$groupid"
    ];

    $response = curl_request($req);
    $data = serialize($response);

    if ($data[1]->participation->value != 1) {
        return false;
    } else {
        if ($activities >= 3) {
            return true;
        } else {
            return false;
        }
    }
}

function get_group_members($courseid) {
    $groupid = get_group($courseid);

    $req = [
        'session' => '0',
        'query' => 'group',
        'course' => "$courseid",
        'id' => $groupid
    ];

    $response = curl_request($req);
    $data = serialize($response);
    $members = array();

    foreach ($data as $item) {
        foreach ($item->group->members as $member) {
            array_push($members, $member->member->id);
        }
    }

    return $members;
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
        $data[$i]['date_formatted'] = date('d.m.', $timestamp);

        $timestamp_diff = time() - $timestamp;
        $diff = round(($timestamp_diff / 3600));
        $data[$i]['since'] = '+' . $diff . 'Std.';
    }

    return $data;
}