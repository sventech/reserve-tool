<?php
// $Id: month.php,v 1.15 2003/12/02 23:39:17 sven Exp sven $

    $MONTH_VIEW = True;
    $WEEK_VIEW = False;
    $DAY_VIEW = False;
    $INDEX_VIEW = False;

    if(isset($_GET["d"])){$today = date("l, F j, Y",$_GET["d"]);}else{$today = date("l, F j, Y");}
    $today_tmsp = strtotime($today);
    
   include_once("functions.inc.php");
   check_auth();

   include_once("config.inc.php");
   include_once("mysql.inc.php");
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
   genCenterSelector("month.php", $today_tmsp, $c, $f, $i);
?>
    		            &raquo;
<? 
   genFacilitySelector("month.php", $today_tmsp, $c, $f, $i);
?>
   		            	&raquo;
<? 
   genInstrumentSelector("month.php", $today_tmsp, $c, $f, $i);
?>
    			        		    
    			    </div>
    			    
    			</div>
					
					<table id="middle" align="center">
						<tr>
							<td>
<?php
include("monthsched.inc.php");
$sched = new MonthSchedule($today_tmsp, $i, $netID_secure, $PHP_SELF, "day.php", 1);
echo $sched->getHTML();

?>
							</td>
							<td width="250">
								<div id="menus">
			
<?php
/* If user from FAC_STAFF logs in, show AdminMenu. */
if($reserve_authed!=''){createAdminMenu();}

?>
								
										<div id="labNotes">
												<h2>Lab Notes</h2>
												<p>All instruments are functioning normally.</p>
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
