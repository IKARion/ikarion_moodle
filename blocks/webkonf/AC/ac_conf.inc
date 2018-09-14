<?php
// Globale Werte Connect
$ac_host = 'connect.private.oncampus.de';              // f.d. fsockopen()
$ac_host2 = "connect.oncampus.de";                    // f.d. Moodlelink acenter.php...

date_default_timezone_set("Europe/Berlin");

$ac_port = 80;
$ac_admin = 'adm_api!LU';
$ac_xuserid = '%YfF9Qr4Yj/';
// meeting_folder_id ist ein Systemordner - daher statisch
$ac_meeting_folder_id = 10004;
$ac_logfile = '/var/log/moodle/ac_error.log';
$ac_db_host = 'moodledb.private.oncampus.de';
$ac_db_user = 'acuser';
$ac_db_pw = 'dP,fdacU!';
$ac_db_name = 'acqueue';
define("CREATE_ROOM", "a");
define("CREATE_USER", "b");
define("CREATE_ASSI", "c");
define("DELETE_ROOM", "d");
define("DELETE_USER", "e");
define("DELETE_ASSI", "f");
define("MODIFY_USER", "g");
define("ALL_OK", 0);
define("W_DUPL", 1);
define("W_WAIT", 2);
define("ERROR", 99);
?>
