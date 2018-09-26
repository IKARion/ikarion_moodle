<?php

require_once('../../config.php');

global $CFG;

require_once($CFG->dirroot . '/blocks/grouplatency/locallib.php');

$data = [
    'session' => '0',
    'query' => 'participation',
    //'course' => '2',
    'id' => '18'
];

$req = grouplatency\curl_request($data);

$data = explode(PHP_EOL, $req['content']);

foreach ($data as $d) {
    print_object(json_decode($d));
}

//print_object(json_decode($req['content'], true));