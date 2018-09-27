<?php

require_once('../../config.php');

global $CFG;

require_once($CFG->dirroot . '/blocks/groupactivity/locallib.php');

$data = [
    'session' => '0',
    'query' => 'facts',
    //'course' => '2',
    //'id' => groupactivity\anonymize('3')
];

$req = groupactivity\curl_request($data);

$data = explode(PHP_EOL, $req['content']);

print_object($data);

//print_object(groupactivity\anonymize('3'));

foreach ($data as $d) {
    print_object(json_decode($d));
}