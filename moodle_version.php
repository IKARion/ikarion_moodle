<?php
require_once('config.php');

global $DB;

$git_head='';
$git_head_file = pathinfo(__FILE__)['dirname'] .'/.git/HEAD' ;
if (file_exists ( $git_head_file )) {
	$git_head_content = file_get_contents($git_head_file);
	$git_head_content_parts = explode('/',$git_head_content);
	$git_head = array_pop($git_head_content_parts);
}

$mysql_info = $DB->get_server_info();
$result = serialize(array(
		"version" => "".$CFG->version, 
		"release" => $CFG->release,
		"php_version" => phpversion() ,
		"mysql_version" => $mysql_info["version"],
		"hostname" => gethostname(),
		"git_head" => $git_head
));

echo $result;
?>