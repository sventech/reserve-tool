<?php
// $Id: reserve_sql.inc.php,v 1.5 2004/01/13 21:26:51 sven Exp $
// reserve_sql.inc

/** CheckFree()
 * 
 * Check to see if the time period specified is free
 * 
 * $equip_id  - Which piece of equipment are we checking
 * $starttime - The start of period
 * $endtime   - The end of the period
 * 
 * Returns:
 *   nothing   - The instrument is free
 *   something - An error occured, the return value is an array of bookings
 */
function CheckFree($equip_id, $starttime, $endtime, $ignore_id=0)
{
    global $vocab;
    $conflicts = array();

    # generate SQL to convert to Unix timestamps for comparison
    $start_time_db = sql_syntax_timestamp_to_unix( "start_time" );
    $end_time_db = sql_syntax_timestamp_to_unix( "end_time" );

    # Select any bookings which overlap ($starttime,$endtime) for this equipment:
    $sql = "SELECT * FROM fac_reserve_entry WHERE
            $start_time_db < $endtime AND $end_time_db > $starttime
            AND equip_id = $equip_id AND book_id <> $ignore_id ORDER BY start_time";

    #echo "<p>sql is:\n<br/>" . $sql . "</p>\n";

    $res = sql_query($sql);
    if(! $res) {
        die("sql error in conflict check: " . sql_error() . "<br/>\n");
        return array();
    }
    if (sql_count($res) == 0)
    {
        sql_free($res);
        return array();
    }
	
    # Build an array of all the conflicts:
    for ($i = 0; ($row = sql_row_keyed($res, $i)); $i++) {
        $conflicts[$i] = new ReserveBooking($row); 
    }
	
    return $conflicts;
}

/** DelEntry()
 * 
 * Delete an entry
 * 
 * $user (ReserveUser object)        - Who's making the request
 * $booking (ReserveBooking object)  - The entry to delete
 *
 * Returns:
 *   False - An error occured
 *   True  - The entry was deleted
 */
function DelEntry($user, $booking)
{
    $removed = False;
    $sql = "DELETE FROM fac_reserve_entry 
            WHERE book_id = ". $booking->book_id;
    echo "<p>sql is:\n<br/>" . $sql . "</p>\n";
    if($user->HasAccess($booking)) {
        if (sql_command($sql) > 0) {
            $removed = True;
            return $removed;
        }
    }	
    echo "<p>sql error in conflict check: " . sql_error() . "</p>\n";
    return $removed;
}

/** CreateEntry()
 * 
 * $starttime   - Start time of entry
 * $endtime     - End time of entry
 * $book_type   - Entry type
 * $book_id     - Booking ID
*  $equip_id    - Instrument
 * $user_id     - Creator/Owner
 * $description - Description
 * 
 * Returns:
 *   0        - An error occured while inserting the entry
 *   non-zero - The entry's ID
 */
function CreateEntry($starttime, $endtime, $book_type, $equip_id, $user_id, $description)
{
    $starttime = sql_syntax_timestamp_from_unix( $starttime );
    $endtime = sql_syntax_timestamp_from_unix( $endtime );
    $description = slashes($description);
	
    $sql = "INSERT INTO fac_reserve_entry 
                     ( start_time, end_time, book_type, equip_id,
	               created_by,  user_id,   description)
              VALUES ( $starttime, $endtime, '$book_type', $equip_id,
	               '$user_id', '$user_id', '$description')";
	
    if (sql_command($sql) < 0) {
        echo "<p>sql is:\n<br/>" . $sql . "</p>\n";
        echo "sql error in creation: " . sql_error() . "<br/>\n";
        return 0;
    }
    return sql_insert_id("fac_reserve_entry", "book_id");
}

/* GetEntryInfo()
 *
 * Get the booking's entrys
 * 
 * $book_id = The booking ID for which to get the info for.
 * 
 * Returns:
 *    nothing                = The ID does not exist
 *    ReserveBooking object  = The bookings info
 */
function GetEntryInfo($book_id)
{
    $sql = "SELECT * FROM fac_reserve_entry 
            WHERE book_id = $book_id";
	
    $res = sql_query($sql);
    if(! $res) {
        die("sql error in get booking info: " . sql_error() . "<br/>\n");
        return array();
    }
	
    $row = sql_row_keyed($res, 0);
    if(!empty($row)) {
        $booking = new ReserveBooking($row);
    }
    else {
        return;
    }
	
    return $booking;
}

?>
