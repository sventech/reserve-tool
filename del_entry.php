<?php
// $Id: del_entry.php,v 1.1 2004/01/13 21:25:17 sven Exp $

include_once("functions.inc.php");
check_auth();

include("config.inc.php");
include("$dbsys.inc.php");
include("reserve_sql.inc.php");

# make sure we got all 
if(isset($_GET["d"], $_GET["c"], $_GET["f"], $_GET["i"]))
{
    $d            = $_GET["d"];
    $c            = $_GET["c"];
    $f            = $_GET["f"];
    $i            = $_GET["i"];
}
else {
    # we're in deep tapioca
    print_header();
    echo "<b>one of the variables was not set!!!</b>\n";
    print_footer();
    exit;
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

# make sure the user has permission to edit the booking
if(isset($_GET["book_id"])) {
    $book_id = $_GET["book_id"];
    $booking = GetEntryInfo($book_id);

    if(!$user->HasAccess($booking)) {
        showAccessDenied("You are not authorized to delete that booking");
        exit;
    }
}
else {
    # we're in deep tapioca
    print_header();
    echo "<b>no booking ID was set!!!</b>\n";
    print_footer();
    exit;
}

# Acquire mutex to lock out others trying to modify the same slot.
if (!sql_mutex_lock('fac_reserve_entry'))
    fatal_error(1, $vocab['failed_to_acquire']);
	
# Delete the entry
DelEntry($user, $booking) || fatal_error(1,"failed to delete booking $book_id\n");

sql_mutex_unlock('fac_reserve_entry');
	
# Now its all done; go back to the day view
Header("Location: day.php?d=$d&c=$c&f=$f");
exit;

?>
