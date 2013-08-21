<?php

/********** Massage GET data */

if(!isset($_GET["mi"])){
	$mi = -1;
}
else{
	$mi = $_GET["mi"];
}

/********** Massage POST data */

if(isset($_POST["submit"])){

	$p_view_order = $_POST["view_order"];
	$update_data = "UPDATE fac_equipment SET view_order = '$p_view_order' WHERE equip_id LIKE '$mi'";

	$link2 = mysql_connect('localhost', 'reserveAdmin', '!XiAwt');
	if ($link2){
		$connected = mysql_select_db('reserve');
		if ($connected){
			$rslt = mysql_query($update_data, $link2);
		}
		else{
			echo "Update didn't work.<br>\n";
		}
	}
	else{
		echo "Couldn't connect to server. Please contact <a href=\"mailto:webmaster@ccmr.cornell.edu\">webmaster</a>";
	}
}

?>