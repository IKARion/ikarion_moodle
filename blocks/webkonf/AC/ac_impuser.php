<?php
// import initial AC User

require "acifwrite.php";

$liste = fopen("/tmp/ac_user", "r");

while($line = fgets($liste))
{
	$ud = explode(';', $line);
	// login;fn;ln
	$ud[2] = trim($ud[2]);
	$ud[0] = trim($ud[0],'"');
	$ud[1] = trim($ud[1],'"');
	$ud[2] = trim($ud[2],'"');
	$ret = aiw_Create_user(trim($ud[0]), trim($ud[1]), trim($ud[2]));
	echo $ud[0]."  ->  ".$ret."<br>\n";
}

echo "<br>ende";
fclose ($liste);
?>
