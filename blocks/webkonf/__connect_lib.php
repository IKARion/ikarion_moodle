<?php
/**

			ADOBE-Connect Controler 1.0 - oncampus, Fachhochschule L�beck
			
			__construct()
				$host												Host des Connectsystems
				$port												i.d.R. 80
				$ac_meeting_folder_id				Folderid UNTERHALB dessen die ganzen Meetings liegen
				$ac_admi										API-Username
				$ac_xuserid									HTTP-Auth-Token (siehe web.xml)

	
			Methoden:
			
			[bool]			connectionOk()				liefert TRUE bei erfolgreicher Verbindung/Authentifizierung zur�ck
			[void]			log( $text )					schreibt interne Logzeile
			[string]  	getLogText()					liefert interne Logs zur�ck	

			[bool]			createUser("username", "Vorname", "Nachname")
									Legt definierten User an. Liefert FALSE zur�ck falls der User bereits existiert. TRUE wenn Operation ok ist.
									
			[bool]			deleteUser("username")
									L�scht definierten User, liefert FALSE zur�ck falls User nicht existiert.
									
			[bool]			createRoom("Raumname")
									Legt Meeting mit dem Name "Raumname" an.Liefert FALSE zur�ck falls Meeting bereits existiert.
									
			[bool]			deleteRoom("Raumname")
									L�scht Raum "Raumname", liefert FALSE zur�ck falls Meeting nicht existiert.

			[string]		getRoomUrl("Raumname")
									Liefert URL des Meetings zur�ck als vollst�ndigen Pfad, z.B. http://meinconnect.domain.com/r12345678
									
			[bool]			checkAssignment("username", "Raumname")
									Pr�ft ob Benutzer im Meeting als Teilnehmer drinne ist, liefert FALSE zur�ck wenn nicht - ansonten TRUE
									
			[bool]			assignUser ("username","Raumname", [int] Rolle)
									Weisst User "username" in Meeting "Raumname" mit der Rolle zu. 1= Student, 2 = Veranstalter
									Liefert FALSE zur�ck falls User oder Raumname nicht existiert.
									
			[array]			getRecordings("raumname")
									Liefert Daten aller Aufzeichnung zum Meeting "raumname" als assoz.Array zur�ck.
									
									
**/

class AC_controler {
	
		private $connected = false;
		private $session = NULL;
		
		private $https = false;
		
		public function __construct($host, $port, $ac_meeting_folder_id, $ac_admin, $ac_xuserid) {
			
					$this->host 			= $host;
					$this->port 			= $port;
					$this->ac_admin 	= $ac_admin;
					$this->ac_xuserid = $ac_xuserid;
					
					$this->ac_meeting_folder_id = $ac_meeting_folder_id;
					$this->sessionid  = "";
					$this->connection = false;
					
					$this->logText = array();
							
					$this->log("Connecting to $host".":".$port);
					$return = $this->getSession();
					if ($return == false ) { 
						$this->log("Error!");
					} else {
						$this->log("Success: SessionId = ".$this->sessionid." ;-) ");
						$this->connection = true;
					}
					$this->log("------------------------------------------------------------------------------------------");
		}
		
		public function __destruct() {}
		
		function conneectionOk() {
				return $this->connection;
		}
			
			
		function log($arg) {
			$this->logText[] = $arg;
		}
		function getLogText() {
			$result = "";
			foreach ($this->logText as $e) { $result .= " $e <br />"; }
				return $result;
		}
		
		
		function getSession() {
	
			$fp = fsockopen($this->host, $this->port);
			if (!$fp) {
				return false; // Connection-Error
			}
			
			$url = '/api/xml?action=login&external-auth=use';
			fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->ac_xuserid.":".$this->ac_admin."\r\nHost: ".$this->host."\r\n\r\n");
			
			$logging = "";
			$sessionid = false;
			$retv = false;
			
			$this->log ("Login-Request: ".$url);
			while($line = fgets($fp)) 
			{
				$this->log(":: ".$line);
					// breeze-session-cookie rausholen
					if (strstr($line, "code=\"ok\"")) {
						$retv = true;
						break;
					} else {
						$logging .= $line;
					}
					if (preg_match('/BREEZESESSION=(.*)\;/U', $line, $result)) {
						$sessionid = $result[1];
					}
			}
			fclose($fp);
			// log bei Bedarf schreiben
			if ( !$retv )
			{
		  $this->log("CONNECTION-ERROR:<br /> $logging ");
		  
			return false;
			}
			
