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

class request {
    private $courseid;
    private $userid;
    private $groupid;
    private $role;

    public function __construct($courseid, $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
        $this->roles = $this->get_roles();
        $this->groupid = $this->get_group();
    }

    private function get_roles() {
        $req = [
            'session' => '0',
            'query' => 'roles_member',
            'course' => $this->courseid,
            'id' => $this->anonymize($this->userid)
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
        $role_ids = [];

        foreach ($data as $item) {
            array_push($role_ids, $item->role->id);
        }

        return $role_ids;
    }

    private function get_group() {
        $req = [
            'session' => '0',
            'query' => 'groups!member',
            'course' => $this->courseid,
            'id' => $this->anonymize($this->userid)
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        return $data[1]->group->id;
    }

    public function get_group_posts_data() {
        global $USER;
        $posts = $this->get_group_posts();
        $data = array();
        $counter = 1;
        $userid = $this->anonymize($USER->id);

        foreach ($posts as $post) {
            $postmsg = \html_to_text($post->post->value);
            $postmsg = (strlen($postmsg) > 25) ? mb_substr($postmsg, 0, 25, 'UTF-8') . '...' : $postmsg;
            $color = '#E63533';

            if (isset($post->post->visitors)) {
                if (($post->post->visitors != null) && is_array($post->post->visitors)) {
                    foreach ($post->post->visitors as $visitor) {
                        if ($visitor->member->id == $userid) {
                            $color = '#17375D';
                        }
                    }
                }
            }

            if ($post->post->members->member->id == $userid) {
                $color = '#AEAEAE';
            }

            $item = [
                'id' => $counter,
                'date' => $post->post->created,
                'postid' => $post->post->parent->discussion->id . '#p' . $post->post->id,
                'postmsg' => $postmsg,
                'discussionid' => $post->post->id,
                'color' => $color
            ];

            array_push($data, $item);
            $counter++;
        }

        $data = $this->format_date($data);
        $preparation = $this->get_preparation();

        $return = [
            'hasdata' => (count($data) > 0) ? true : false,
            'posts' => $data,
            'preparation' => ($preparation <= 0.5) ? 0 : $preparation,
            'task_period' => $this->get_task_period()
        ];

        return $return;
    }

    private function get_task_period() {
        $req = [
            'session' => '0',
            'query' => 'tasks_group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        return $data[1]->task->period;
    }

    private function get_preparation() {
        $req = [
            'session' => '0',
            'query' => 'preparation',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        return $data[1]->preparation->value;
    }

    private function get_group_posts() {
        $group_posts = array();

        $req = [
            'session' => '0',
            'query' => 'posts!group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $posts = $this->serialize($response);

        foreach ($posts as $post) {
            $group_posts[$post->post->created] = $post;
        }

        return $group_posts;
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

    private function format_date($data) {
        for ($i = 0; $i < count($data); $i++) {
            $timestamp = $data[$i]['date'];
            $data[$i]['date_formatted'] = date('d.m.', $timestamp);

            $timestamp_diff = time() - $timestamp;
            $diff = round(($timestamp_diff / 3600 / 24));

            if ($diff < 1) {
                $data[$i]['since'] = round(($timestamp_diff / 3600)) . 'Std.';
            } else {
                $data[$i]['since'] = $diff . 'Tg.';
            }
        }

        return $data;
    }
}
