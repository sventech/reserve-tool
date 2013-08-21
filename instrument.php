<?php

include_once("functions.inc.php");
include_once("config.inc.php");
include_once("$dbsys.inc.php");
include_once("mincals.inc.php");

if(isset($_GET["c"])){$c = $_GET["c"];}else{$c = get_default_center();}
if(isset($_GET["f"])){$f = $_GET["f"];}else{$f = get_default_facility($c);}
if(isset($_GET["i"])){$i = $_GET["i"];}else{$i = get_default_instrument($f);}

$qry = "SELECT name_short FROM center_name WHERE center_id like '$c'";
$rslt = sql_query($qry);
$row = mysql_fetch_array($rslt);
$center = $row["name_short"];
$fac = new ReserveFacility($f);
$inst = new ReserveInstrument($i);


?>

<html>

	<head>
		<title>Reserve - Instrument Description</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
		<link rel="stylesheet" href="reserve.css" media="screen" type="text/css"/>
		<link rel="stylesheet" href="reserve.css" media="print" type="text/css"/>
	</head>
	
	<body>
	
		<div id="container">
		
			<div id="equipExplain">
			
				<h2>Instrument Description</h2>
				<div style="clear:both; text-align:right; font-size:9px;"><a href="#" onclick="window.close();">Close Window</a></div>
				<div id="equipPhoto"><img src="images/no_instrument_200x200.gif" width="200" height="200" alt="Instrument Photo Not Available" /></div>
				<div id="equipDescrip">
					<p><span>Center:</span><br /><strong><? echo $center; ?></strong></p>
					<p><span>Facility:</span><br /><strong><? echo $fac->name_long; ?></strong></p>
					<p><span>Name:</span><br /><strong><? echo $inst->name_long; ?></strong></p>
					<p><span>Abbreviated Name:</span><br /><strong><? echo $inst->name_short; ?></strong></p>
					<p><span>Quick Description:</span><br /><? echo $inst->desc_short; ?></p>
					<p><span>Full Description:</span><br /><? echo $inst->desc_long; ?></p>
					<p><span>Location:</span><br /><? echo $inst->location; ?></p>
				</div>
				<div style="clear:both; text-align:right; font-size:9px;"><a href="#" onclick="window.close();">Close Window</a></div>
				
			</div><?# End div.equipExplain ?>
			
			<div id="footer">
				<span>&copy; 2003 Cornell Center for Materials Research | <a href="mailto:admin@mycompany.com">admin@mycompany.com</a></span>
			</div><?# End div.footer ?>
			
		</div><?# End div.container ?>
	
	</body>
	
</html>
