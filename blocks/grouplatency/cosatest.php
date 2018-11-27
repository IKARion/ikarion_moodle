<?php

require_once('../../config.php');

global $CFG;

require_once($CFG->dirroot . '/blocks/groupactivity/locallib.php');

/*
$req = [
    'session' => '0',
    'query' => 'facts',
    'course' => '2',
    //'id' => groupactivity\anonymize('3')
];
*/

$req = [
    'session' => '0',
    'query' => 'contents_group',
    'course' => 11,
    //'id' => groupactivity\anonymize('304')
    'id' => 237
];

$response = groupactivity\curl_request($req);
$data = groupactivity\serialize($response);

print_object($data);

