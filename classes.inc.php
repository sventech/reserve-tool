<?php
// $Id: classes.inc.php,v 1.20 2004/01/23 23:32:42 sven Exp $

# Classes

class ReserveBooking
{
    var $book_id;
    var $start_time;
    var $end_time;
    var $book_type;
    var $timestamp;
    var $created_by;
    var $description;
    var $equip_id;
    var $user_id;

    function ReserveBooking($db_booking)
    {
        $this->book_id    = $db_booking["book_id"];
        # accept both real database entries and manual entries
        if(is_numeric($db_booking["start_time"]) && is_numeric($db_booking["end_time"])) {
            $this->start_time = $db_booking["start_time"];
            $this->end_time   = $db_booking["end_time"];
        }
        else {
            $this->start_time = strtotime($db_booking["start_time"]);
            $this->end_time   = strtotime($db_booking["end_time"]);
        }
        $this->book_type  = $db_booking["book_type"];
        $this->timestamp  = $db_booking["timestamp"];
        $this->created_by = $db_booking["created_by"];
        $this->description= $db_booking["description"];
        $this->equip_id   = $db_booking["equip_id"];
        $this->user_id    = $db_booking["user_id"];
    }

    function Contains($time)
    {
        if($time >= $this->start_time && $time < $this->end_time) {
            return True;
        }
        else {
            return False;
        }
    }

    function SameDay($time, $facility)
    {
        # get day portion of time stamp (YYYYMMDD)
        $day = date("Ymd",$time);
        $start_day = date("Ymd",$this->start_time);
        $end_day = date("Ymd",$this->end_time);

        # get time portion of booking timestamp (HHMM)
        $book_start = date("Gi", $this->start_time);
        $book_end = date("Gi", $this->end_time);

        # facility start/stop times 
        #    seconds -> HHMM [ / (60*60) * 100 ]
        $sched_start = $facility->hours_start / 36;
        $sched_end   = $facility->hours_stop  / 36;
        #echo "<!-- book_start:$book_start book_end:$book_end sched_start:$sched_start -->\n";
        
        if($day == $start_day) {
            if($book_start >= $sched_start) { 
                return True;
            }
        }
        elseif($day == $end_day) {
            if($book_end > $sched_start) { 
                return True;
            }
        }
        else {
            return False;
        }
    }

    function PrintTime() {
        return date("G:i",$this->start_time)
                   . "-" . 
               date("G:i", $this->end_time);
    }

    function PrintUser() {
        return $this->user_id;
    }
    
    # time windows for user editing
    function isEditable($user, $facility) {
        # it is assumed that the user has
        #    ownership or is an admin

        # modification end - until how long before can you edit it
        $mod_end = $facility->book_end * 60; # min->hr
        $orig_time = $this->start_time;
        $herenow = time();
        # future - present < minimum time window
        if( ($orig_time - $herenow) > $mod_end) {
            return True;
        }
        else return False;
    }
    
    function getDuration() {
        return $this->end_time - $this->start_time;
    }
    
    function getDurationHours() {
        $dur = ($this->end_time - $this->start_time)/60;
        return (int) ($dur/60);
    }
    
    function getDurationMinutes() {
        $dur = ($this->end_time - $this->start_time)/60;
        return $dur % 60;
    }
}

class ReserveUser
{
    var $user_id;
    var $admin;

    function ReserveUser($user_id)
    {
        $this->user_id = $user_id;

        # check for admin status
        # note: we could just add "AND fac_id LIKE '$fac_id' for better granularity of perms

        $qry_admin = "SELECT COUNT(*) FROM fac_staff WHERE user_id LIKE '$user_id'"; 
        $is_admin = sql_query1($qry_admin);
        if($is_admin == -1) {$is_admin = 0;}
        if($is_admin) {
            $this->admin = True;
        }
    }
    function HasAccess($booking)
    {
        if( ($booking->user_id == $this->user_id) || $this->admin) {
            return True;
        }
        else return False;
    }
    function isAdmin()
    {
        if($this->admin) 
            return True;
        else
            return False;
    }
}

class ReserveInstrument
{
    var $equip_id;
    var $fac_id;
    var $center_id;
    var $name_short;
    var $name_long;
    var $desc_short;
    var $desc_long;
    var $location;
    var $schedule;
    var $view_order;

    function ReserveInstrument($equip_id)
    {
        $qry_equip = "SELECT * FROM fac_equipment WHERE equip_id LIKE '$equip_id'";
        $rslt_equip = sql_query($qry_equip);
        if($rslt_equip) {
            for($i=0; ($row_e = sql_row_keyed($rslt_equip,$i)); $i++) {
                $this->equip_id  = $row_e["equip_id"];
                $this->fac_id    = $row_e["fac_id"];
                $this->center_id = $row_e["center_id"];
                $this->name_short = $row_e["name_short"];
                $this->name_long =  $row_e["name_long"];
                $this->desc_short = $row_e["desc_short"];
                $this->desc_long =  $row_e["desc_long"];
                $this->location = $row_e["location"];
                $this->schedule = $row_e["schedule"];
                $this->view_order = $row_e["view_order"];
            }
        }
    }
}

