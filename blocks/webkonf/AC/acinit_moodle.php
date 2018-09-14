<?php
// initializes AC with moodle-Users/Courses and Enrolments (moodle-DB as source)
exit
require_once "acifwrite.php";
require_once "../config.php";

//ziel zwischen-db
//quelle moodle-db
$mo_host = "moodledb.private.oncampus.de";
$mo_db_user = "moodleuser";
$mo_db_pw = "dM-PfdMD";
//mo_db_name = moodle

if (! $q_link = mysql_connect($mo_host, $mo_db_user, $mo_db_pw))
{
	echo "acinit_moodle: Error connect qDB: ". mysql_error();
}
mysql_select_db("moodle") or die (mysql_error());

$sql = "select id, idnumber, firstname, lastname from mdl_user where idnumber != ''";
$erg = mysql_query($sql, $q_link) or die (mysql_error());
// user anlegen
while ($row = mysql_fetch_assoc($erg)) 
{
	// user festhalten
	$uname[$row["id"]] = $row["idnumber"];
	$uid[$row["idnumber"]] = $row["id"];
	// anlegen
	$row["firstname"] = addslashes($row["firstname"]);
	$row["lastname"] = addslashes($row["lastname"]);
	aiw_Create_user($row["idnumber"], $row["firstname"], $row["lastname"]);
}

$sql = "select idnumber from mdl_course where idnumber != ''";
$erg = mysql_query($sql, $q_link) or die (mysql_error());
// kurs-instance holen; Kurs anlegen
while ($row = mysql_fetch_assoc($erg))
{
	$course = get_record('course', 'idnumber', $row["idnumber"]);
	$cx = get_context_instance(CONTEXT_COURSE, $course->id);
	// anlegen
	aiw_Create_room($row["idnumber"]);
	
	foreach ($uid as $row2) 
	{
		// assignment/enrol
		$sql = "select roleid from mdl_role_assignments where userid = ".$row2." and contextid = ".$cx->id;
		$erg3 = mysql_query($sql, $q_link) or die (mysql_error());
	
		while ($row3 = mysql_fetch_assoc($erg3))
		{
			// anlegen
			aiw_Assign($uname[$row2], $row["idnumber"], $row3["roleid"]);
		}
	}
}
?>
