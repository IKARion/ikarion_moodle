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
    private $roles;

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

    public function get_group_activity_data($blockinstance) {
        global $DB;

        $members = $this->get_group_members();
        $hascapability = $this->has_capability($blockinstance);

        $data = $useritems = array();
        $all_mails = '';
        $counter = 1;
        $colors = ['#A71680', '#1D71B8', '#EFC22F', '#9DBE3A', '#59297F'];

        if ($hascapability['selfassess']) {
            $assessments = $this->get_group_assessments();
            $items = $DB->get_records('groupactivity_items', ['blockinstance' => $blockinstance, 'deleted' => 0], '', 'id, shortname');
            $items = array_values($items);
            $maxvalue = $assessments['maxvalue'];
            $type = 'assessments';
        }

        if ($hascapability['participation']) {
            $hascapability['selfassess'] = false;
            $participations = $this->get_group_participations();
            $items = [];
            $forum = new \stdClass();
            $forum->shortname = 'Forum';
            $items[] = $forum;

            $wiki = new \stdClass();
            $wiki->shortname = 'Wiki';
            $items[] = $wiki;

            $maxvalue = $participations['maxvalue'];
            $type = 'participations';
        }

        foreach ($members as $member) {
            $user = $DB->get_record('user', array('id' => $this->deanonymize($member)));
            $online = ($user->lastaccess > (time() - 5 * 60)) ? 1 : 0;
            $color = array_shift($colors);
            $itemscount = 3;

            if ($hascapability['selfassess']) {
                $useritem = [];
                if (isset($assessments['items'][$member])) {
                    $useritem['id'] = $counter;
                    $useritem['color'] = $color;
                    foreach ($assessments['items'][$member] as $key => $mitem) {
                        $useritem['item' . ($key + 1)] = $mitem;
                    }
                    array_push($useritems, $useritem);
                } else {
                    $useritem['id'] = $counter;
                    $useritem['color'] = $color;
                    for ($i = 0; $i < $itemscount; $i++) {
                        $useritem['item' . ($i + 1)] = 0;
                    }
                    array_push($useritems, $useritem);
                }
            }

            if ($hascapability['participation']) {
                $useritem = [];
                $useritem['id'] = $counter;
                $useritem['color'] = $color;

                foreach ($participations['items'][$member] as $key => $pitem) {
                    $useritem['item' . ($key + 1)] = $pitem;
                }

                array_push($useritems, $useritem);
            }

            $user_data = [
                'counter' => $counter,
                'color' => $color,
                'name' => $user->firstname . ' ' . $user->lastname,
                'mailto' => $user->email,
                'chatto' => (new \moodle_url('/message/index.php?id=' . $user->id))->out(),
                'online' => $online,
                'profile' => (new \moodle_url('/user/view.php?id=' . $user->id . '&course=' . $this->courseid))->out(),
            ];

            $all_mails .= $user->email . ';';
            array_push($data, $user_data);
            $counter++;
        }

        return [
            'showblock' => $hascapability['showblock'],
            'hasdata' => (count($useritems) > 0) ? true : false,
            'selfassess' => $hascapability['selfassess'],
            'selfassessbutton' => ($hascapability['selfassess']) ? $this->show_selfassessment() : false,
            'participation' => $hascapability['participation'],
            'usersdata' => $data,
            'items' => $items,
            'useritems' => $useritems,
            'maxvalue' => $maxvalue,
            'type' => $type,
            'mailtogroup' => $all_mails,
            'canmanage' => \has_capability('block/groupactivity:addinstance', \context_block::instance($blockinstance))
        ];
    }

    private function get_group_participations() {
        $req = [
            'session' => '0',
            'query' => 'group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
        $participation = array();
        $max_value = 0;

        foreach ($data as $group) {
            foreach ($group->group->members as $member) {
                $forum = $participation['items'][$member->member->id][] = ($member->member->other[0]->forum == null) ? 0 : $member->member->other[0]->forum;
                $wiki = $participation['items'][$member->member->id][] = ($member->member->other[1]->wiki == null) ? 0 : $member->member->other[1]->wiki;

                if (($new_val = $forum + $wiki) > $max_value) {
                    $max_value = $new_val;
                }
            }
        }

        $participation['maxvalue'] = $max_value;

        return $participation;
    }

    private function get_group_assessments() {
        $req = [
            'session' => '0',
            'query' => 'assessments:group',
            'course' => $this->courseid,
            'id' => $this->groupid
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);
        $assessments = array();
        $max_value = 0;

        foreach ($data as $item) {
            $max_value_temp = 0;

            foreach ($item->assessment->items as $key => $val) {
                $assessments['items'][$item->assessment->id->member->id][] = $val[1]->value;
                $max_value_temp += $val[1]->value;
            }
            $max_value = ($max_value_temp > $max_value) ? $max_value_temp : $max_value;
        }

        $assessments['maxvalue'] = $max_value;

        return $assessments;
    }

    private function has_capability($blockinstance) {
        global $DB;

        $block_record = $DB->get_record('block_instances', array('id' => $blockinstance), '*', MUST_EXIST);
        $block_instance = block_instance('groupactivity', $block_record);

        $data = [
            'showblock' => false,
            'participation' => false,
            'selfassess' => false
        ];

        if (!empty($block_instance->config)) {
            if (!empty($block_instance->config->roles_selfassess)) {
                foreach ($this->roles as $roleid) {
                    if (in_array($roleid, $block_instance->config->roles_selfassess)) {
                        $data['selfassess'] = true;
                        $data['showblock'] = true;
                    }
                }
            }

            if (!empty($block_instance->config->roles_mirroring)) {
                foreach ($this->roles as $roleid) {
                    if (in_array($roleid, $block_instance->config->roles_mirroring)) {
                        $data['participation'] = true;
                        $data['showblock'] = true;
                    }
                }
            }
        }

        return $data;
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

    private function show_selfassessment() {
        $req = [
            'session' => 0,
            'query' => 'display',
            'course' => $this->courseid,
            'id' => $this->anonymize($this->userid)
        ];

        $response = $this->curl_request($req);
        $data = $this->serialize($response);

        if (isset($data[1]->display->assessment)) {
            $assessment = $data[1]->display->assessment;
        }

        return (isset($assessment)) ? $assessment : 0.5;
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
}
