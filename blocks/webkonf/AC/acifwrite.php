<?php
// insertion of ac-commands into db-table for later execution

require_once 'acoclib.php';


function aiw_sql($sql, $parent)
{
  global $ac_db_host, $ac_db_user, $ac_db_pw, $ac_db_name;
  
  $db_link = mysql_connect($ac_db_host, $ac_db_user, $ac_db_pw)
    or acLog("acifwrite: Error connect DB: " . mysql_error());
  mysql_select_db($ac_db_name) or acLog("acifwrite: Error selection DB");
  
	if (mysql_query($sql))   // write-only; no response
	{
	   mysql_close($db_link);
	   return true;
	}
	else
	{
		acLog("Error soap ".$parent." ".mysql_error());
		acLog($sql);
		mysql_close($db_link);
		return false;
	}
}

function aiw_Create_room($roomname)
{
	$sql = "insert synccmd (cmd, cid) values ( '".CREATE_ROOM."', '$roomname' )";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_Create_user($login, $fname, $lname)
{
	$sql = "INSERT synccmd (cmd, uid, firstname, lastname) values ('".CREATE_USER."', '$login', '$fname', '$lname')";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_Assign($login, $roomname, $role)
{
	$sql = "INSERT synccmd (cmd, uid, cid, rid) values ('".CREATE_ASSI."', '$login', '$roomname', '$role')";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_Delete_room($roomname)
{
	$sql = "INSERT synccmd (cmd, cid) values ('".DELETE_ROOM."', '$roomname')";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_Delete_user($login)
{
	$sql = "INSERT synccmd (cmd, uid) values ('".DELETE_USER."', '$login')";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_Modify_user($login, $fname, $lname)
{
	$sql = "INSERT synccmd (cmd, uid, firstname, lastname) values ('".MODIFY_USER."', '$login', '$fname', '$lname')";
	return aiw_sql($sql, __FUNCTION__ );
}

function aiw_UnAssign($login, $roomname)
{
	$sql = "INSERT synccmd (cmd, uid, cid) values ('".DELETE_ASSI."', '$login', '$roomname')";
	return aiw_sql($sql, __FUNCTION__ );
}

?>