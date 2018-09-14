<?php
// AC-lib oncampus

//function acGet_session()
//function acCreate_room($roomname)
//function acCreate_user($login, $fname, $lname)
//function acDelete_room($roomname)
//function acDelete_user($login)
//function acUnAssign($login, $roomname)
//function acAssign($login, $roomname, $role)
//function acModify_user($login, $fname, $lname)


require_once 'ac_conf.inc';

function acLog($info)
{
  global $ac_logfile;
  // logging aktivieren
  $aclog = fopen($ac_logfile, "a");

  // Logausgabe im Format YYYYMMDD-hhmmss <Meldung>
  fwrite($aclog, date("Ymd-His ").$info."\n");

  fclose($aclog);
}


function acGet_session() 
{
  global $ac_host, $ac_port, $ac_admin, $ac_xuserid;

  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return false; //Basisfunktion; 0-Wert-Auswertung
  }
  
  $url = '/api/xml?action=login&external-auth=use';
  fputs($fp, "GET ".$url." HTTP/1.0\r\n$ac_xuserid:$ac_admin\r\nHost: $ac_host\r\n\r\n");
  // fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->ac_xuserid.":".$this->ac_admin."\r\nx-user-id:".$this->ac_xuserid."\r\n\Host: $ac_host\r\n\r\n");

  $sessionid = false;
  $retv = false;
  // erste Zeile des Headers loggen
  $logging = "login:".fgets($fp);
  while($line = fgets($fp)) 
  {
		// breeze-session-cookie rausholen
		if (strstr($line, "code=\"ok\""))
		{
			$retv = true;
			break;
		}
		else
		{
      		$logging .= $line;
    	}
		if (preg_match('/BREEZESESSION=(.*)\;/U', $line, $result))
		{
			$sessionid = $result[1];
		}
  }
  fclose($fp);
  // log bei Bedarf schreiben
  if ( !$retv )
  {
		acLog( $logging );
		return false;
  }

  return $sessionid;
}

function acLogout($sid)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return false;
  }

  $url = "/api/xml?action=logout&session=$sid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");
  
  fclose($fp);
}


function acLookup_roomURL($sessionid, $oid)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return false;
  }

  $url = "/api/xml?action=sco-info&sco-id=$oid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $url = false;
  // erste Zeile des Headers loggen
  $logging = "sco-info:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// url rausholen
		if (preg_match('@\<url-path\>\/([^/]+)@', $line, $result))
		{
			$url = $result[1];
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);

  if ( !$url )
  {
		acLog( $logging );
  }

  return ($url);
}


function acLookup_room($sessionid, $roomid)
{
  global $ac_host, $ac_port, $ac_meeting_folder_id;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return false;
  }

  $url = "/api/xml?action=sco-expanded-contents&sco-id=$ac_meeting_folder_id&filter-type=meeting&filter-name=$roomid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $scoid = false;
  $retv = false;
  // erste Zeile des Headers loggen
  $logging = "sco-expanded-contents:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// sco-id rausholen
		if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result))
		{
			$scoid = $result[1];
		}
		if (strstr($line, "code=\"ok\""))
		{
			$retv = true;
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);

  if ( !$retv )
  {
		acLog( $logging );
  }

  return ($scoid);
}


function acLookup_user($sessionid, $loginid)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return false;
  }

  $url = "/api/xml?action=principal-list&filter-login=$loginid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $scoid = false;
  $retv = false;
  // erste Zeile des Headers loggen
  $logging = "principal-list:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// sco-id rausholen
		if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result))
		{
			$scoid = $result[1];
		}
		if (strstr($line, "code=\"ok\""))
		{
			$retv = true;
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);

  if ( !$retv )
  {
		acLog( $logging );
  }

  return ($scoid);
}


function LookupOK($socket, $source)
{
  // erste Zeile des Headers loggen
  $logging = $source.":".fgets($socket, 150);
  $retv = ERROR;
  while($line = fgets($socket))
  {
		// Ergebnis rausholen
		if (strstr($line, "code=\"ok\""))
		{
			$retv = ALL_OK;
		}
		else
		{
			$logging .= $line;
		}
  }
  if ( $retv )
  {
		acLog( $logging );
  }
  return ($retv);
}


function acAss($sessionid, $roomid, $princid, $role)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (ERROR);
  }

  $url = "/api/xml?action=permissions-update&permission-id=$role&acl-id=$roomid&principal-id=$princid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");
  
  $retv = LookupOK($fp, __FUNCTION__);
  fclose($fp);
  return ($retv);
}


function acAssign($sid, $login, $roomname, $role)
{
	$training=false;
  if (!$rid = acLookup_room($sid, $roomname))
  {
  	if (substr($roomname, -3, 3)=='-TR')
		{
		$training=true;
		}
	else
		{
	  	acLog ( "error Room not found ".$roomname);
  		return (ERROR);
		}
  }
  if ($training==false)
  	{
	  if (!$pid = acLookup_user($sid, $login))
	  {
  		acLog ( "error User not found ".$roomname);
	  	return (ERROR);
	  }
  
	  switch ( $role )
	  {
	    case '5':
    		$ac_role = 'view';
    		break;
	    case '4':
    		$ac_role = 'mini-host';
    		break;
	    case '3':
    		$ac_role = 'host';
    		break;
	    default:
			$ac_role = 'remove';
	  }
  return acAss($sid, $rid, $pid, $ac_role);
  }
}


