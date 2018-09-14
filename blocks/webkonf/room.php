<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/webkonf/connect_lib.php');
require_once($CFG->dirroot.'/course/lib.php');


global $SESSION, $CFG, $USER, $DB;
if ($USER->idnumber != $_GET["uid"]) {
	#die("Authentification error!");
}



$uid    = optional_param('uid', '', PARAM_RAW);
$room   = optional_param('room', '', PARAM_RAW);
$grouped   = optional_param('grouped', '', PARAM_RAW);
$cid = optional_param('c', '', PARAM_RAW);
$discussion   = optional_param('discussion', '', PARAM_BOOL);
$uid=urldecode($uid);
$room=urldecode($room);
$groupedMode = false;
if ($grouped == 'true') {

	$originalMeeting = $room;
	$p = explode("-GRP-",$room);
	$room = $p[0];
	$checkAgain = $p[1];

	$groupedMode = true;

	require_once("../../group/lib.php");

	$course = $DB->get_record('course', array('id'=>$cid));
	$groups = groups_get_all_groups($course->id);
	$context = context_course::instance($course->id, MUST_EXIST);
	$canEnter = false;
	if (is_array($groups)) {



		$isTeacher = false;
		if (has_capability('moodle/course:manageactivities', $context)) {
			$isTeacher = true;
		}

		# moodle/course:manageactivities
		#  has_capability('moodle/role:switchroles', $context)
		$debugMe = true;
		if ($_COOKIE["debugger"] == "illi") {
			$debugMe= true;
		}

		foreach ($groups as $g) {
			$members = groups_get_members_by_role($g->id, $course->id);
			# if ($debugMe == true) {echo "<pre>".(var_dump($members))."</pre>";}

			$userList = array_shift($members);
			$userList = $userList->users;

			if ($debugMe == true) { echo "<pre><tt>".print_r($members)."</tt></pre>";}
			foreach ($userList as $m) {
				if ($debugMe == true) {
					echo $m->username." in ".$g->name." checkagainst = $checkAgain <br>";
				}

				# func groupName2meetingName()

				if (
					($USER->username == $m->username && $checkAgain == groupName2meetingName($g->name))
					|| $isTeacher == true
				) {
					$canEnter = true;
					if ($debugMe == true) {
						echo "*** CAN ENTER*** <br>";
					}
					# FHL-MIM-12-W15-000502-GRP-Luebeck-Hamburg
				} else {
					if ($debugMe == true) {
						echo "<br>";
					}
				}
			}
		}

		if ($canEnter == false && $debugMe == false) { error("Groupmeeting not allowed!"); }
	}
}

if ($groupedMode) {
	$role = 2;
}


if (empty($uid) || empty($room)) { error("Must specify uid and room");}

if (! ($course = $DB->get_record('course', array('idnumber'=>$room))) ) { error('Invalid course idnumber'); }
$courseid=$course->id;

$uid_norm = $uid;
$uid = str_replace("ikarion_","",$uid);

if (! ($getuser = $DB->get_record('user', array('username'=> $uid))) ) { error('Invalid user'); }

if ($USER->username!=$uid) { error('Invalid user');}

if($discussion==true) { $room=$room.'-TR';}

//preload_course_contexts($course->id);
if (! $context = context_course::instance($course->id)) {
	error('nocontext');
}

$userid="ikarion_".$USER->id;

$role='1';

if (has_capability('moodle/course:manageactivities', $context)) {
	$role='2';
} else {
	if($discussion==true) {
		$role='2';
	} else {
		$role='1';
	}
}
if ($canEnter == true ) {
	$role = "2";
	if ($debugMe == true) { die("role 2"); }
}

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

if ($groupedMode == true) {
	$room = $originalMeeting;
}

#$connect = new AC_controler($connect_host,$connect_port,$connect_folderid,$connect_username,$connect_pass,true);
$connect = new AC_controler("connect.oncampus.de",443,$connect_folderid,$connect_username,$connect_pass,true);



if ($debugMe == true) {
	die($room);
}

$connect_room_id=$connect->lookupRoom($room);
if (!$connect_room_id) {
	$connect->createRoom ($room);
}


$connect_user_id=$connect->lookupUser($USER->username);
if (!$connect_user_id) {
	$connect->createUser($USER->username, $USER->firstname, $USER->lastname);
}
if ($discussion == true) {
	$connect->unassignUser ($USER->username, $room);
}

	$connect->assignUser ($USER->username, $room, $role);
  
if (!$connect->checkAssignment($USER->username, $room)) {
	$connect->assignUser ($USER->username, $room, $role);
}

if ($debugMe == true) {
	die($room);
}

$connect->loginAs($USER->username,$room);




function devLog($arg) {

	$fname = "/opt/www/moodle/blocks/webkonf/_devLog/log_".date("Y-m-d",date("U")).".txt";
	if (!file_exists($fname)) {
		$h = fopen ($fname, "a+");
		fwrite($h,"");
		fclose($h);
		#chmod($fname,0664);
	}
	$h = fopen ($fname, "a+");
	fwrite ( $h, date("H:i:s",date("U"))." ".utf8_decode($arg)."\r\n");
	fclose($h);

}


function groupName2meetingName($expr) {

	$expr = trim($expr);
	$expr = str_replace(" ","-",$expr);
	#$expr = str_replace(".","-",$expr);
	$expr = str_replace(":","-",$expr);
	$expr = str_replace(";","-",$expr);
	$expr = str_replace("/","-",$expr);
	$expr = str_replace("&uuml;","ü",$expr);

	$expr = str_replace("ä","ae-",$expr);
	$expr = str_replace("ö","oe",$expr);
	$expr = str_replace("ü","ue",$expr);
	$expr = str_replace("Ä","AE",$expr);
	$expr = str_replace("Ü","UE",$expr);
	$expr = str_replace("Ö","OE",$expr);
	$expr = str_replace("ß","SZ",$expr);
	return $expr;
}



?>