			$this->sessionid = $sessionid;
			return true;
		}
		
function createUser($loginid, $firstname, $lastname) {
			
		//	function acCreate_user($sessionid, $loginid, $fname, $lname)
		$this->log("try to create user $firstname $lastname ($loginid) ");
		
		
		$fp = fsockopen($this->host, $this->port);
		
		if ($this->lookupUser($loginid)) {
			$this->log("Duplicate User $loginid, not created ");
			return false;
		}  
		
		# oncampus-update: eventuelle doppelnamen (Leerzeichen) urlencoden
		$lastname = urlencode($lastname);
		$firstname = urlencode($firstname);
		
		$loginid = trim($loginid);
		$url = "/api/xml?action=principal-update&login=$loginid&first-name=$firstname&has-children=0&last-name=$lastname&type=user&session=".$this->sessionid."";
		fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
		
		$scoid = false;
		// erste Zeile des Headers loggen
		$logging = "principal-update:".fgets($fp, 150);
		while($line = fgets($fp)) {
			// princ-id abholen
			if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result)) {
				$scoid = $result[1];
			} else {
				$logging .= $line;
			}
		}
		fclose($fp);
		
		if ( !$scoid ) {
			$this->log("Could not create $firstname $lastname ($loginid) ");
			return false;
		}
		return true;
		$this->log("$firstname $lastname ($logindid) created!");
} // creazeUser
		
		
function deleteUser($loginid) {
			
		$fp = fsockopen($this->host, $this->port);
		$oid = $this->lookupUser($loginid);
		if ($oid) { 
			$url = "/api/xml?action=principals-delete&principal-id=$oid&session=".$this->sessionid."";
			fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
			
			// $retv = LookupOK($fp, __FUNCTION__);
			fclose($fp);
			$this->log("$loginid deleted!");
			
			return (true);
		} else {
			return false;
		}
}
	
		
function lookupUser($loginid) {
  
		$fp = fsockopen($this->host, $this->port);
		if (!$fp) {
			// Socket kann nicht geoeffnet werden
			return false;
		}
		
		$url = "/api/xml?action=principal-list&filter-login=$loginid&session=".$this->sessionid."";
		fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
		
		$scoid = false;
		$retv = false;
		// erste Zeile des Headers loggen
		$logging = "principal-list:".fgets($fp, 150);
		while($line = fgets($fp)) {
			// sco-id rausholen
			if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result)) {
				$scoid = $result[1];
			}
			if (strstr($line, "code=\"ok\"")) {
				$retv = true;
			} else {
				$logging .= $line;
			}
		}
		fclose($fp);
		if ( !$retv ) {
		//acLog( $logging );
		} else {
		
		}
			return ($scoid);
}

function createRoom ($roomname) {
	
	$roomname = urlencode($roomname);
	
  $fp = fsockopen($this->host, $this->port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		return (false);
  }
  
  if ($this->lookupRoom($roomname))
  {
    	//dup
    	$this->log( "Duplicate room found - ignored, ".$roomname ." not created twice");
    	return (false);
  }
  
  $roomname = trim($roomname);
  $url = "/api/xml?action=sco-update&name=$roomname&folder-id=".$this->ac_meeting_folder_id."&type=meeting&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");

  $scoid = false;
  // erste Zeile des Headers loggen
  $logging = "sco-update:".fgets($fp, 150);
  while($line = fgets($fp))
  {
		// sco-id abholen
		if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result))
		{
			$scoid = $result[1];
			$this->log("room $roomname created ");
		}
		else
		{
			$logging .= $line;
		}
  }
  fclose($fp);

  if ( !$scoid )
  {
		$this->log("error while creating room $roomname ");
		return (false);
  }

  return (true);

}//createRoom()


function deleteRoom($roomname) {
	
	
	
	$oid = $this->lookupRoom($roomname);
  if ($oid) 
  { 
		 
		  $fp = fsockopen($this->host, $this->port);
		  if (!$fp) 
		  {
				// Socket kann nicht geoeffnet werden
				return (false);
		  }

		  $url = "/api/xml?action=sco-delete&sco-id=$oid&session=".$this->sessionid."";
		  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
		  fclose($fp);
		  $this->log("$roomname deleted ");
		  return (true);
	} else {
  		$this->log("Error while deleting room: $roomname ");
  		return (false);
  }

} //deleteRoom()

