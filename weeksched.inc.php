<?php
// $Id: weeksched.inc.php,v 1.20 2004/01/23 18:13:23 sven Exp $
// Created by Sven Pedersen 2003/08/28 for RESERVE

// PHP schedule class for booking system 

class WeekSchedule
{
    var $today;
    var $facility;
    var $instrument;
    var $user;
    var $curpage; # web page for link generation
    var $schedpage; # web page for link generation

    var $week_forward;
    var $week_backward;

    var $day_sec =   86400;
    var $week_sec = 604800;
    
    function WeekSchedule($today, $instrument, $user, $curpage, $schedpage)
    {
        $this->today  = $today;
        $this->instrument = new ReserveInstrument($instrument);
	$this->facility = new ReserveFacility($this->instrument->fac_id);
        $this->user  = new ReserveUser($user);
        $this->curpage  = $curpage;
        $this->schedpage  = $schedpage;

        $this->week_forward  = $today + $this->week_sec;
        $this->week_backward = $today - $this->week_sec;
    }
    
    function getLink($timestamp) {
       return $this->curpage . "?d=" . $timestamp;
    }

    
    function getSchedLink($timestamp) {
       return $this->schedpage . "?d=" . $timestamp 
                     . "&f=" . $this->facility->fac_id . "&i=" . $this->instrument->equip_id;
    }

    function getBookLink($booking) {
      # make staff hours not clickable - fixme
      if($booking->book_type == "staffhours") {return "";}
      return "details.php?"
             . "c=" . $this->facility->center_id . "&f=" . $this->facility->fac_id . "&i="
. $booking->equip_id . "&book_id=" . $booking->book_id;
    }


    function getHTML()
    {

        if($this->facility->num_instruments == 0) {
             
             return "<h1>This facility does not offer instrument scheduling.</h1>\n";
        }
	global $tab;

        $s = "";
        
        $s .= $tab[4] . "<div id=\"schedule\">\n"; 
	$s .= $tab[4] . "<table cellpadding=\"0\" cellspacing=\"0\">\n";
	$s .= $tab[5] . "<tr>\n";
	$s .= $tab[6] . "<td class=\"cTitle\" colspan=\"8\">\n";
	$s .= $tab[7] . "<h2><a href=\"" . $this->getLink($this->week_backward) . "\">&laquo;</a> ";
        $s .= "Week of ". date("l, F j, Y",$this->today); 
        $s .= " <a href=\"" . $this->getLink($this->week_forward) . "\">&raquo;</a></h2>\n";
	$s .= $tab[6] . "</td>\n";
	$s .= $tab[5] . "</tr>\n";
	$s .= $tab[5] . "<tr>\n";
	$s .= $tab[6] . "<td class=\"elbow\">&nbsp;</td>\n";

	## Column Headers - days

        global $weekstarts;
        $sec_in_day = $this->day_sec;
        $day_of_week = strftime('%w', $this->today);
        # find the first day, Sunday or Monday, of this week:
        $firstday = $this->today - ($sec_in_day * $day_of_week - $sec_in_day * $weekstarts);
        $lastday = $firstday + (6 * $sec_in_day);
        $days = array();

        $cen = $this->facility->center_id;
        $fac = $this->facility->fac_id;
        for ($i = 0; $i < 7; $i++)
        {
           $show = $firstday + ($i * $sec_in_day);
           $days[$i] = $show;

           # get abbreviated day name for locale
           $dayName = strftime('%a <nobr>%b %d</nobr>',$show);

           $link = "day.php?d=$show&c=$cen&f=$fac";
           $s .= $tab[6];
           $s .= "<td class=\"cCHead\"><a href=$link>$dayName</a></td>\n";
        }

	$s .= $tab[5].      "</tr>\n";

	## Creates Table body

        $hours_start = $this->facility->hours_start;
        $hours_stop  = $this->facility->hours_stop;

        $bookings = getBookingsInstrument($this->instrument->equip_id, $firstday+$hours_start, $lastday+$hours_stop);
        $resolution = $this->facility->time_res;

        # display time increments up to penultimate segment of hour (if resolution < 30)
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
	    $fixedTime = date(hour_min_format(), ($this->today + $j));
	    if($labelclass == 'hour') {
	        $s .= "<nobr>" . $fixedTime . "</nobr>";
	    }
	    $s .= "</td>\n";

            # create rows of day/hour cells
	    foreach($days as $day){
	        $reserve_time = $day + $j;
		$s .= $tab[6];
                # for dates outside viewable range
                if(! $this->facility->checkViewDate($reserve_time)) {
                    $s .= "<td class=\"nonviewable\">&nbsp;</td>\n";
                    continue; # continue to next day
                }
                $cell = "";

                # check if any bookings include this cell
                $isBooked = False;
                foreach($bookings as $booking) {
                    if($booking->Contains($reserve_time)) {
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
                                $cell .= "<nobr>". $booking->PrintTime() ."</nobr><br/>";
                            } 
                            else {
                                $cell .= "&nbsp;"; 
                            }
                            if($link) {
                                $cell .= "</a>";
                            }
                        }
		        else {
                            $cell .= "<td class=\"grayed\">";

                            # print start/stop time in second cell of booking
                            if($reserve_time - $booking->start_time < $resolution) {
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
                    $cell .= "<a class=\"reserve\" href=\""
                          . $this->getSchedLink($reserve_time) ."\">";
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
