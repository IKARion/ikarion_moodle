<?php
/* Marketingtop Spottop Settings temp*/
    $temp = new admin_settingpage('theme_pioneer_marketingtop', get_string('marketingheadingtop', 'theme_pioneer'));

    // Toggle Marketing Spots.
    $name = 'theme_pioneer/togglemarketingtop';
    $title = get_string('togglemarketing' , 'theme_pioneer');
    $description = get_string('togglemarketingdesc', 'theme_pioneer');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_pioneer');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_pioneer');
    $displayafterlogin = get_string('displayafterlogin', 'theme_pioneer');
    $dontdisplay = get_string('dontdisplay', 'theme_pioneer');
    $default = '0';
    $choices = array('1'=>$alwaysdisplay, '2'=>$displaybeforelogin, '3'=>$displayafterlogin, '0'=>$dontdisplay);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // This is the descriptor for Marketing Spot One
    $name = 'theme_pioneer/marketingtop1info';
    $heading = get_string('marketing1', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot One
    $name = 'theme_pioneer/marketingtop1';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop1content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop1buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop1icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing1image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing1image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop1buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop1target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // This is the descriptor for Marketing Spot Two
    $name = 'theme_pioneer/marketingtop2info';
    $heading = get_string('marketing2', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Two.
    $name = 'theme_pioneer/marketingtop2';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop2content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop2buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop2icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing2image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing2image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop2buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop2target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // This is the descriptor for Marketing Spot Three
    $name = 'theme_pioneer/marketingtop3info';
    $heading = get_string('marketing3', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Three.
    $name = 'theme_pioneer/marketingtop3';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop3content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop3buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop3icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing3image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing3image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop3buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop3target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    



    // This is the descriptor for Marketing Spot four
    $name = 'theme_pioneer/marketingtop4info';
    $heading = get_string('marketing4', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot One
    $name = 'theme_pioneer/marketingtop4';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop4content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop4buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop4icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing4image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing4image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop4buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop4target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // This is the descriptor for Marketing Spot Two
    $name = 'theme_pioneer/marketingtop5info';
    $heading = get_string('marketing5', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Two.
    $name = 'theme_pioneer/marketingtop5';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop5content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop5buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop5icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing5image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing5image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop5buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop5target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // This is the descriptor for Marketing Spot Three
    $name = 'theme_pioneer/marketingtop6info';
    $heading = get_string('marketing6', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Three.
    $name = 'theme_pioneer/marketingtop6';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop6content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop6buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop6icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/topmarketing6image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topmarketing6image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtop6buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtop6target';
    $title = get_string('marketingurltarget' , 'theme_pioneer');
    $description = get_string('marketingurltargetdesc', 'theme_pioneer');
    $target1 = get_string('marketingurltargetself', 'theme_pioneer');
    $target2 = get_string('marketingurltargetnew', 'theme_pioneer');
    $target3 = get_string('marketingurltargetparent', 'theme_pioneer');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);



    $ADMIN->add('theme_pioneer', $temp);