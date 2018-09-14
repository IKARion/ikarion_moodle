<?php
global $SESSION, $CFG, $USER;
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/webkonf/connect_lib.php');

$uid   = optional_param('uid', '', PARAM_RAW);
$recurl   = optional_param('recurl', '', PARAM_RAW);
$recurl=urldecode($recurl);
$uid=urldecode($uid);



$global_connect_username=get_config('webkonf','username');
$connect_username  = (!empty($global_connect_username)) ? $global_connect_username : false;

$global_connect_pass=get_config('webkonf','pass');
$connect_pass  = (!empty($global_connect_pass)) ? $global_connect_pass : false;

$global_connect_host=get_config('webkonf','host');
$connect_host  = (!empty($global_connect_host)) ? $global_connect_host : false;

$global_connect_port=get_config('webkonf','port');
$connect_port  = (!empty($global_connect_port)) ? $global_connect_port : false;

$global_connect_folderid=get_config('webkonf','folderid');
$connect_folderid  = (!empty($global_connect_folderid)) ? $global_connect_folderid : false;


if ((!$connect_username)||(!$connect_pass)||(!$connect_host)||(!$connect_port)||(!$connect_folderid)) {
	error(get_string('cfg_error', 'block_webkonf'));
}


$connect = new AC_controler("connect.oncampus.de",443,$connect_folderid,$connect_username,$connect_pass,true);  
$connect->showRecording($uid, $recurl);


?>