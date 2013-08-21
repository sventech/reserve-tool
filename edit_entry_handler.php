<?php
// $Id: edit_entry_handler.php,v 1.9 2004/01/13 20:02:17 sven Exp $

include_once("functions.inc.php");
check_auth();

include("config.inc.php");
include("$dbsys.inc.php");
include("reserve_sql.inc.php");

# make sure we got all the critical stuff
if(isset($_POST["d"], $_POST["startHour"], $_POST["startMinute"],
   $_POST["durationHour"], $_POST["durationMinute"], $_POST["description"],
   $_POST["c"], $_POST["f"], $_POST["i"], $_POST["book_type"]))
{
    $today_tmsp   = $_POST["d"];
    $startHour    = $_POST["startHour"];
    $startMinute  = $_POST["startMinute"];
    $durHour      = $_POST["durationHour"];
    $durMinute    = $_POST["durationMinute"];
    $description  = $_POST["description"];
    $c            = $_POST["c"];
    $f            = $_POST["f"];
    $i            = $_POST["i"];
    $book_type    = $_POST["book_type"];
    $equip_id     = $i;
}
else {
    # we're in deep tapioca
    echo "<b>one of the variables was not set!!!</b>\n";
}

# make sure the user has authenticated
if($reserve_authed) {
    $user_id = $reserve_authed;
    $user = new ReserveUser($user_id);
}
else {
    $whereTo = $reserve_base_http . "/login.php";
    Header($whereTo);
    exit;
}

# for editing existing bookings
#     make sure the user has permission to edit the booking
$book_id = 0;
if(isset($_POST["book_id"])) {
    $book_id = $_POST["book_id"];
    $booking = GetEntryInfo($book_id);

    if(!$user->HasAccess($booking)) {
        showAccessDenied("You are not authorized to change that booking");
        exit;
    }
}

if (!$twentyfourhour_format) {
    if (isset($_POST["ampm"]) && ($_POST["ampm"] == "pm")) {
        $startHour += 12;
    }
}

$tu = getdate($today_tmsp);
$starttime = mktime($startHour, $startMinute, 0, $tu["mon"], $tu["mday"], $tu["year"]);
$duration = ($durHour * 3600) + ($durMinute * 60);
$endtime  = $starttime + $duration;

# Round up the duration to the next whole resolution unit.
# If they asked for 0 minutes, push that up to 1 resolution unit.
if (($tmp = $duration % $resolution) != 0 || $duration == 0)
    $endtime += $resolution - $tmp;

# Acquire mutex to lock out others trying to book the same slot.
if (!sql_mutex_lock('fac_reserve_entry'))
    fatal_error(1, $vocab['failed_to_acquire']);
	
# Check for schedule conflicts for the instrument we're trying to book
$conflicts = CheckFree($i, $starttime, $endtime-1, $book_id);

if(empty($conflicts)) {

    # Create the booking:
    $res = CreateEntry($starttime, $endtime, $book_type, $equip_id, $user_id, $description);
    if(!$res) {
        print_header();
        echo "<b>failed to book slot</b><br/>\n";
        print_footer();
        exit;
    }

    # Delete the original entry (if it was a modification)
    if(isset($booking))
        DelEntry($user, $booking);

    sql_mutex_unlock('fac_reserve_entry');
	
    # Now its all done; go back to the day view
    Header("Location: day.php?d=$d&c=$c&f=$f");
    exit;
}

# The instrument was available
sql_mutex_unlock('fac_reserve_entry');

if(count($conflicts)) {
    print_header();
	
    echo "<h1>" . $vocab["sched_conflict"] . "</h1>";
    echo $vocab["conflict"];
    echo "<ul>";
    foreach($conflicts as $entry) {
        echo "<li>" . $entry->PrintUser() . " " . $entry->PrintTime() . "</li>\n";
    }	
    echo "</ul>";

    $whereTo = $_COOKIE["WhereTo"];
    echo "<a href=\"$whereTo\">$vocab[returncal]</a><br/>";
	
    print_footer();
}

?>
