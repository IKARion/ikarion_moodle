#!/usr/local/bin/php
<?php

// selection of ac-commands from db-table and execution
// all table rows will be executed 

require_once 'acoclib.php';

mysql_connect($ac_db_host, $ac_db_user, $ac_db_pw)
    or acLog("acifread: Error connect DB: " . mysql_error());
if (!mysql_select_db($ac_db_name)) 
{
	acLog("acifread: Error selection DB");
	exit (W_WAIT);
}

$sql = "delete from synccmd where cid like '%oncampus-S-Hygiene%'";
$ret = mysql_query($sql);
$sql = "select count(id) from synccmd"; 
$ret = mysql_query($sql);
$anz = mysql_fetch_row($ret);
if ($anz[0] == 0)
{
	// no data, nothing to do
	exit (ALL_OK);
}





$sess = acGet_session();
if (!$sess)
{
  //kein Login - Abbruch
  acLog("Error acifread: no session from AC; waiting");
  exit (W_WAIT);
}
acLog("-----------------------------------------------------");
acLog("*** Session: $sess ");
acLog("-----------------------------------------------------");

for ($ite = 1; $ite <= $anz[0]; $ite++) 
{
  // Main loop across found rows
  
  $sql = "select min(id) from synccmd"; 
  $ret = mysql_query($sql);
  $mid = mysql_fetch_row($ret);
  if (!$mid[0])
  {
  	acLog("Error acifread no data ".mysql_error());
  	exit (ERROR);
  }
  
  $sql = "select cmd, cid, uid, rid, firstname, lastname from synccmd where id = $mid[0]";
  $ret = mysql_query($sql);
  $erg = mysql_fetch_row($ret);
  if (!$erg)
  {
  	acLog("Error acifread in data @".$mid[0].mysql_error());
  	exit (ERROR);
  }
  $erg[1] = rawurlencode($erg[1]);
  $erg[4] = rawurlencode($erg[4]);
  $erg[5] = rawurlencode($erg[5]);
  
  $retv = false;
  switch ($erg[0])    // cmd
  {
    case CREATE_ROOM :
  	$retv = acCreate_room($sess, $erg[1]);
  	break;
    case CREATE_USER :
  	$retv = acCreate_user($sess, $erg[2], $erg[4], $erg[5]);
  	break;
    case CREATE_ASSI :
  	$retv = acAssign($sess, $erg[2], $erg[1], $erg[3]);
  	break;
    case DELETE_ROOM :
  	$retv = acDelete_room($sess, $erg[1]);
  	break;
    case DELETE_USER :
  	$retv = acDelete_user($sess, $erg[2]);
  	break;
    case MODIFY_USER :
  	$retv = acModify_user($sess, $erg[2], $erg[4], $erg[5]);
  	break;
    case DELETE_ASSI :
  	$retv = acUnAssign($sess, $erg[2], $erg[1]);
  	break;
    default :
  	acLog("Error acifread undefined command cmd:".$erg[0]." @ ".$mid[0]);
  	exit (ERROR);
  }
  
  switch ($retv)
  {
    case ALL_OK:
    // delete oldest entry
    $sql = "delete from synccmd where id = $mid[0]";
    if (!mysql_query($sql))
    {
    	acLog("Error acifread ".mysql_error());
    	exit (ERROR);
    }
    break;
    case W_DUPL:
    acLog("Warning acifread (DUPL_ENTRY_DONE) in AC@".$erg[0]."@".$mid[0].", ".$erg[1]." ".$erg[2] );
    // delete oldest entry
    $sql = "delete from synccmd where id = $mid[0]";
    if (!mysql_query($sql))
    {
    	acLog("Error acifread ".mysql_error());
    	exit (ERROR);
    }
  	break;
    case W_WAIT:
    acLog("Warning  acifread (CONN_WAIT)");
    break;
    default : // error
    acLog("Error acifread in AC@".$erg[0]."@".$mid[0].", ".$erg[1]." ".$erg[2] );
    exit (ERROR);
  }
}
acLogout($sess);
exit ($retv);
?>
