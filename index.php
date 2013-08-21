<?php
// $Id: index.php,v 1.9 2003/12/05 23:04:26 sven Exp $

    $MONTH_VIEW = False;
    $WEEK_VIEW = False;
    $DAY_VIEW = False;
    $INDEX_VIEW = True;

    if(isset($_GET["d"])){$today = date("l, F j, Y",$_GET["d"]);}else{$today = date("l, F j, Y");}
    $today_tmsp = strtotime($today);

    include_once("functions.inc.php");
    check_auth();

    include_once("config.inc.php");
    include_once("$dbsys.inc.php");
    include_once("mincals.inc.php");

    $cookieValue = basename($PHP_SELF) ."?". $QUERY_STRING;
    setcookie("WhereTo",$cookieValue,time()+3600,'','',1);

    print_header();
?>

			<div id="content">
			    
<!--
			    <div id="topBar">
			
<?

#print_login_status();

?>
    
			
    			    <div id="selection">
    			    
    			        Choose:
<? 
#   echo genCenterSelector("day.php", $today_tmsp, $c, $f, $i);
?>
    		            &raquo;
<? 
#   echo genFacilitySelector("day.php", $today_tmsp, $c, $f, $i);
?>

    			        		    
    			    </div><?# End div.selection ?>

    			</div><?# End div.topBar ?>
-->    

					<table id="middle" align="center">
						<tr>
							<td>
								<div id="schedule">
									<h2>Welcome!</h2>
									<p>The Reserve system is designed to allow users to reserve time on designated Facility Instuments. Please select a Facility from one of the Centers listed on the right.</p>
									<h3>New Users</h3>
									<p> This schedule is viewable to the general public. If you would like to create reservations using this online scheduling service, please contact the Facility Manager directly.</p>
									<p>For more information on how this system works, please seek <a href="">help</a>.</p>
									<h3>News</h3>
									<p><strong>Oct 14th, 2003</strong> - Dan finally gets some ambition and makes progress on desiging the Reserve system.</p>
									<p><strong>Oct 14th, 2003</strong> - New welcome page created. Will be the entrance point of the system and will contain system wide news as well as links to the day view of each facility.</p>
								</div><?# End div.schedule ?>
							</td>
							<td width="250">
								<div id="menus">
									
<?php
/* If user from FAC_STAFF logs in, show AdminMenu. */
if($reserve_authed!=''){createAdminMenu();}

?>
						
<?

## Creates menu for Center and Facilities linking to Day view.

$qry_center = "SELECT center_name.name_short,center_name.center_id FROM center_name ORDER BY name_short ASC";
$rslt_center = sql_query($qry_center);
while ($row_c = mysql_fetch_array($rslt_center)):
	
	$cid = $row_c["center_id"];
	$qry_facility = "SELECT name_short,fac_id FROM fac_name WHERE center_id like '$cid' ORDER BY name_short ASC";
	$rslt_facility = sql_query($qry_facility);
	
	echo "<div class=\"center\">\n";
	echo "<h2>".$row_c["name_short"]."</h2>\n";
	echo "<ul>\n";
	while ($row_f = mysql_fetch_array($rslt_facility)):
		echo "<li><a href=\"day.php?c=".$row_c["center_id"]."&f=".$row_f["fac_id"]."\">".$row_f["name_short"]."</a></li>\n";
	endwhile;
	echo "</ul>\n";
	echo "</div>\n";
	
endwhile;

?>
									
								</div><?# End div.menu ?>
							</td>
						</tr>
					</table>
    			    			
    		</div><?# End div.content ?>

<?php

print_footer();

?>
