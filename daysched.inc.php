<?php
// $Id: daysched.inc.php,v 1.26 2004/01/23 22:26:14 sven Exp $
// Created by Sven Pedersen 2003/08/28 for RESERVE

// PHP schedule class for booking system 

class DaySchedule
{
    var $today;
    var $facility;
    var $user;
    var $curpage; # web page for link generation
    var $schedpage; # web page for link generation

    var $day_forward;
    var $day_backward;
    var $week_forward;
    var $week_backward;

    var $day_sec =   86400;
    var $week_sec = 604800;
    
    function DaySchedule($today, $facility, $user, $curpage, $schedpage)
    {
        $this->today  = $today;
	$this->facility = new ReserveFacility($facility);
        $this->user  = new ReserveUser($user);
        $this->curpage  = $curpage;
        $this->schedpage  = $schedpage;

        $this->day_forward  = $today + $this->day_sec;
        $this->day_backward = $today - $this->day_sec;
        $this->week_forward  = $today + $this->week_sec;
        $this->week_backward = $today - $this->week_sec;
    }
    
    function getLink($timestamp) {
       return $this->curpage . "?d=" . $timestamp;
    }

    
    function getSchedLink($timestamp, $instrument) {
       return $this->schedpage . "?d=" . $timestamp 
                     . "&f=" . $this->facility->fac_id . "&i=" . $instrument->equip_id;
    }

    function getBookLink($booking) {
      # make staff hours not clickable - fixme
      if($booking->book_type == "staffhours") {return "";}
      return "details.php?" 
             . "c=" . $this->facility->center_id . "&f=" . $this->facility->fac_id . "&i=" . $booking->equip_id . "&book_id=" . $booking->book_id;
    }

