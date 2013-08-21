<?php
// $Id: reserve.php,v 1.28 2004/01/23 21:28:23 sven Exp $

if(isset($_GET["d"])){$today_tmsp = $_GET["d"];}else{$today_tmsp = time();}
	
include_once("functions.inc.php");
check_auth();

include_once("config.inc.php");
include_once("$dbsys.inc.php");
include_once("mincals.inc.php");

$cookieValue = basename($PHP_SELF) ."?". $QUERY_STRING;
setcookie("WhereTo",$cookieValue,time()+3600,'','',1);


# is this a normal booking or a modification?
if(isset($_GET["book_id"])) {
    $book_mod = True;
    $db_booking = getBookingDB($_GET["book_id"]);
    $BookInfo = new ReserveBooking($db_booking);
    $today_tmsp = $BookInfo->start_time;
}
else {
    $book_mod = False;
}
print_header();

# make sure we're within viewable range
$facility = new ReserveFacility($_GET["f"]);
if(!$facility->checkViewDate($today_tmsp)) {
    echo "<b>The date you have requested is not viewable.</b>\n";
    print_footer();
    exit;
}
?>
			
    <div id="content">		
    	
			<table id="middle" align="center">
				<tr>
					<td>
        <div id="reserveOne">
            <h2>Schedule Some Time</h2>

            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="res1">Center: </td>
                    <td class="res2">

<?php
   echo genCenterSelector("reserve.php", $today_tmsp, $c, $f, $i, "get");
?>

                    </td>
                </tr>
                <tr>
                    <td class="res1">Facility: </td>
                    <td class="res2">
<?php
   echo genFacilitySelector("reserve.php", $today_tmsp, $c, $f, $i, "get");
?>
                    </td>
                </tr>
                <tr>
                    <td class="res1">Instrument: </td>
                    <td class="res2">

<?php
   echo genInstrumentSelector("reserve.php", $today_tmsp, $c, $f, $i, "get");
?>
                    </td>
                </tr>
                <tr>
                    <td class="res1">Date: </td>
                    <td class="res2">
<?php
   echo genDateSelector("reserve.php", $today_tmsp, $c, $f, $i);
?>
					</td>
				</tr>
			</table>
            <form name="bookingDesc" action="edit_entry_handler.php" method="post">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="res1">Time: </td>
					<td class="res2">
<?php
    $resolution = 15*60;
    genTimeSelector("reserve.php", $today_tmsp, $resolution);
?>

                    </td>
                </tr>
                <tr>
			<td class="res1">Duration: </td>
			<td class="res2">
<?php
    if($book_mod) {
        $durHour = $BookInfo->getDurationHours();
        $durMin = sprintf("%02d", $BookInfo->getDurationMinutes());
    }
    else {
        $durHour = "1";
        $durMin = "00";
    }
?>
				<input type="text" name="durationHour" value="<?php echo $durHour;?>" size="3" maxlength="2" /> hour(s) 
				<input type="text" name="durationMinute" value="<?php echo $durMin;?>" size="2" maxlength="2" /> minute(s)
			</td>
                </tr>
                <tr>
			<td class="res1">Booking Type: </td>
			<td class="res2">
<?php
    if($book_mod) {
        genBookTypeSelector($BookInfo->book_type);
    }
    else {
        genBookTypeSelector();
    }
?>
                                              </td>
					<tr>
						<td class="res1">Brief Description:</td>
						<td class="res2">
							<textarea name="description" cols="30" rows="10"><?php if($book_mod){echo $BookInfo->description;}?></textarea>
						</td>
					</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="font-size:16px;"><input type="submit" value="Reserve" style="font-weight: bold;" /></td>
				</tr>
            </table>
						<input type="hidden" name="c" value="<? echo $c; ?>" />
						<input type="hidden" name="f" value="<? echo $f; ?>" />
						<input type="hidden" name="i" value="<? echo $i; ?>" />
						<input type="hidden" name="d" value="<? echo $d; ?>" />
<?php
    if($book_mod) {
        echo $tab[6] . "<input type=\"hidden\" name=\"book_id\" value=\"" .  $BookInfo->book_id . "\"/>\n";
    }
?>
            </form>

<?php

# display bookings that have already been made for today
# so the user knows about possible conflicts

$thisdate = getdate($_GET["d"]);
$hours_start = mktime(0, 0, 0, $thisdate["mon"], $thisdate["mday"], $thisdate["year"]);
$hours_stop = mktime(23, 59, 59, $thisdate["mon"], $thisdate["mday"], $thisdate["year"]);

$bookings = getBookingsInstrument($i, $hours_start, $hours_stop);

echo $tab[3];
echo "<div id=\"reservedBookings\">\n";
echo $tab[4];
echo "<h2>Current Bookings</h2>\n";
echo $tab[4];
echo "<ul>\n";

if(empty($bookings)) {
    echo $tab[5];
    echo "<li>There are currently no bookings for this day.</li>\n";
}

foreach($bookings as $booking) {
    echo $tab[5];
    echo "<li>" . $booking->PrintTime() . "</li>\n";
}
echo $tab[4];
echo "</ul>\n";
echo $tab[3];
echo "</div>\n";

?>
        </div>    

					</td>
					<td>
						<div id="menus">
		
								<div id="labNotes">
								<h2>Lab Notes</h2>
								<p>All instruments are functioning normally.</p>
								</div>
		
								<div id="description">
								<h2>Instrument Information</h2>
								</div>	
						
<?php
	 $caldate = getdate($today_tmsp);
	 minicals($caldate["year"], $caldate["mon"], $caldate["mday"], "reserve.php");
?>
		
						</div>
					</td>
				</tr>
			</table>
		    
    </div>
					    
<?php

print_footer();

?>
