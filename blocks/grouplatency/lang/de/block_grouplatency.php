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
 * @package    block_grouplatency
 * @copyright  2018 ILD, Fachhoschule Lübeck (https://www.fh-luebeck.de/ild)
 * @author     Eugen Ebel (eugen.ebel@fh-luebeck.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'IKARion Grouplatency';
$string['grouplatency'] = 'IKARion Grouplatency';
$string['grouplatency:addinstance'] = 'Einen neuen IKARion Grouplatency Block hinzufügen';

$string['headerconfig'] = 'IKARion Grouplatency Konfiguration';
$string['descconfig'] = 'Globale Konfiguration der Schnittstelle zum Expertensystem';
$string['configlabel_curl_url'] = 'CURL URL';
$string['configdesc_curl_url'] = 'Serverurl für den CURL-Aufruf';
$string['configlabel_curl_port'] = 'CURL Port';
$string['configdesc_curl_port'] = 'CURL Port';
$string['configlabel_secret_token'] = 'Secret Token';
$string['configdesc_secret_token'] = 'Secret Token zur Authentifizierung';
$string['configlabel_intervall'] = 'Poll Interval';
$string['configdesc_intervall'] = 'Polling Interval zum XPS';

$string['edit'] = 'Tutor konfigurieren';
$string['weak'] = 'Zurückhaltender Tutor';
$string['average'] = 'Normaler Tutor';
$string['hard'] = 'Strenger Tutor';
$string['tutor-text'] = 'Tutor Konfiguration';

$string['level_0'] = 'Guiding-Level-0 Wert';
$string['level_0_text'] = 'Guiding-Level-0 Text';
$string['level_1'] = 'Guiding-Level-1 Wert';
$string['level_1_text'] = 'Guiding-Level-1 Text';
$string['level_2'] = 'Guiding-Level-2 Wert';
$string['level_2_text'] = 'Guiding-Level-2 Text';

$string['mirroring'] = 'Grouping Mirroring';
$string['guiding'] = 'Grouping Guiding';
$string['mirroring_guiding'] = 'Grouping Mirroring & Guiding';
$string['nothing'] = 'Grouping Nothing to show';