function acUnAssign($sid, $login, $roomname)
{
$training=false;
  if (!$rid = acLookup_room($sid, $roomname))
  {
	if (substr($roomname, -3, 3)!='-TR')
		{
   		acLog ( "error Room not found ".$roomname);
	  	return (ERROR);
		}
	else
		{
		$training=true;
		}
  }
  if (!$pid = acLookup_user($sid, $login))
  {
  	acLog ( "error User not found ".$roomname);
  	return (ERROR);
  }
  
	if ($training==true)
  		{return true;}
	else
		{
	  return acAss($sid, $rid, $pid, 'remove');
	  }
}



function acDuser($sessionid, $princid)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (W_WAIT);
  }

  $url = "/api/xml?action=principals-delete&principal-id=$princid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $retv = LookupOK($fp, __FUNCTION__);
  fclose($fp);
  return ($retv);
}

function acDelete_user($sid, $login)
{
	$oid = acLookup_user($sid, $login);
	if ($oid) 
	{ 
		acDuser($sid, $oid); 
	}
	else
	{
  		acLog( "error ". __FUNCTION__.$login);
  		return (ERROR);
	}
}


function acMuser($sessionid, $princid, $loginid, $fname, $lname)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (W_WAIT);
  }

  $url = "/api/xml?action=principal-update&principal-id=$princid&login=$loginid&has-children=0&first-name=$fname&last-name=$lname&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $retv = LookupOK($fp, __FUNCTION__);
  fclose($fp);
  return ($retv);
}

function acModify_user($sid, $login, $fname, $lname)
{
	$oid = acLookup_user($sid, $login);
	if ($oid) 
	{ 
		acMuser($sid, $oid, $login, $fname, $lname); 
	}
	else
	{
  		acLog( "error ". __FUNCTION__.$login);
  		return (ERROR);
	}
}


function acDroom($sessionid, $scoid)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (W_WAIT);
  }

  $url = "/api/xml?action=sco-delete&sco-id=$scoid&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $retv = LookupOK($fp, __FUNCTION__);
  fclose($fp);
  return ($retv);
}

function acDelete_room($sid, $roomname)
{
  $oid = acLookup_room($sid, $roomname);
  if ($oid) 
  { 
		return acDroom($sid, $oid); 
  }
  else
  {
  	if (substr($roomname, -3, 3)!='-TR')
		{
  		acLog( "error ". __FUNCTION__.$roomname);
  		return (ERROR);
		}
	else
		{
		return true; // Fehler beim Löschen evtl nicht vorhandener Trainingsräume nicht als Fehler zurückmelden
		}
  }
}



function acCreate_user($sessionid, $loginid, $fname, $lname)
{
  global $ac_host, $ac_port;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (W_WAIT);
  }
  
  if (acLookup_user($sessionid, $loginid))
  {
    	//dup
    	acLog( "Duplicate user found - ignored, ".$loginid );
    	return (W_DUPL);
  }
  
  $loginid = trim($loginid);
  $url = "/api/xml?action=principal-update&login=$loginid&first-name=$fname&has-children=0&last-name=$lname&type=user&session=$sessionid";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $scoid = false;
  // erste Zeile des Headers loggen
  $logging = "principal-update:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// princ-id abholen
		if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result))
		{
			$scoid = $result[1];
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);
  
  if ( !$scoid )
  {
		acLog( $logging );
		return (ERROR);
  }

  return (ALL_OK);
}



function acCreate_room($sessionid, $roomname)
{
  global $ac_host, $ac_port, $ac_meeting_folder_id;
  
  $fp = fsockopen($ac_host, $ac_port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		acLog( "error sockopen ". __FUNCTION__);
		return (W_WAIT);
  }
  
  if (acLookup_room($sessionid, $roomname))
  {
    	//dup
    	acLog( "Duplicate room found - ignored, ".$roomname );
    	return (W_DUPL);
  }
  
  $roomname = trim($roomname);
  
  if (substr($roomname,-3,3)=='-TR')
  	{
	$url = "/api/xml?action=sco-update&name=$roomname&folder-id=$ac_meeting_folder_id&type=meeting&source-sco-id=999681&session=$sessionid";
	}
  else
  	{
	$url = "/api/xml?action=sco-update&name=$roomname&folder-id=$ac_meeting_folder_id&type=meeting&session=$sessionid";
	}
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: $ac_host\r\n\r\n");

  $scoid = false;
  // erste Zeile des Headers loggen
  $logging = "sco-update:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// sco-id abholen
		if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result))
		{
			$scoid = $result[1];
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);

  if ( !$scoid )
  {
		acLog( $logging );
		return (ERROR);
  }

  return (ALL_OK);
}


?>