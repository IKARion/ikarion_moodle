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

namespace groupactivity;

class request {
    private $courseid;
    private $userid;
    private $groupid;
    private $role;
    private $showguiding;
    private $showmirroring;
    private $activitycount;

    public function __construct($courseid, $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
        $this->role = $this->get_role();
        $this->groupid = $this->get_group();
        $this->activitycount = $this->get_activity_count();
        $this->showguiding = $this->show_guiding();
    }

    private function get_role() {
        global $DB;

        $req = [
            'session' => '0',
            'query' => 'roles_member',
            'course' => $this->courseid,
            'id' => $this->anonymize($this->userid)
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        foreach ($data as $item) {
            $roleid = $item->role->id;
            $role = $DB->get_record('role', array('id' => $roleid));

            if ($role->shortname == 'mgma' || $role->shortname == 'mgmb') {
                break;
            }
        }

        return $role->shortname;
    }

    private function get_group() {
        $req = [
            'session' => '0',
            'query' => 'groups_member',
            'course' => $this->courseid,
            'id' => $this->anonymize($this->userid)
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
        $groupid = '';
        $temp_group = 0;

        foreach ($data as $item) {
            if ($item->group->id > $temp_group) {
                $groupid = $temp_group = $item->group->id;
            }
        }

        return $groupid;
    }

    private function show_guiding() {
        $req = [
            'session' => '0',
            'query' => 'participation',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        if ($data[1]->participation->value != 1) {
            return false;
        } else {
            return true;
        }
    }

    private function get_activity_count() {
        $total = 0;

        $req = [
            'session' => '0',
            'query' => 'contents_group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

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

    public function get_group_activity_data() {
        global $DB;

        $members = $this->get_group_members();
        $data = array();
        $all_mails = '';
        $symbols = [9632, 9733, 9650, 10010, 9670];
        $symbolid = 1;

        foreach ($members as $member) {
            $words = $this->get_wordcount($member);
            $user = $DB->get_record('user', array('id' => $this->deanonymize($member)));

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
                'profile' => (new \moodle_url('/user/view.php?id=' . $user->id . '&course=' . $this->courseid))->out(),
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

    private function get_group_members() {
        $req = [
            'session' => '0',
            'query' => 'group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
        $members = array();

        foreach ($data as $item) {
            foreach ($item->group->members as $member) {
                array_push($members, $member->member->id);
            }
        }

        return $members;
    }

    private function get_wordcount($member) {
        $req = [
            'session' => '0',
            'query' => 'contents_member',
            'course' => $this->courseid,
            'id' => $member
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
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

    private function curl_request($data) {
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

    private function anonymize($data) {
        global $CFG;

        return openssl_encrypt($data, $CFG->encryptmethod, $CFG->encryptkey);
    }

    private function deanonymize($data) {
        global $CFG;

        return openssl_decrypt($data, $CFG->encryptmethod, $CFG->encryptkey);
    }

    private function serialize($response) {
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

    public function showguiding() {
        return $this->showguiding;
    }

    public function showmirroring() {
        return $this->showmirroring;
    }

    public function activitycount() {
        return $this->activitycount;
    }

    public function getrole() {
        return $this->role;
    }
}