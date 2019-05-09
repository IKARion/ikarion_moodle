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

$functions = array(
    'block_groupactivity_get_data' => array(
        'classname' => 'block_groupactivity_external',
        'methodname' => 'get_data',
        'classpath' => 'blocks/groupactivity/externallib.php',
        'description' => 'Get groupactivity data',
        'type' => 'read',
        'ajax' => true
    ),
    'block_groupactivity_set_self_assess' => array(
        'classname' => 'block_groupactivity_external',
        'methodname' => 'set_self_assess',
        'classpath' => 'blocks/groupactivity/externallib.php',
        'description' => 'Set self assess data',
        'type' => 'write',
        'ajax' => true
    ),
    'block_groupactivity_get_selfassess_items' => array(
        'classname' => 'block_groupactivity_external',
        'methodname' => 'get_selfassess_items',
        'classpath' => 'blocks/groupactivity/externallib.php',
        'description' => 'Get selfassess items',
        'type' => 'read',
        'ajax' => true
    )
);
// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'groupactivity_get_data' => array(
        'functions' => array('block_groupactivity_get_data'),
        'restrictedusers' => 0,
        'enabled' => 1,
    ),
    'groupactivity_set_self_assess' => array(
        'functions' => array('block_groupactivity_set_self_assess'),
        'restrictedusers' => 0,
        'enabled' => 1,
    ),
    'groupactivity_get_selfassess_items' => array(
        'functions' => array('block_groupactivity_get_selfassess_items'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);