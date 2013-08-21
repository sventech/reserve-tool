<?
// $Id: week.php,v 1.12 2003/12/05 22:17:04 sven Exp $

    $MONTH_VIEW = False;
    $WEEK_VIEW = True;
    $DAY_VIEW = False;
    $INDEX_VIEW = False;

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
			    
			    <div id="topBar">
			
<?

#print_login_status();

?>
    			
    			    <div id="selection">
    			    
    			        Choose:
<? 
   genCenterSelector($PHP_SELF, $today_tmsp, $c, $f, $i);
?>
    		            &raquo;
<? 
   genFacilitySelector($PHP_SELF, $today_tmsp, $c, $f, $i);
?>
    		            &raquo;
<? 
   genInstrumentSelector($PHP_SELF, $today_tmsp, $c, $f, $i);
?>
    			        		    
    			    </div>
    			    
    			</div>
					
					<table id="middle" align="center">
						<tr>
							<td>
<?
    include('weeksched.inc.php');
    $sched = new WeekSchedule($today_tmsp, $i, $netID_secure, $PHP_SELF, "reserve.php");
    echo $sched->getHTML();
?>
							</td>
							<td>
								<div id="menus">
			
<?php
/* If user from FAC_STAFF logs in, show AdminMenu. */
if($reserve_authed!=''){createAdminMenu();}

?>
			
										<div id="labNotes">
												<h2>Lab Notes</h2>
												<p>All instruments are functioning normally.</p>
										</div>
								
										<div id="description" style="display:none;">
											<h2>Instrument List</h2>
											<ol>		        
<?
$qry = "SELECT name_short,name_long FROM fac_equipment WHERE fac_id LIKE '$f' AND schedule like '1' ORDER BY view_order ASC, name_short ASC";
$rslt = sql_query($qry);
while($row = mysql_fetch_array($rslt)):
	echo "<li><a href=\"\">".$row["name_long"]."</a></li>\n";
endwhile;
?>
									</ol>
										</div>
										
										<div id="jumpTo">
												<h2>Jump To</h2>
												<ul>
														<li><a href="./day.php">Today</a></li>
														<li><a href="./week.php">This Week</a></li>
														<li><a href="./month.php">This Month</a></li>
														<li>Week of: 
<?php
		genWeekOfSelector($today_tmsp);
?>
										</li>
														<li>Month of:
<?php
		genMonthOfSelector($today_tmsp);
?>
										</li>
												</ul>
										</div>
										
<?
	 $caldate = getdate($today_tmsp);
	 minicals($caldate["year"], $caldate["mon"], $caldate["mday"], "day.php");
?>
										
								</div>
							</td>
						</tr>
					</table>
       		
		    
			    
			</div>
				    
<?php

print_footer();

?>
