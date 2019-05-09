<?php

require_once('../../config.php');
global $CFG;

require_login();

if (is_siteadmin()) {
    $req = [
        'session' => '0',
        'query' => 'groups_member',
        'course' => 20,
        //'id' => 398
        'id' => test_anonymize(391)
    ];
    
    $response = test_curl_request($req);
    $data = test_serialize($response);

    print_object($response);
}

function test_curl_request($data) {
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

function test_serialize($response) {
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

function test_anonymize($data) {
    global $CFG;

    return openssl_encrypt($data, $CFG->encryptmethod, $CFG->encryptkey);
}
