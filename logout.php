<?    
// $Id: logout.php,v 1.2 2003/12/04 18:28:13 sven Exp sven $

if(isset($_GET["d"])){$today = date("l, F j, Y",$_GET["d"]);}else{$today = date("l, F j, Y");}
$today_tmsp = strtotime($today);

if(!isset($_COOKIE["WhereTo"])) {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/";
}
else {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/" . $_COOKIE["WhereTo"];
}

include_once("functions.inc.php");
check_auth();

include_once("config.inc.php");
include_once("$dbsys.inc.php");
include("mincals.inc.php");

$cookieValue = basename($PHP_SELF) ."?". $QUERY_STRING;
setcookie("WhereTo",$cookieValue,time()+3600,'','',1);
print_header();
?>


			<div id="content">
<?	
	$authobject->logout();
	header($whereto);
?>
			</div>
					    
<?php

print_footer();

?>
