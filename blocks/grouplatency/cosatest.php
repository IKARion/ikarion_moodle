<?php

require_once('../../config.php');

global $CFG;

require_once($CFG->dirroot . '/blocks/groupactivity/locallib.php');

$data = [
    'session' => '0',
    'query' => 'posts',
    'course' => '15',
    //'id' => '96'
];

$req = groupactivity\curl_request($data);

$data = explode(PHP_EOL, $req['content']);

print_object($data);

//print_object(groupactivity\deanonymize('OK9uZ2q9+jHqZvojtoMCrCL/4gtPy7lV4jejtM0NV0A='));


foreach ($data as $d) {
    print_object(json_decode($d));
}