function lookupRoom($roomid) {
	
	$roomid = urlencode($roomid);
	
  $fp = fsockopen($this->host, $this->port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		return false;
  }

  $url = "/api/xml?action=sco-expanded-contents&sco-id=".$this->ac_meeting_folder_id."&filter-type=meeting&filter-name=$roomid&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");

  $scoid = false;
  $retv = false;
  // erste Zeile des Headers loggen
  $logging = "sco-expanded-contents:".fgets($fp, 150);
  while($line = fgets($fp)) {
		// sco-id rausholen
		if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result)) {
			$scoid = $result[1];
		}
		if (strstr($line, "code=\"ok\"")) {
			$retv = true;
		} else {
			$logging .= $line;
		}
  }
  fclose($fp);
  if ( !$retv )
  {
		// acLog( $logging );
  }
  return ($scoid);
  
} //lookupRoom

		
function getRoomUrl($roomname) {
	
	$roomname = urldecode($roomname);
	$oid = $this->lookupRoom($roomname);
 
  
  
  $fp = fsockopen($this->host, $this->port);
  if (!$fp) 
  {
		// Socket kann nicht geoeffnet werden
		
		return false;
  }

  $url = "/api/xml?action=sco-info&sco-id=$oid&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");

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
		$this->Log( $logging );
  }
  $this->log(" room \"$roomname\" ist located at http://".$this->host."/$url ");
  return ("http://".$this->host."/".$url);
 

} //getRoomUrl 


function assignUser ($username, $roomname, $role = 1) {
	
	$this->log("assigning user $username in room $roomname with role $role ... ");
  if (!$rid = $this->lookupRoom($roomname))
  {
  	$this->log ( "ERROR: room $roomname not found!");
  	return (false);
  }
  if (!$pid = $this->lookupUser($username))
  {
  	$this->log( "error User not found ".$username);
  	return (false);
  }
  
  switch ( $role )
  {
    case '1':
    	$ac_role = 'view';
    	break;
    case '2':
    	$ac_role = 'host';
    	break;
    case '3':
    	$ac_role = 'mini-host';
    	break;
   	default:
		$ac_role = 'remove';
  }
  
  $fp = fsockopen($this->host, $this->port);
   $url = "/api/xml?action=permissions-update&permission-id=$ac_role&acl-id=$rid&principal-id=$pid&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
  
  
  fclose($fp);
  return (true); 
} //assignuser
		


function unassignUser ($username, $roomname) {
	
	$this->log("unassigning user $username in room $roomname with role $role ... ");
  if (!$rid = $this->lookupRoom($roomname))
  {
  	$this->log ( "ERROR: room $roomname not found!");
  	return (false);
  }
  if (!$pid = $this->lookupUser($username))
  {
  	$this->log( "error User not found ".$username);
  	return (false);
  }
  
 
		$ac_role = 'remove';
 
  
  $fp = fsockopen($this->host, $this->port);
   $url = "/api/xml?action=permissions-update&permission-id=remove&acl-id=$rid&principal-id=$pid&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
  
  
  fclose($fp);
  return (true); 
} //assignuser



