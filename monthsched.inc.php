<?php
// $Id: monthsched.inc.php,v 1.10 2004/01/23 21:06:19 sven Exp $
// Modified by Sven Pedersen 2003/09/25 for RESERVE

// PHP Calendar Class
//  
// Copyright David Wilkinson 2000. All Rights reserved.
// 
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head 
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly 
// from the use of this script.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk

class MonthSchedule
{
    var $today;
    var $month;
    var $year;
    var $day;
    var $h;    # highlight day?
    var $curpage; # web page for link generation
    var $schedpage; # web page for link generation
    var $instrument;
    var $facility;
    var $user;
    
    function MonthSchedule($today, $instrument, $user, $curpage, $schedpage, $h)
    {
         $time = getdate($today);
         $this->today = $today;
         $this->day   = $time["mday"];
         $this->month = $time["mon"];
         $this->year  = $time["year"];
         $this->instrument = new ReserveInstrument($instrument);
         $this->facility = new ReserveFacility($this->instrument->fac_id);
         $this->user     = new ReserveUser($user);
         $this->curpage  = $curpage;
         $this->schedpage  = $schedpage;
         $this->h     = $h;
    }
    
    function getCalendarLink($month, $year)
    {
         return $this->curpage . "?d=" . mktime(0, 0, 0, $month, 1, $year);
    }
    
    function getDateLink($day, $month, $year) {
         return $this->schedpage . "?d=" . mktime(0, 0, 0, $month, $day, $year);
    }
    

    function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
    
        $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
   
        $d = $days[$month - 1];
   
        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...
        
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }
    
        return $d;
    }

    function getFirstDays()
    {
      global $weekstarts;
      global $tab;

      $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
      for ($i = 0, $s = ""; $i < 7; $i++)
      {
         $show = $basetime + ($i * 86400);

         # get abbreviated day name for locale
         $dayName = strftime('%a',$show);

    	 $s .= $tab[6];       
         $s .= "<td class=\"cCHead\">$dayName</td>\n";
      }
      return $s;
    }

    function getHTML()
    {
        if($this->instrument->schedule == 0) {
             return "<div id=\"schedule\"><h1>This instrument is not available for scheduling.</h1></div>\n";
        }
        global $weekstarts;
        global $tab;


        if (!isset($weekstarts)) $weekstarts = 0;
        $s = "";
        
    	$daysInMonth = $this->getDaysInMonth($this->month, $this->year);
    	$date = mktime(12, 0, 0, $this->month, 1, $this->year);
    	
    	$first = (strftime("%w",$date) + 7 - $weekstarts) % 7;
    	$monthName = strftime("%B",$date);
    	
    	$prevMonth = $this->getCalendarLink($this->month - 1 >   0 ? $this->month - 1 : 12, $this->month - 1 >   0 ? $this->year : $this->year - 1);
    	$nextMonth = $this->getCalendarLink($this->month + 1 <= 12 ? $this->month + 1 :  1, $this->month + 1 <= 12 ? $this->year : $this->year + 1);
    	
    	$s .= $tab[3];
    	$s .= "<div id=\"schedule\">\n";
    	$s .= $tab[4];
    	$s .= "<table cellspacing=\"2\" cellpadding=\"0\">\n";
    	$s .= $tab[5];
    	$s .= "<tr>\n";
    	$s .= $tab[6];
    	$s .= "<td class=\"cTitle\" colspan=\"7\"><h2><a href=\"" . $prevMonth . "\">&laquo;</a> ";
        $s .= "$monthName $this->year";
        $s .= " <a href=\"" . $nextMonth . "\">&raquo;</a></h2></td>\n"; 
    	$s .= $tab[5];
    	$s .= "</tr>\n";
    	
    	$s .= $tab[5];
    	$s .= "<tr>\n";
        $s .= $this->getFirstDays();
    	$s .= $tab[5];
    	$s .= "</tr>\n";

        ## Creates Table body

        $day_sec =   86400;

        $hours_start = mktime(0, 0, 0, $this->month, 1, $this->year);
        $hours_stop = mktime(23, 59, 59, $this->month, $daysInMonth, $this->year);
        $base_date = $hours_start;

        $bookings = getBookingsInstrument($this->instrument->equip_id, $hours_start, $hours_stop);
        $bookings_num = count($bookings);

    	$d = 1 - $first;
    	   	
        $reserve_time = $date;
    	while ($d <= $daysInMonth)
    	{
    	    $s .= $tab[5];
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
    	        $s .= $tab[6];
    	        $real_day = ($d > 0 && $d <= $daysInMonth);
                $viewable = $this->facility->checkViewDate($reserve_time);
                if(!$viewable && $real_day) {
    	            $s .= "<td class=\"mDayNonView\" valign=\"top\">";       
                }
                else {
    	            $s .= "<td class=\"mDay\" valign=\"top\">";       
                }
    	        if ($real_day)
    	        {
                    $link = $this->schedpage . "?d=" . $reserve_time;
                    $daycode = "";
                    if (($d == $this->day) and ($this->h))
                        $daycode = "<span class=\"mNum\"><span class=\"curDay\">$d</span></span>";
                    else
                        $daycode = "<span class=\"mNum\">$d</span>";

                    if(!$viewable) {
                        $s .= $daycode;
                        $reserve_time += $day_sec;
      	                $s .= "</td>\n";       
        	        $d++;
                        continue;
                    }
                    $s .= "<a class=\"reserve\" href=\"$link\">";
                    $s .= $daycode;

                    # check if there are any bookings for this day
                    foreach($bookings as $booking) {
                        if($booking->SameDay($reserve_time, $this->facility)) {
                            $s .= "<nobr>". $booking->PrintTime() ."</nobr><br/>";
                        }
                    }
                    $s .= "</a>";
                    $reserve_time += $day_sec;
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";       
        	$d++;
    	    }
    	    $s .= $tab[5];
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= $tab[4];
    	$s .= "</table>\n";
    	$s .= $tab[3];
    	$s .= "</div>\n";
    	
    	return $s;
    }
}

?>
