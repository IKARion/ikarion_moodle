<?php
/* marketingtexbox Spottop Settings temp*/
    $temp = new admin_settingpage('theme_pioneer_marketingtextbox', get_string('marketingheadingtextbox', 'theme_pioneer'));

    // Toggle Marketing Spots.
    $name = 'theme_pioneer/togglemarketingtextbox';
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
    $name = 'theme_pioneer/marketingtexbox1info';
    $heading = get_string('marketing1', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot One
    $name = 'theme_pioneer/marketingtexbox1';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox1content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox1buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox1icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox1image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox1image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox1buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox1target';
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
    $name = 'theme_pioneer/marketingtexbox2info';
    $heading = get_string('marketing2', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Two.
    $name = 'theme_pioneer/marketingtexbox2';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox2content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox2buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox2icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox2image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox2image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox2buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox2target';
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
    $name = 'theme_pioneer/marketingtexbox3info';
    $heading = get_string('marketing3', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Three.
    $name = 'theme_pioneer/marketingtexbox3';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox3content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox3buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox3icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox3image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox3image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox3buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox3target';
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
    $name = 'theme_pioneer/marketingtexbox4info';
    $heading = get_string('marketing4', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot One
    $name = 'theme_pioneer/marketingtexbox4';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox4content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox4buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox4icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox4image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox4image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox4buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox4target';
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
    $name = 'theme_pioneer/marketingtexbox5info';
    $heading = get_string('marketing5', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Two.
    $name = 'theme_pioneer/marketingtexbox5';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox5content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox5buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox5icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox5image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox5image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox5buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox5target';
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
    $name = 'theme_pioneer/marketingtexbox6info';
    $heading = get_string('marketing6', 'theme_pioneer');
    $information = get_string('marketinginfodesc', 'theme_pioneer');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);
    
    // Marketing Spot Three.
    $name = 'theme_pioneer/marketingtexbox6';
    $title = get_string('marketingtitle', 'theme_pioneer');
    $description = get_string('marketingtitledesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox6content';
    $title = get_string('marketingcontent', 'theme_pioneer');
    $description = get_string('marketingcontentdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox6buttontext';
    $title = get_string('marketingbuttontext', 'theme_pioneer');
    $description = get_string('marketingbuttontextdesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox6icon';
    $title = get_string('marketingicon', 'theme_pioneer');
    $description = get_string('marketingicondesc', 'theme_pioneer');
    $default = 'star';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Background image setting.
    $name = 'theme_pioneer/marketingtexbox6image';
    $title = get_string('marketingimage', 'theme_pioneer');
    $description = get_string('marketingimage_desc', 'theme_pioneer');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketingtexbox6image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_pioneer/marketingtexbox6buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_pioneer');
    $description = get_string('marketingbuttonurldesc', 'theme_pioneer');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    $name = 'theme_pioneer/marketingtexbox6target';
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