    function getHTML()
    {
        if($this->facility->num_instruments == 0) {
             return "<h1>This facility does not offer" 
                    .   " instrument scheduling.</h1>\n";
        }
	global $tab;

        $s = "";

        # get real equipment and facility managers
        #     then add managers as instruments
        $equipment = $this->facility->instruments;
        $managerequip = getManagersAsEquipment($this->facility->fac_id);
        foreach($managerequip as $elem){array_push($equipment, $elem);}
        $num_instruments = count($equipment);

        $s .= $tab[4] . "<div id=\"schedule\">\n"; 
	$s .= $tab[4] . "<table cellpadding=\"0\" cellspacing=\"0\">\n";
	$s .= $tab[5] . "<tr>\n";
	$s .= $tab[6] . "<td class=\"cTitle\" colspan=\"" . ($num_instruments + 1) . "\">\n";
	$s .= $tab[7] . "<h2><a href=\"" . $this->getLink($this->day_backward) . "\">&laquo;</a> ";
        $s .= date("l, F j, Y",$this->today); 
        $s .= " <a href=\"" . $this->getLink($this->day_forward) . "\">&raquo;</a></h2>\n";
	$s .= $tab[6] . "</td>\n";
	$s .= $tab[5] . "</tr>\n";
	$s .= $tab[5] . "<tr>\n";
	$s .= $tab[6] . "<td class=\"elbow\">&nbsp;</td>\n";

	## Column Headers - Equipment
	foreach($equipment as $equip){
	    $s .= $tab[6];
            $s .= "<td class=\"cCHead\"><a class=\"instHead\"";

            # managers have user name for equip_id, thus not numeric
            if(is_numeric($equip->equip_id)) {
                $s .= " href=\"#\" onclick=\"showChild('instrument.php?"
                   .  "c="  . $this->facility->center_id 
                   .  "&f=" . $this->facility->fac_id 
                   .  "&i=" . $equip->equip_id . "');\">";
            }
            else {
                $s .= ">"; 
            }
            $s .= $equip->name_short . "</a></td>\n";
	}

	$s .= $tab[5].      "</tr>\n";

	## Creates Table body

        $hours_start = $this->facility->hours_start;
        $hours_stop  = $this->facility->hours_stop;

        # get real bookings and manager schedules
        #    then add Manager schedules as bookings
        $bookings = getBookings($this->facility, $this->today+$hours_start, $this->today+$hours_stop);
        $managersched = getManagerSchedules($this->facility->fac_id, $this->today);
        foreach($managersched as $elem){array_push($bookings, $elem);}

        $bookings_num = count($bookings);
        $resolution = $this->facility->time_res;

        # display time increments up to penultimate segment of hour (if resolution < 60)
        $hour_secs = 3600;
        if($resolution < $hour_secs) {
	    $hours_stop = $hours_stop + (($hour_secs/$resolution) -1) * $resolution;
        }

	for($j = $hours_start; $j <= $hours_stop; $j += $resolution) {
	    if(($j % 3600) == 0) {
	        $labelclass="hour";
	        $cellclass="nrHour";
	    }
	    else {
                $labelclass="half";
                $cellclass="nrHalf";
	    }
	    $s .= $tab[5] . "<tr>\n";
	    $s .= $tab[6] . "<td class=\"$labelclass\">";
	    $actual_hour = ($j / 3600);
	    $reserve_time = $this->today + $j;
	    $fixedTime = date(hour_min_format(), ($this->today + $j));
	    if($labelclass == 'hour') {
	        $s .= "<nobr>" . $fixedTime . "</nobr>";
	    }
	    $s .= "</td>\n";

            # create rows of instrument cells
	    foreach($equipment as $equip){
		$s .= $tab[6];
                # for dates outside viewable range
                if(! $this->facility->checkViewDate($reserve_time)) {
                    $s .= "<td class=\"nonviewable\">&nbsp;</td>\n";
                    continue; # continue to next instrument
                }

                $cell = "";

                # check if any bookings include this cell
                $isBooked = False;
                foreach($bookings as $booking) {
                    if($booking->Contains($reserve_time) 
                       &&($equip->equip_id == $booking->equip_id)) {
                        $isBooked = True;
                    
                        # check if username and color code should be displayed
                        if($this->user->HasAccess($booking)) {
                            $cell .= "<td class=\"". $booking->book_type ."\">";
                            # check if booking can still be edited
                            $link = False;
                            if($booking->isEditable($this->user, $this->facility)) {
                                $cell .= "<a class=\"bookinfo\" href=\""
                                      .  $this->getBookLink($booking) ."\">";
                                $link = True;
                            }

                            # print username in first cell of booking
                            if($reserve_time - $booking->start_time < $resolution) {
                               $cell .= $booking->user_id;
                            }

                            # print start/stop time in second cell of booking
                            elseif(abs($reserve_time - ($booking->start_time + $resolution)) < $resolution) {
                                $cell .= "<nobr>".  $booking->PrintTime() ."</nobr><br/>";
                            }
                            else {
                                $cell .= "&nbsp;";
                            }

                            if($link) {
                                $cell .= "</a>";
                            }
                        }
                        # otherwise just gray out
		        else {
                            $cell .= "<td class=\"grayed\">";

                            # print start/stop time in first cell of booking
                            if($reserve_time - $booking->start_time  < $resolution) {
                                $cell .= "<nobr>". $booking->PrintTime() ."</nobr><br/>";
                            }
                            else {
                                $cell .= "&nbsp;";
                            }
                        }
                        $cell .= "</td>\n";
                    }
                }
                if(! $isBooked) {
                    $cell .= "<td class=\"$cellclass\">";
                    $cell .= "<a class=\"reserve\" href=\"". $this->getSchedLink($reserve_time, $equip) ."\">";
                    $cell .= "<span class=\"nrTime\">&nbsp;</span></a>";
                    $cell .= "<span class=\"noLink\">&nbsp;</span>";
                    $cell .= "</td>\n";
                }
                $s .= $cell;
            }
	    $s .= $tab[5] . "</tr>\n";
        }
        $s .= $tab[4] . "</table>\n";
        $s .= $tab[4] . "</div>\n";

        return $s;
    }
}

?>
