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
 * Web service block plugin template external functions and service definitions.
 *
 * @package    block_ikarionconsent
 * @copyright  2018 ILD, Fachhoschule Lübeck
 * @author	   Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'block_ikarionconsent_set_choice' => array(
        'classname' => 'block_ikarionconsent_external',
        'methodname' => 'set_choice',
        'classpath' => 'blocks/ikarionconsent/externallib.php',
        'description' => 'Set policy choice',
        'type' => 'write',
        'ajax' => true
    )
);
// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'ikarionconsent_set_choice' => array(
        'functions' => array('block_ikarionconsent_set_choice'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);