<?php
// delete many AC Users

require "acifwrite.php";

$liste = fopen("/tmp/ac_duser", "r");

while($ud = fgets($liste))
{
	
	$ud = trim($ud);
	$ret = aiw_Delete_user($ud);
	echo $ud."  ->  ".$ret."<br>\n";
}

echo "<br>ende";
fclose ($liste);
?>