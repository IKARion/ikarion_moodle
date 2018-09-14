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
 * @package    block_groupactivity
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
$settings->add(new admin_setting_heading(
	'headerconfig',
	get_string('headerconfig', 'block_groupactivity'),
	get_string('descconfig', 'block_groupactivity')
));

$settings->add(new admin_setting_configtext(
    'groupactivity/intervall',
    get_string('configlabel_intervall', 'block_groupactivity'),
    get_string('configdesc_intervall', 'block_groupactivity'), ''
));

$settings->add(new admin_setting_configtext(
	'groupactivity/curl_url',
	get_string('configlabel_curl_url', 'block_groupactivity'),
	get_string('configdesc_curl_url', 'block_groupactivity'), ''
));

$settings->add(new admin_setting_configtext(
    'groupactivity/curl_port',
    get_string('configlabel_curl_port', 'block_groupactivity'),
    get_string('configdesc_curl_port', 'block_groupactivity'), ''
));

$settings->add(new admin_setting_configpasswordunmask(
	'groupactivity/secrettoken',
	get_string('configlabel_secret_token', 'block_groupactivity'),
	get_string('configdesc_secret_token', 'block_groupactivity'), ''
));