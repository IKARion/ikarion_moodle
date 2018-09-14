<?php
$settings->add(new admin_setting_heading(            
	'headerconfig',            
	get_string('headerconfig', 'block_webkonf'),            
	get_string('descconfig', 'block_webkonf')    ,''    
)); 
$settings->add(new admin_setting_configtext(            
	'webkonf/username',            
	get_string('configlabel_username', 'block_webkonf'),            
	get_string('configdesc_username', 'block_webkonf'),''
));
$settings->add(new admin_setting_configpasswordunmask(
	'webkonf/pass', 
	get_string('configlabel_pass', 'block_webkonf'),
	get_string('configdesc_pass', 'block_webkonf'), 
	'password'
));
$settings->add(new admin_setting_configtext(            
	'webkonf/host',            
	get_string('configlabel_host', 'block_webkonf'),            
	get_string('configdesc_host', 'block_webkonf'),''
));
$settings->add(new admin_setting_configtext(            
	'webkonf/port',            
	get_string('configlabel_port', 'block_webkonf'),            
	get_string('configdesc_port', 'block_webkonf'),''
));
$settings->add(new admin_setting_configtext(            
	'webkonf/folderid',            
	get_string('configlabel_folderid', 'block_webkonf'),            
	get_string('configdesc_folderid', 'block_webkonf'),''
));
$settings->add(new admin_setting_configtext(            
	'webkonf/helpurl',            
	get_string('configlabel_helpurl', 'block_webkonf'),            
	get_string('configdesc_helpurl', 'block_webkonf'),''
));
