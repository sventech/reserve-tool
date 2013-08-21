<?php
// $Id: mincals.inc,v 1.4 2003/10/16 13:43:09 dad46 Exp $
// Modified by Sven Pedersen 2003/08/22 for RESERVE

function minicals($year, $month, $day, $page) {

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

class Calendar
{
    var $month;
    var $year;
    var $day;
    var $h;    # highlight day?
    var $page; # web page for link generation
    
    function Calendar($day, $month, $year, $h, $page)
    {
        $this->day   = $day;
        $this->month = $month;
        $this->year  = $year;
        $this->h     = $h;
        $this->page  = $page;
    }
   
    
    function getCalendarLink($month, $year)
    {
        return "";
    }
    
   function getDateLink($day, $month, $year) {
      #return $this->dmy.".php?year=$year&month=$month&day=$day&area=".$this->area;
      #return "blah".".php?year=$year&month=$month&day=$day";
      return $this->page . "?d=" . mktime(0, 0, 0, $month, $day, $year);
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
         $show = $basetime + ($i * 24 * 60 * 60);

         # get abbreviated day name for locale
         $dayName = strftime('%a',$show);

         # get first character of day name
         $dayName = substr($dayName, 0, 1);

    	 $s .= $tab[7];       
         $s .= "<td class=\"mcDay\">$dayName</td>\n";
      }
      return $s;
    }

    function getHTML()
    {
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
    	
    	$s .= $tab[4];
    	$s .= "<div class=\"miniCal\">\n";
    	$s .= $tab[5];
    	$s .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
    	$s .= $tab[6];
    	$s .= "<tr>\n";
    	$s .= $tab[7];
    	$s .= "<td colspan=\"7\"><h2><a href=\"month.php?d=".mktime(0, 0, 0, $this->month, $this->day, $this->year)."\">$monthName $this->year</a></h2></td>\n"; 
    	$s .= $tab[6];
    	$s .= "</tr>\n";
    	
    	$s .= $tab[6];
    	$s .= "<tr>\n";
        $s .= $this->getFirstDays();
    	$s .= $tab[6];
    	$s .= "</tr>\n";
    	
    	$d = 1 - $first;
    	   	
    	while ($d <= $daysInMonth)
    	{
    	    $s .= $tab[6];
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
    	        $s .= $tab[7];
    	        $s .= "<td class=\"miniCalDay\">";       
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
    	            $link = $this->getDateLink($d, $this->month, $this->year);
                    if ($link == "")
                        $s .= "<div class=\"dayCell\">$d</div>";
                    elseif (($d == $this->day) and ($this->h))
                        $s .= "<a href=\"$link\"><span class=\"curDay\">$d</span></a>";
                    else
                        $s .= "<a href=\"$link\">$d</a>";
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";       
        	    $d++;
    	    }
    	    $s .= $tab[6];
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= $tab[5];
    	$s .= "</table>\n";
    	$s .= $tab[4];
    	$s .= "</div>\n";
    	
    	return $s;
    }
}

$lastmonth = mktime(0, 0, 0, $month-1, 1, $year);
$thismonth = mktime(0, 0, 0, $month,   $day, $year);
$nextmonth = mktime(0, 0, 0, $month+1, 1, $year);

$cal = new Calendar(date("d",$lastmonth), date("m",$lastmonth), date("Y",$lastmonth), 0, $page);
echo $cal->getHTML();

$cal = new Calendar(date("d",$thismonth), date("m",$thismonth), date("Y",$thismonth), 1, $page);
echo $cal->getHTML();

$cal = new Calendar(date("d",$nextmonth), date("m",$nextmonth), date("Y",$nextmonth), 0, $page);
echo $cal->getHTML();
}
?>