function checkAssignment ($username, $roomname) {
	
	$this->log("checking assginment for user \"$username\" in room \"$roomname\" ");
	if (!$rid = $this->lookupRoom($roomname))
  {
  	$this->log ( "ERROR: room $roomname not found!");
  	return (false);
  }
  if (!$pid = $this->lookupUser($username))
  {
  	$this->log( "error User not found ".$username);
  	return (false);
  }
	
	$fp = fsockopen($this->host, $this->port);
	$url = "/api/xml?action=permissions-info&principal-id=$pid&acl-id=$rid&session=".$this->sessionid."";
  fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");

  $scoid = false;
  $retv = false;
  // erste Zeile des Headers loggen
  $logging = "sco-expanded-contents:".fgets($fp, 150);
  $permission = false;
  while($line = fgets($fp)) {
		// $this->log(htmlspecialchars($line));
	 if (  substr_count($line, "permission-id=\"view\"") == 1 || substr_count($line,"permission-id=\"host\"")== 1 || substr_count($line,"permission-id=\"mini-host\"")== 1) {
	 	$permission = true;
		}
	}
	if ($permission == true) { $this->log("user is assinged!"); } else { $this->log("User not asigned");}
	
	return $permission;
}
		
		function getRecordings($roomname) {
			
	
		
	if (!$rid = $this->lookupRoom($roomname))
  {
  	$this->log ( "ERROR: room $roomname not found!");
  	return (false);
  }	
	
	$fp = fsockopen($this->host, $this->port);
	$url = "/api/xml?action=sco-contents&sco-id=$rid&filter-icon=archive&session=".$this->sessionid."";
	fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->host."\r\n\r\n");
	$result="";
	$start=false;
	$xml_data = "";
	$record = false;
	while($line = fgets($fp)) {
		if(substr_count($line,"<?xml version=") == 1) { $record = true; }
		
		if ($record == true) {
				$line = str_replace("url-path","urlpath",$line);
				$line = str_replace("date-begin","datebegin",$line);
				$xml_data .= $line;
			}
		$this->log(htmlspecialchars($line));	
		}
    
	  $xml = new SimpleXMLElement($xml_data);
	  
	  $result = array();
	  
	  foreach ($xml->scos->sco as $entry) {
	  	$name =  (string) $entry->name;
	  	$description = (string) $entry->description;
	  	$laenge_p = (string) $entry->duration;
	  	$laenge_p = explode(".",$laenge_p);
	  	$laenge = $laenge_p[0];
	  	
	  	$url = "http://".str_replace(".private.",".",$this->host.$entry->urlpath);
	  	
	  	$scoid = (string) $entry["sco-id"];
	  	
	  	$date_start = $entry->datebegin;
	  	$p = explode("T",$date_start);
	  	$pp = explode("-",$p[0]);
	    $date_start_single = $pp[2].".".$pp[1].".".$pp[0];
	  
	  	$result[] = array (
	  	
	  				"name"		=> $name,
	  				"description"		=> $description,
	  				"length"				=> $laenge,
	  				"url"						=> $url,
	  				"date"					=> $date_start_single,
	  				"sco-id"				=> $scoid
	  				);
	  				
	  }
	 
	 $result = array_reverse($result);
	 return $result;
	 
	}
		
	function loginAs($username,$roomname) {
	$roomname = urldecode($roomname);
	
	if ($this->checkAssignment ($username, $roomname)) {
		
	$roomUrl = $this->getRoomUrl($roomname);
	//echo $roomUrl;
	
	$fp = fsockopen($this->host, $this->port);
	$url = '/api/xml?action=login&external-auth=use';
  fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->ac_xuserid.":".$username."\r\nHost: ".$this->host."\r\n\r\n");

  $usersession = false;
  while($line = fgets($fp)) {
	// breeze-session-cookie rausholen
	if (preg_match('/BREEZESESSION=(.*)\;/U', $line, $result))
	{
		$usersession = $result[1];
		break;
	}
  }
  fclose($fp);
	
	if ($usersession != false) {
		
		if ($this->https == true) {
			$roomUrl = str_replace("http:","https:",$roomUrl);
		}
		  header("location: ".str_replace('private.','',$roomUrl)."?session=$usersession");
}
	} else {
		
		if ($_GET["illi"] == "debugMe!") {
			echo "<tt>".$this->getLogText();
		}
			die ("permission denied!");
	}	 







	}


	function showRecording($username, $recordurl) {
		
	$fp = fsockopen($this->host, $this->port);
	$url = '/api/xml?action=login&external-auth=use';
  fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->ac_xuserid.":".$username."\r\nHost: ".$this->host."\r\n\r\n");

  $usersession = false;
  while($line = fgets($fp)) {
		// breeze-session-cookie rausholen
		if (preg_match('/BREEZESESSION=(.*)\;/U', $line, $result)) {
			$usersession = $result[1];
			break;
		}
  }
  fclose($fp);
  
  if ($this->https == true) {
  	$recordurl = str_Replace("http:","https:",$recordurl);
  }
  
  if ($usersession != false) {
  		header("Location: $recordurl"."?session=$usersession");
  }
		
	}


} // class end

?>