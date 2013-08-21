<?
// $Id: details.php,v 1.5 2004/01/13 21:54:16 sven Exp $

    $CURR_VIEW = basename($PHP_SELF, ".php");

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

    $book_id = $_GET["book_id"];
    $db_booking = getBookingDB($book_id);
    $BookInfo = new ReserveBooking($db_booking);

    $center  = new ReserveCenter($_GET["c"]);
    $facility  = new ReserveFacility($_GET["f"]);
    $instrument = new ReserveInstrument($_GET["i"]);

    $durMin = $BookInfo->getDurationMinutes();
    $durHour = $BookInfo->getDurationHours();
?>

			<div id="content">
			           		
					<table id="middle" align="center">
						<tr>
							<td>

								<div id="schedule">
									<h2>reservation details</h2>
									<table border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td class="res1">Center: </td>
											<td class="res2"><? echo $center->name_short; ?></td>
										</tr>
										<tr>
											<td class="res1">Facility: </td>
											<td class="res2"><? echo $facility->name_short; ?></td>
										</tr>
										<tr>
											<td class="res1">Instrument: </td>
											<td class="res2"><? echo $instrument->name_short; ?></td>
										</tr>
										<tr>
											<td class="res1">Date: </td>
											<td class="res2"><? echo date("l, F j, Y",$BookInfo->start_time); ?></td>
										</tr>
										<tr>
											<td class="res1">Time: </td>
											<td class="res2"><? echo date("H:i",$BookInfo->start_time); ?></td>
										</tr>
										<tr>
											<td class="res1">Duration: </td>
											<td class="res2"><? printf("%d:%02d",$durHour,$durMin); ?></td>
										</tr>
										<tr>
											<td class="res1">Booking Type: </td>
											<td class="res2"><? echo $BookInfo->book_type; ?></td>
										<tr>
											<td class="res1">Brief Description:</td>
											<td class="res2"><? echo $BookInfo->description; ?></td>
										</tr>
									</table>
<?php
function getLink()
{
     global $BookInfo,$today_tmsp,$c,$f,$i;
     return "d=" . $BookInfo->start_time . "&c=$c&f=$f&i=$i&book_id=" . $BookInfo->book_id;

}
?>
									
									<div style="margin:10px 0px; text-align:center;">
<?php
    echo "<h4><a href=\"reserve.php?" . getLink() . "\">Modify Reservation</a></h4>\n";
    echo "<h4><a href=\"del_entry.php?" . getLink() . "\">Delete Reservation</a></h4>\n";
?>
									</div>
									
								</div>

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
	echo "\t\t\t\t\t<li><a href=\"\">".$row["name_long"]."</a></li>\n";
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
	 minicals($caldate["year"], $caldate["mon"], $caldate["mday"], $PHP_SELF);
?>
										
								</div>
							</td>
						</tr>
					</table>		    
			    
			</div>
				    
<?php

print_footer();

?>