class ReserveFacility
{
    var $fac_id;
    var $center_id;
    var $name_short;
    var $name_long;
    var $desc_short;
    var $desc_long;
    var $advisor;
    var $location;
    var $contact_info;
    var $rules;

    var $instruments;
    var $num_instruments;

    var $hours_start;
    var $hours_stop;
    var $time_res;
    var $book_begin;
    var $book_end;
    var $view_future;
    var $view_past;

    function ReserveFacility($fac_id)
    {
        $this->fac_id = $fac_id;

        # Query Information about Facility
        $qry_info = "SELECT * FROM fac_name WHERE fac_id like '$fac_id'";
        $rslt_info = sql_query($qry_info);
        if($rslt_info) {
            for($i=0; ($row = sql_row_keyed($rslt_info,$i)); $i++) {
                $this->center_id     = $row["center_id"];
                $this->name_short    = $row["name_short"];
		$this->name_long     = $row["name_long"];
		$this->desc_short    = $row["desc_short"];
		$this->desc_long     = $row["desc_long"];
		$this->advisor       = $row["advisor"];
		$this->location      = $row["location"];
		$this->contact_info  = $row["contact_info"];
		$this->rules         = $row["rules"];
	    }
	}
        else {
            $error = sql_error();
            echo "<br>\n\nerror: $error\n\nINFO_CRF<br>\n\n";
        }

        # Query Configuration for given Facility
        $qry_conf = "SELECT * from fac_reserve_config"; 
        $rslt_conf = sql_query($qry_conf);
        if($rslt_conf) {
            for($i=0; ($row = sql_row_keyed($rslt_conf,$i)); $i++) {
                $this->hours_start = 60 * 60 * $row["hours_start"];
                $this->hours_stop  = 60 * 60 * $row["hours_stop"];
                $this->time_res    = 60 * $row["time_res"]; #min->sec
                $this->book_begin  = $row["book_begin"];
                $this->book_end    = $row["book_end"];
                $this->view_future = $row["view_future"];
                $this->view_past   = $row["view_past"];
            }
        }
        else {
            $error = sql_error();
            echo "<br>\n\nerror: $error\n\nCONF_CRF<br>\n\n";
        }

        # Query Instruments from given Facility
        $qry_equip = "SELECT equip_id FROM fac_equipment WHERE fac_id LIKE '$fac_id' AND schedule LIKE '1'";
        $rslt_equip = sql_query($qry_equip);
        if($rslt_equip) {
            $this->instruments = array();
            for($i=0; ($row_e = sql_row_keyed($rslt_equip,$i)); $i++) {
                $this->instruments[$i] = new ReserveInstrument($row_e["equip_id"]);
                $this->num_instruments++;
            }
        }
        else {
            $error = sql_error();
            echo "<br>\n\nerror: $error<br>\n\nINSTR_CRF<br>\n\n";
        }
    }

    function checkViewDate($timestamp)
    {
        # is the date outside the allowable range?

        $now = time();    # 86,400 seconds in a day
        $now = $now - ($now % 86400) + 14460; # correct to 0:01
        $future = $now + ($this->view_future * 86400); 
        $past = $now - ($this->view_past * 86400); 
        if($timestamp > $future) {
            return False;
        }
        elseif($timestamp < $past) {
            return False;
        }
        else return True;
    }
}

class ReserveCenter
{
    var $center_id;
    var $name_short;
    var $name_long;
    var $desc_short;
    var $desc_long;
    var $location;
    var $contact_info;
    var $director;

    function ReserveCenter($center_id)
    {
        # Query Information about Center
        $qry_info = "SELECT * FROM center_name WHERE center_id like '$center_id'";
        $rslt_info = sql_query($qry_info);
        if($rslt_info) {
            for($i=0; ($row = sql_row_keyed($rslt_info,$i)); $i++) {
                $this->center_id		= $row["center_id"];
                $this->name_short		= $row["name_short"];
		$this->name_long		= $row["name_long"];
		$this->desc_short		= $row["desc_short"];
		$this->desc_long		= $row["desc_long"];
		$this->location			= $row["location"];
		$this->contact_info	        = $row["contact_info"];
		$this->director			= $row["director"];
	    }
	}
        else {
            $error = sql_error();
            echo "<br>\n\nerror: $error\n\nINFO_CRC<br>\n\n";
        }
    }

}

?>
