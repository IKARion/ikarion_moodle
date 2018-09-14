<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme ildcustom version file.
 *
 * @package    theme_ildcustom
 * @copyright  2015 Fachhochschule L&uuml;beck, www.fh-luebeck.de
 * @autor	   Miriam Kunst
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
	
// ---------------------------------------------------------
	
	// Custom CSS file.
    $name = 'theme_ildcustom/customcss';
    $title = get_string('customcss', 'theme_ildcustom');
    $description = get_string('customcssdesc', 'theme_ildcustom');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);



	$settings->add(new admin_setting_heading('generalcolors', get_string('generalcolorsettings', 'theme_ildcustom'), get_string('generalcolorsettings_description', 'theme_ildcustom')));
	
// -----------------------------------------------------------	
	
	
	// Theme color setting.
    $name = 'theme_ildcustom/themecolor';
    $title = get_string('themecolor', 'theme_ildcustom');
    $description = get_string('themecolor_description', 'theme_ildcustom');
    $default = '#d32121';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Navbar color setting.
    $name = 'theme_ildcustom/navbarcolor';
    $title = get_string('navbarcolor', 'theme_ildcustom');
    $description = get_string('navbarcolor_description', 'theme_ildcustom');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Block background color setting.
    $name = 'theme_ildcustom/blockcolor';
    $title = get_string('blockcolor', 'theme_ildcustom');
    $description = get_string('blockcolor_description', 'theme_ildcustom');
    $default = '#f0f1f3';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Footer background color (HTML-Footer).
    $name = 'theme_ildcustom/linkcolor';
    $title = get_string('linkcolor', 'theme_ildcustom');
    $description = get_string('linkcolor_description', 'theme_ildcustom');
    $default = '#275e8f';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Content text color setting.
    $name = 'theme_ildcustom/textcolor';
    $title = get_string('textcolor', 'theme_ildcustom');
    $description = get_string('textcolor_description', 'theme_ildcustom');
    $default = '#333333';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Navbar text color setting.
    $name = 'theme_ildcustom/navtextcolor';
    $title = get_string('navtextcolor', 'theme_ildcustom');
    $description = get_string('navtextcolor_description', 'theme_ildcustom');
    $default = '#333333';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Page-footer background color (HTML-Footer).
    $name = 'theme_ildcustom/footercolor';
    $title = get_string('footercolor', 'theme_ildcustom');
    $description = get_string('footercolor_description', 'theme_ildcustom');
    $default = '#eeeeee';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	
	// -----------------------------------------------
	
	// Settings navbar: Logo und Text
	$settings->add(new admin_setting_heading('navbarbrand', get_string('navbarbrandsettings', 'theme_ildcustom'), get_string('navbarbrandsettings_description', 'theme_ildcustom')));
	
    // Logo file setting.
    $name = 'theme_ildcustom/logo';
    $title = get_string('logo', 'theme_ildcustom');
    $description = get_string('logodesc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	// Navbar header brand note setting.
    $settings->add(new admin_setting_confightmleditor('theme_ildcustom_brand_html', 
													  get_string('brand_html', 'theme_ildcustom'),
													  get_string('brand_html_description', 'theme_ildcustom'), 
													  ''));	
    

	$name = 'theme_ildcustom/brandwidth';
    $title = get_string('brandwidth', 'theme_ildcustom');
    $description = get_string('brandwidth_description', 'theme_ildcustom');
    $default = '138';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	$name = 'theme_ildcustom/navheight';
    $title = get_string('navheight', 'theme_ildcustom');
    $description = get_string('navheight_description', 'theme_ildcustom');
    $default = '52';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	$name = 'theme_ildcustom/navtopborder';
    $title = get_string('navtopborder', 'theme_ildcustom');
    $description = get_string('navtopborder_description', 'theme_ildcustom');
    $default = '0';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	$name = 'theme_ildcustom/navbottomborder';
    $title = get_string('navbottomborder', 'theme_ildcustom');
    $description = get_string('navbottomborder_description', 'theme_ildcustom');
    $default = '0';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	// Navbar border color.
    $name = 'theme_ildcustom/navbordercolor';
    $title = get_string('navbordercolor', 'theme_ildcustom');
    $description = get_string('navbordercolor_description', 'theme_ildcustom');
    $default = '#fff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	

	//$settings->add(new admin_setting_heading('pagemodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));
	$settings->add(new admin_setting_heading('pagemodeditgraphicbanner', get_string('graphicbanner', 'theme_ildcustom'), get_string('graphicbanner_description', 'theme_ildcustom')));
	
	$settings->add(new admin_setting_configtext('theme_ildcustom_graphicbanner_text',
												get_string('graphicbanner_text', 'theme_ildcustom'),
												get_string('graphicbanner_text_description', 'theme_ildcustom'), 
												'Moodle', 
												PARAM_TEXT));
	
	/* Grafikbanner Hintergrundebene */
	// Bannerhoehe
	$name = 'theme_ildcustom/bannerheight';
    $title = get_string('bannerheight', 'theme_ildcustom');
    $description = get_string('bannerheight_description', 'theme_ildcustom');
    $default = '170';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	// Banner background setting.
    $name = 'theme_ildcustom/bannerbgcolor';
    $title = get_string('bannerbgcolor', 'theme_ildcustom');
    $description = get_string('bannerbgcolor_desc', 'theme_ildcustom');
    $default = '#dedede';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	    
	// Background image setting.
    $name = 'theme_ildcustom/bannerbgimage';
    $title = get_string('bannerbgimage', 'theme_ildcustom');
    $description = get_string('bannerbgimage_desc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'bannerbgimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	
	// Background size setting.
    $name = 'theme_ildcustom/bannerbgsize';
    $title = get_string('bannerbgsize', 'theme_ildcustom');
    $description = get_string('bannerbgsize_desc', 'theme_ildcustom');;
    $default = 'cover';
    $choices = array(
        '0' => get_string('default'),
        'cover' => get_string('bannerbgsizecover', 'theme_ildcustom'),
        'contain' => get_string('bannerbgsizecontain', 'theme_ildcustom'),
        'auto' => get_string('bannerbgsizeauto', 'theme_ildcustom'),
		'auto 100%' => get_string('bannerbgsizeheight', 'theme_ildcustom'),
        '100% auto' => get_string('bannerbgsizewidth', 'theme_ildcustom')
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
		
    // Background repeat setting.
    $name = 'theme_ildcustom/bannerbgrepeat';
    $title = get_string('bannerbgrepeat', 'theme_ildcustom');
    $description = get_string('bannerbgrepeat_desc', 'theme_ildcustom');;
    $default = 'no-repeat';
    $choices = array(
        '0' => get_string('default'),
        'repeat' => get_string('bannerbgrepeatrepeat', 'theme_ildcustom'),
        'repeat-x' => get_string('bannerbgrepeatrepeatx', 'theme_ildcustom'),
        'repeat-y' => get_string('bannerbgrepeatrepeaty', 'theme_ildcustom'),
        'no-repeat' => get_string('bannerbgrepeatnorepeat', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background position setting.
    $name = 'theme_ildcustom/bannerbgposition';
    $title = get_string('bannerbgposition', 'theme_ildcustom');
    $description = get_string('bannerbgposition_desc', 'theme_ildcustom');
    $default = '0';
    $choices = array(
        '0' => get_string('default'),
        'left_top' => get_string('bannerbgpositionlefttop', 'theme_ildcustom'),
        'left_center' => get_string('bannerbgpositionleftcenter', 'theme_ildcustom'),
        'left_bottom' => get_string('bannerbgpositionleftbottom', 'theme_ildcustom'),
        'right_top' => get_string('bannerbgpositionrighttop', 'theme_ildcustom'),
        'right_center' => get_string('bannerbgpositionrightcenter', 'theme_ildcustom'),
        'right_bottom' => get_string('bannerbgpositionrightbottom', 'theme_ildcustom'),
        'center_top' => get_string('bannerbgpositioncentertop', 'theme_ildcustom'),
        'center_center' => get_string('bannerbgpositioncentercenter', 'theme_ildcustom'),
        'center_bottom' => get_string('bannerbgpositioncenterbottom', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	// Banner second layer
	// Banner second layer image setting.
    $name = 'theme_ildcustom/bannerlayerimage';
    $title = get_string('bannerlayerimage', 'theme_ildcustom');
    $description = get_string('bannerlayerimage_desc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'bannerlayerimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	// Background size setting.
    $name = 'theme_ildcustom/bannerlayersize';
    $title = get_string('bannerlayersize', 'theme_ildcustom');
    $description = get_string('bannerlayersize_desc', 'theme_ildcustom');;
    $default = 'contain';
    $choices = array(
        '0' => get_string('default'),
        'cover' => get_string('bannerlayersizecover', 'theme_ildcustom'),
        'contain' => get_string('bannerlayersizecontain', 'theme_ildcustom'),
        'auto' => get_string('bannerlayersizeauto', 'theme_ildcustom'),
		'auto 100%' => get_string('bannerlayersizeheight', 'theme_ildcustom'),
        '100% auto' => get_string('bannerlayersizewidth', 'theme_ildcustom')
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
    // Banner second layer repeat setting.
    $name = 'theme_ildcustom/bannerlayerrepeat';
    $title = get_string('bannerlayerrepeat', 'theme_ildcustom');
    $description = get_string('bannerlayerrepeat_desc', 'theme_ildcustom');;
    $default = 'no-repeat';
    $choices = array(
        '0' => get_string('default'),
        'repeat' => get_string('bannerlayerrepeatrepeat', 'theme_ildcustom'),
        'repeat-x' => get_string('bannerlayerrepeatrepeatx', 'theme_ildcustom'),
        'repeat-y' => get_string('bannerlayerrepeatrepeaty', 'theme_ildcustom'),
        'no-repeat' => get_string('bannerlayerrepeatnorepeat', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Banner second layer position setting.
    $name = 'theme_ildcustom/bannerlayerposition';
    $title = get_string('bannerlayerposition', 'theme_ildcustom');
    $description = get_string('bannerlayerposition_desc', 'theme_ildcustom');
    $default = '0';
    $choices = array(
        '0' => get_string('default'),
        'left_top' => get_string('bannerlayerpositionlefttop', 'theme_ildcustom'),
        'left_center' => get_string('bannerlayerpositionleftcenter', 'theme_ildcustom'),
        'left_bottom' => get_string('bannerlayerpositionleftbottom', 'theme_ildcustom'),
        'right_top' => get_string('bannerlayerpositionrighttop', 'theme_ildcustom'),
        'right_center' => get_string('bannerlayerpositionrightcenter', 'theme_ildcustom'),
        'right_bottom' => get_string('bannerlayerpositionrightbottom', 'theme_ildcustom'),
        'center_top' => get_string('bannerlayerpositioncentertop', 'theme_ildcustom'),
        'center_center' => get_string('bannerlayerpositioncentercenter', 'theme_ildcustom'),
        'center_bottom' => get_string('bannerlayerpositioncenterbottom', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	/*
    // Background fixed setting.
    $name = 'theme_ildcustom/bannerlayerfixed';
    $title = get_string('bannerlayerfixed', 'theme_ildcustom');
    $description = get_string('bannerlayerfixed_desc', 'theme_ildcustom');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	*/
	
// --------------------bis hier hin laeuft es ---------------------------
	/*
	// Grafikbanner Vordergrund innen
	// Banner content background setting.
    $name = 'theme_ildcustom/bannercontentcolor';
    $title = get_string('bannercontentcolor', 'theme_ildcustom');
    $description = get_string('bannercontentcolor_desc', 'theme_ildcustom');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	*/

    // Banner Content image setting.
    $name = 'theme_ildcustom/bannercontentimage';
    $title = get_string('bannercontentimage', 'theme_ildcustom');
    $description = get_string('bannercontentimage_desc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'bannercontentimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	/*
	
	// Banner Content size setting.
    $name = 'theme_ildcustom/bannercontentsize';
    $title = get_string('bannercontentsize', 'theme_ildcustom');
    $description = get_string('bannercontentsize_desc', 'theme_ildcustom');;
    $default = 'auto';
    $choices = array(
        '0' => get_string('default'),
        'cover' => get_string('bannercontentsizecover', 'theme_ildcustom'),
        'contain' => get_string('bannercontentsizecontain', 'theme_ildcustom'),
        'auto' => get_string('bannercontentsizeauto', 'theme_ildcustom'),
		'auto 100%' => get_string('bannercontentsizeheight', 'theme_ildcustom'),
        '100% auto' => get_string('bannercontentsizewidth', 'theme_ildcustom')
    );
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	*/
	
	$name = 'theme_ildcustom/bannercontentfix';
    $title = get_string('bannercontentfix', 'theme_ildcustom');
    $description = get_string('bannercontentfix_description', 'theme_ildcustom');
    $default = 'auto';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	
    // Background repeat setting.
    $name = 'theme_ildcustom/bannercontentrepeat';
    $title = get_string('bannercontentrepeat', 'theme_ildcustom');
    $description = get_string('bannercontentrepeat_desc', 'theme_ildcustom');;
    $default = 'no-repeat';
    $choices = array(
        '0' => get_string('default'),
        'repeat' => get_string('bannercontentrepeatrepeat', 'theme_ildcustom'),
        'repeat-x' => get_string('bannercontentrepeatrepeatx', 'theme_ildcustom'),
        'repeat-y' => get_string('bannercontentrepeatrepeaty', 'theme_ildcustom'),
        'no-repeat' => get_string('bannercontentrepeatnorepeat', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

		
    // Background position setting.
    $name = 'theme_ildcustom/bannercontentposition';
    $title = get_string('bannercontentposition', 'theme_ildcustom');
    $description = get_string('bannercontentposition_desc', 'theme_ildcustom');
    $default = '0';
    $choices = array(
        '0' => get_string('default'),
        'left_top' => get_string('bannercontentpositionlefttop', 'theme_ildcustom'),
        'left_center' => get_string('bannercontentpositionleftcenter', 'theme_ildcustom'),
        'left_bottom' => get_string('bannercontentpositionleftbottom', 'theme_ildcustom'),
        'right_top' => get_string('bannercontentpositionrighttop', 'theme_ildcustom'),
        'right_center' => get_string('bannercontentpositionrightcenter', 'theme_ildcustom'),
        'right_bottom' => get_string('bannercontentpositionrightbottom', 'theme_ildcustom'),
        'center_top' => get_string('bannercontentpositioncentertop', 'theme_ildcustom'),
        'center_center' => get_string('bannercontentpositioncentercenter', 'theme_ildcustom'),
        'center_bottom' => get_string('bannercontentpositioncenterbottom', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Loginbereich 
	$settings->add(new admin_setting_heading('pagemodeditloginsite', get_string('loginsite', 'theme_ildcustom'), get_string('loginsite_description', 'theme_ildcustom')));
	
	// loginsite / loginblock backgroundcolor setting.
    $name = 'theme_ildcustom/loginbgcolor';
    $title = get_string('loginbgcolor', 'theme_ildcustom');
    $description = get_string('loginbgcolor_desc', 'theme_ildcustom');
    $default = '#dedede';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	    
	// Login Background image setting.
    $name = 'theme_ildcustom/loginbgimage';
    $title = get_string('loginbgimage', 'theme_ildcustom');
    $description = get_string('loginbgimage_desc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbgimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

	
	// Login Background size setting.
    $name = 'theme_ildcustom/loginbgsize';
    $title = get_string('loginbgsize', 'theme_ildcustom');
    $description = get_string('loginbgsize_desc', 'theme_ildcustom');;
    $default = 'cover';
    $choices = array(
        '0' => get_string('default'),
        'cover' => get_string('loginbgsizecover', 'theme_ildcustom'),
        'contain' => get_string('loginbgsizecontain', 'theme_ildcustom'),
        'auto' => get_string('loginbgsizeauto', 'theme_ildcustom'),
		'auto 100%' => get_string('loginbgsizeheight', 'theme_ildcustom'),
        '100% auto' => get_string('loginbgsizewidth', 'theme_ildcustom')
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
		
    // Login Background repeat setting.
    $name = 'theme_ildcustom/loginbgrepeat';
    $title = get_string('loginbgrepeat', 'theme_ildcustom');
    $description = get_string('loginbgrepeat_desc', 'theme_ildcustom');;
    $default = 'no-repeat';
    $choices = array(
        '0' => get_string('default'),
        'repeat' => get_string('loginbgrepeatrepeat', 'theme_ildcustom'),
        'repeat-x' => get_string('loginbgrepeatrepeatx', 'theme_ildcustom'),
        'repeat-y' => get_string('loginbgrepeatrepeaty', 'theme_ildcustom'),
        'no-repeat' => get_string('loginbgrepeatnorepeat', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background position setting.
    $name = 'theme_ildcustom/loginbgposition';
    $title = get_string('loginbgposition', 'theme_ildcustom');
    $description = get_string('loginbgposition_desc', 'theme_ildcustom');
    $default = '0';
    $choices = array(
        '0' => get_string('default'),
        'left_top' => get_string('loginbgpositionlefttop', 'theme_ildcustom'),
        'left_center' => get_string('loginbgpositionleftcenter', 'theme_ildcustom'),
        'left_bottom' => get_string('loginbgpositionleftbottom', 'theme_ildcustom'),
        'right_top' => get_string('loginbgpositionrighttop', 'theme_ildcustom'),
        'right_center' => get_string('loginbgpositionrightcenter', 'theme_ildcustom'),
        'right_bottom' => get_string('loginbgpositionrightbottom', 'theme_ildcustom'),
        'center_top' => get_string('loginbgpositioncentertop', 'theme_ildcustom'),
        'center_center' => get_string('loginbgpositioncentercenter', 'theme_ildcustom'),
        'center_bottom' => get_string('loginbgpositioncenterbottom', 'theme_ildcustom'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	// Loginblock auf der Startseite.
    $name = 'theme_ildcustom/loginblockheader';
    $title = get_string('loginblockheader', 'theme_ildcustom');
    $description = get_string('loginblockheader_desc', 'theme_ildcustom');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	// Favicon-Anweisungen
	$settings->add(new admin_setting_heading('customfavicon', get_string('customfavsettings', 'theme_ildcustom'), get_string('customfavsettings_description', 'theme_ildcustom')));

	// Favicon
	// ICO-Grafik als neues favicon.
    $name = 'theme_ildcustom/favicon';
    $title = get_string('favicon', 'theme_ildcustom');
    $description = get_string('favicon_desc', 'theme_ildcustom');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
	
	
	// LESS-Anweisungen
	$settings->add(new admin_setting_heading('lessextra', get_string('lessextrasettings', 'theme_ildcustom'), get_string('lessextrasettings_description', 'theme_ildcustom')));

	// Custom LESS file.
    $name = 'theme_ildcustom/customless';
    $title = get_string('customless', 'theme_ildcustom');
    $description = get_string('customlessdesc', 'theme_ildcustom');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);



	
	// Footerbereich
	$settings->add(new admin_setting_heading('pagemodeditfootnote', get_string('footnote', 'theme_ildcustom'), get_string('footnote_description', 'theme_ildcustom')));
	
	// Footnote setting.
    $settings->add(new admin_setting_confightmleditor('theme_ildcustom_footnote_html', 
													  get_string('footnote_html', 'theme_ildcustom'),
													  get_string('footnote_html_description', 'theme_ildcustom'), 
													  ''));	
	
	$settings->add(new admin_setting_heading('pagemodeditsocial_media_urls', get_string('social_media_urls', 'theme_ildcustom'), get_string('social_media_urls_description', 'theme_ildcustom')));
	
	// Social Media
	$settings->add(new admin_setting_configtext('theme_ildcustom_facebook_url',
												get_string('facebook_url', 'theme_ildcustom'),
												get_string('facebook_url_description', 'theme_ildcustom'), 
												'https://www.facebook.com/oncampusgmbh', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_googleplus_url',
												get_string('googleplus_url', 'theme_ildcustom'),
												get_string('googleplus_url_description', 'theme_ildcustom'), 
												'https://plus.google.com/', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_twitter_url',
												get_string('twitter_url', 'theme_ildcustom'),
												get_string('twitter_url_description', 'theme_ildcustom'), 
												'https://twitter.com/oncampusfhl', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_youtube_url',
												get_string('youtube_url', 'theme_ildcustom'),
												get_string('youtube_url_description', 'theme_ildcustom'), 
												'https://www.youtube.com/user/oncampusFHL', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_instagramm_url',
												get_string('instagramm_url', 'theme_ildcustom'),
												get_string('instagramm_url_description', 'theme_ildcustom'), 
												'https://www.instagram.com/', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_linkedin_url',
												get_string('linkedin_url', 'theme_ildcustom'),
												get_string('linkedin_url_description', 'theme_ildcustom'), 
												'https://de.linkedin.com/', 
												PARAM_TEXT));
												
	$settings->add(new admin_setting_configtext('theme_ildcustom_xing_url',
												get_string('xing_url', 'theme_ildcustom'),
												get_string('xing_url_description', 'theme_ildcustom'), 
												'https://www.xing.com/', 
												PARAM_TEXT));
}
