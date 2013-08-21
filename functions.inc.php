 <?php

# $Id: functions.inc.php,v 1.88 2006/03/02 19:05:46 dwb7 Exp $

function check_auth()
{
    global $netID_secure, $showLogin, $reserve_authed, $authobject, $mustChngPW;

    $showLogin = False;
    if(!isset($_SERVER["HTTP_REMOTE_USER"])){
        #require("/restricted/krb_auth.php");
        #require("/webauth/unix_auth.php");
        #$netID_secure = $unixauthed;
    }
    else {
        $netID_secure=$_SERVER["HTTP_REMOTE_USER"];
    }

    # better variable for CCMR authentication object
    $authobject = $a;

    # This allows later use of other authentication methods
    if($netID_secure == 'failure') {
        $reserve_authed = False;
    }
    else $reserve_authed = $netID_secure;
}

function print_login_status()
{
   global $reserve_authed;
   global $tab;

   echo $tab[3];
   echo "<div id=\"logon\">\n";

   if($reserve_authed){
       echo $tab[4];
       echo "You are logged in as: <strong>".$reserve_authed."</strong> <a href=\"logout.php\">Log out</a>\n";
   }
   else {
       echo $tab[4];
       echo "You are not logged in. <a href=\"login.php\">Log in</a>\n";
   }

   echo $tab[3];
   echo "</div>\n";
}

function print_header()
{
    global $vocab, $reserve_company;
    global $MONTH_VIEW, $WEEK_VIEW, $DAY_VIEW, $INDEX_VIEW;
    global $cellWidth, $reserve_authed;
    # center, facility, instrument
    global $c, $f, $i;

    if(isset($_GET["c"])){$c =$_GET["c"];}else{$c = get_default_center();}
    if(isset($_GET["f"])){$f =$_GET["f"];}else{$f = get_default_facility($c);}
    if(isset($_GET["i"])){$i =$_GET["i"];}else{$i = get_default_instrument($f);}
    if(isset($_GET["d"])){$d = $_GET["d"];}else{$d = time();}
    
    if($DAY_VIEW) {
        $equip_num = getNumInstruments($f);
        $cellWidth = 85/($equip_num+1);
    }
    else {
        $cellWidth = 85/(8+1);
    }

    echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Reserve - Scientific Instrument Scheduling System</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
		<link rel="stylesheet" href="reserve.css" media="screen" type="text/css"/>
		<link rel="stylesheet" href="reserve.css" media="print" type="text/css"/>
		<script language="JavaScript1.2" type="text/javascript">
			<?php echo "var current_date = " . $d * 1000 . ";\n";?>
		</script>
		<script language="JavaScript1.2" type="text/javascript" src="reserve.js"></script>

		<style media="all" type="text/css">
    	        	.nrHour,.nrHalf,.grayed,.normal,.maintenance,.training,.staffappt,.staffhours,.tentative{width: <? echo $cellWidth; ?>%;}
<?php if( $reserve_authed || $MONTH_VIEW ) { ?>
                	.noLink{display:none;}
<?php }else{ ?>
                	a.reserve{display:none;}
<?php } ?>
		</style>

	</head>
	<body>

	<div id="container">
	    
		<div id="pageIdentifier">
        		<div id="pageTitle">
				<div id="logo">
					<a href="http://www.mycompany.com"><img src="images/logo_65x65.gif" width="65" height="65" border="0" alt="My Company"/></a>
				</div>
				<div id="clockLogo">
					<a href="index.php"><img src="images/clock_logo.gif" width="57" height="56" border="0" alt="Clock Logo"/></a>
				</div>
				<h1><a href="index.php">Reserve</a></h1>
				<p>Tool Scheduling System</p>
			</div>
		</div>
    
		<div id="mainMenu">
		
<?
print_login_status();
?>
			
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="">Help</a></li>
				<li><a href="">Report</a></li>
				<li><a href="">Search</a></li>
			</ul>
			
		</div>
		
<?php
if(!$INDEX_VIEW) {
    global $tab;
    echo "$tab[3]<div id=\"currentLocation\">\n";
    $top_center = new ReserveCenter($c);
    $top_facility = new ReserveFacility($f);
    $sep = "<span>::</span>";
    echo "$tab[3]<h1>";
    if(!empty($c)) {
        echo "<acronym title=\"" . $top_center->name_long . "\">";
        echo $top_center->name_short;
        echo "</acronym> ";
        if(!empty($f)) {
            echo "$sep $top_facility->name_long ";
            if(!empty($i) && !$DAY_VIEW) {
                echo "$sep " . $top_facility->instruments[$i-1]->name_short;
            }
        }
    }
    echo "</h1>\n";
    echo "$tab[3]</div>";
}
?>

<?php
}

function print_footer()
{
?>
    <div id="footer">
        <span>&copy; 2003 Cornell Center for Materials Research | <a href="mailto:admin@mycompany.com">admin@mycompany.com</a></span>
    </div>
    </div><!-- end container -->
    </body>
</html>

<?php
}

# Error handler - this is used to display serious errors such as database
# errors without sending incomplete HTML pages. This is only used for
# errors which "should never happen", not those caused by bad inputs.
# If $need_header!=0 output the top of the page too, else assume the
# caller did that. Alway outputs the bottom of the page and exits.
function fatal_error($need_header, $message)
{
	global $vocab;
	if ($need_header) print_header();
	echo $message;
	if ($need_header) print_footer();
	exit;
}

# Apply backslash-escape quoting unless PHP is configured to do it
# automatically. Use this for GET/POST form parameters, since we
# cannot predict if the PHP configuration file has magic_quotes_gpc on.
function slashes($s)
{
	if (get_magic_quotes_gpc()) return $s;
	else return addslashes($s);
}

# Remove backslash-escape quoting if PHP is configured to do it with
# magic_quotes_gpc. Use this whenever you need the actual value of a GET/POST
# form parameter (which might have special characters) regardless of PHP's
# magic_quotes_gpc setting.
function unslashes($s)
{
	if (get_magic_quotes_gpc()) return stripslashes($s);
	else return $s;
}

# Return a default center; used if no center is already known. This returns the
# lowest center ID in the database (no guaranty there is a 1).
function get_default_center()
{
	$center = sql_query1("SELECT MIN(center_id) FROM center_name");
	return ($center < 0 ? 0 : $center);
}

# Return a default facility; used if no facility is already known. This returns the
# lowest facility ID in the database (no guaranty there is a 1).
function get_default_facility($center)
{
	$facility = sql_query1("SELECT MIN(fac_id) FROM fac_name WHERE center_id LIKE $center");
	return ($facility < 0 ? 0 : $facility);
}

# Return a default instrument; used if no instrument is already known. This returns the
# lowest equipment ID in the database (no guaranty there is a 1).
function get_default_instrument($facility)
{
	$instrument = sql_query1("SELECT MIN(equip_id) FROM fac_equipment WHERE fac_id LIKE $facility");
	return ($instrument < 0 ? 0 : $instrument);
}

# Get the local day name based on language. Note 2000-01-02 is a Sunday.
function day_name($daynumber)
{
	return strftime("%A", mktime(0,0,0,1,2+$daynumber,2000));
}

function hour_min_format()
{
        global $twentyfourhour_format;
        if ($twentyfourhour_format)
	{
  	        return "G:i";
	}
	else
	{
		return "g:i a";
	}
}

function time_date_string($t)
{
    global $twentyfourhour_format;
    # This bit's necessary, because it seems %p in strftime format
    # strings doesn't work
    $ampm = date("a",$t);
    if ($twentyfourhour_format) {
        return strftime("%H:%M:%S - %A %d %B %Y",$t);
    }
    else {
        return strftime("%I:%M:%S$ampm - %A %d %B %Y",$t);
    }
}

# Round time down to the nearest resolution
function round_t_down($t, $resolution, $am7)
{
    return (int)$t - (int)abs(((int)$t-(int)$am7)
				  % $resolution); 
}

# Round time up to the nearest resolution
function round_t_up($t, $resolution, $am7)
{
    if (($t-$am7) % $resolution != 0) {
        return $t + $resolution - abs(((int)$t-(int)$am7) 
                                   % $resolution);
    }
    else {
        return $t;
    } 
}

function getDaysInMonth($month, $year)
// from Calendar class
// Copyright David Wilkinson 2000. All Rights reserved.
{
    if ($month < 1 || $month > 12) {
        return 0;
    }

    $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $d = $days[$month - 1];

    if ($month == 2) {
        // Check for leap year
        // Forget the 4000 rule, I doubt I'll be around then...

        if ($year % 4 == 0) {
            if ($year % 100 == 0) {
                if ($year % 400 == 0) {
                    $d = 29;
                }
            }
            else {
                $d = 29;
            }
        }
    }
    return $d;
}

function genSearchDateSelector($page, $t_stamp, $center, $facility)
{
    global $tab;
    $day = date("d", $t_stamp);
    $month = date("m", $t_stamp);
    $year = date("Y", $t_stamp);
       	
    echo $tab[3];
    echo "<form name=\"dateForm\" action=\"" . $page ."\" method=\"get\">\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"d\" value=\"-1\"/>\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"c\" value=\"$center\"/>\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"f\" value=\"$facility\"/>\n";
    echo $tab[3];
    echo "</form>\n";

    echo $tab[4];
    echo "<form name=\"phonyForm\" action=\"" . $page . "\" method=\"get\">\n";
    echo $tab[4];
    echo "<select name=\"day\" onchange=\"setReserveDay(); setReserveDate();\">\n";
    $monthdays = getDaysInMonth($month, $year);	
    for($i = 1; $i <= $monthdays; $i++) {
        echo $tab[5];
        echo "<option value=\"$i\"" . ($i == $day ? " selected" : "") . ">$i</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "<select name=\"month\" onchange=\"setReserveMonth(); setReserveDate();\">\n";

    for($i = 0; $i < 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i+1, 1, $year));
		
        echo $tab[5];
        # note: JavaScript uses 0-11 for months
        print "<option value=\"$i\"" . ($i+1 == $month ? " selected" : "") . ">$m</option>\n";
    }
	
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "<select name=\"year\" onchange=\"setReserveYear(); setReserveDate();\">\n";
	
    $min = min($year, date("Y")) - 2;
    $max = max($year, date("Y")) + 2;
	
    for($i = $min; $i <= $max; $i++) {
        echo $tab[5];
        print "<option value=\"$i\"" . ($i == $year ? " selected" : "") . ">$i</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "</form>\n";
}


function genDateSelector($page, $t_stamp, $center, $facility, $instrument)
{
    global $tab;
    $day = date("d", $t_stamp);
    $month = date("m", $t_stamp);
    $year = date("Y", $t_stamp);
       	
    echo $tab[3];
    echo "<form name=\"dateForm\" action=\"" . $page ."\" method=\"get\">\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"d\" value=\"-1\"/>\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"c\" value=\"$center\"/>\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"f\" value=\"$facility\"/>\n";
    echo $tab[4];
    echo "<input type=\"hidden\" name=\"i\" value=\"$instrument\"/>\n";
    echo $tab[3];
    echo "</form>\n";

    echo $tab[4];
    echo "<form name=\"phonyForm\" action=\"" . $page . "\" method=\"get\">\n";
    echo $tab[4];
    echo "<select name=\"day\" onchange=\"setReserveDay(); setReserveDate();\">\n";
    $monthdays = getDaysInMonth($month, $year);	
    for($i = 1; $i <= $monthdays; $i++) {
        echo $tab[5];
        echo "<option value=\"$i\"" . ($i == $day ? " selected" : "") . ">$i</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "<select name=\"month\" onchange=\"setReserveMonth(); setReserveDate();\">\n";

    for($i = 0; $i < 12; $i++)
    {
        $m = strftime("%b", mktime(0, 0, 0, $i+1, 1, $year));
		
        echo $tab[5];
        # note: JavaScript uses 0-11 for months
        print "<option value=\"$i\"" . ($i+1 == $month ? " selected" : "") . ">$m</option>\n";
    }
	
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "<select name=\"year\" onchange=\"setReserveYear(); setReserveDate();\">\n";
	
    $min = min($year, date("Y")) - 2;
    $max = max($year, date("Y")) + 2;
	
    for($i = $min; $i <= $max; $i++) {
        echo $tab[5];
        print "<option value=\"$i\"" . ($i == $year ? " selected" : "") . ">$i</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "</form>\n";
}

function genTimeSelector($page, $timestamp, $resolution)
{
    global $tab;
    global $twentyfourhour_format;

    $year = date("Y", $timestamp);
    $month = date("m", $timestamp);
    $day = date("d", $timestamp);
    if(!$twentyfourhour_format) {
        $hour = date("g", $timestamp);
        $ampm = date("a", $timestamp);
        $firsthr =  1;
        $lasthr  = 12;
    }
    else {
        $hour = date("G", $timestamp);
        $ampm = "na";
        $firsthr =  0;
        $lasthr  = 23;
    }
    $minute = date("i", $timestamp);
    $res = $resolution / 60;

    echo $tab[4];
    echo "<select name=\"startHour\">\n";

    for($i = $firsthr; $i <= $lasthr; $i++) {
        $h = sprintf("%02d", $i);
        echo $tab[5];
        echo "<option" . ($i == $hour ? " SELECTED" : "") . ">$h</option>\n";
    }
    echo $tab[4];
    echo "</select> :\n";

    echo $tab[4];
    echo "<select name=\"startMinute\">\n";
    for($i = 0; $i < 60; $i+= $res) {
        $m = sprintf("%02d", $i);
        echo $tab[5];
        echo "<option" . ($i == $minute ? " SELECTED" : "") . ">$m</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";

    if($ampm == "am") {
        echo $tab[4];
        echo "<a href=\"#\" onclick=\"document.bookingDesc.startTime[0].checked=true;\">\n";
        echo $tab[4];
        echo "<input type=\"radio\" name=\"ampm\" vaue=\"am\" checked/> am</a>\n";

        echo $tab[4];
        echo "<a href=\"#\" onclick=\"document.bookingDesc.startTime[1].checked=true;\">\n";
        echo $tab[4];
        echo "<input type=\"radio\" name=\"ampm\" value=\"pm\"/> pm</a>\n";
    }
    elseif($ampm == "pm") {
        echo $tab[4];
        echo "<a href=\"#\" onclick=\"document.bookingDesc.startTime[0].checked=true;\">\n";
        echo $tab[4];
        echo "<input type=\"radio\" name=\"ampm\" vaue=\"am\"/> am</a>\n";

        echo $tab[4];
        echo "<a href=\"#\" onclick=\"document.bookingDesc.startTime[1].checked=true;\">\n";
        echo $tab[4];
        echo "<input type=\"radio\" name=\"ampm\" value=\"pm\" checked/> pm</a>\n";
    }
}

# generates some html that can be used to select which center should be displayed.
function genCenterSelector($page, $t_stamp, $center, $facility, $instrument, $getpost="get")
{
    global $tab;

    if($getpost == "get") {
        echo $tab[4];
        echo "<form action=\"" . $page ."\" method=\"get\">\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"d\" value=\"" .  $t_stamp . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"f\" value=\"" . $facility . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"i\" value=\"" . $instrument . "\"/>\n";
        echo $tab[4];
        echo "<select name=\"c\" onchange=\"form.submit()\">\n";
    }
    else {
        echo $tab[4];
        echo "<select name=\"c\">\n";
    }
    echo $tab[5];
    echo "<option>CENTER</option>\n";
    echo $tab[5];
    echo "<option>------</option>\n";

    /* Query Center names from database */
    $qry_centers = "SELECT center_id,name_short FROM center_name ORDER BY name_short";
    $rslt_centers = sql_query($qry_centers);
    for($i=0; ($row_c = sql_row_keyed($rslt_centers,$i)); $i++) { 
        echo $tab[5];
        if($center == $row_c["center_id"]) {
           echo "<option value=\"".$row_c["center_id"]."\" selected>".$row_c["name_short"]."</option>\n";
        }
        else {
           echo "<option value=\"".$row_c["center_id"]."\">".$row_c["name_short"]."</option>\n";
        }
    } 
    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "</form>\n";
} # end genCenterSelector

# generates some html that can be used to select which facility should be
# displayed.
function genFacilitySelector($page, $t_stamp, $center, $facility, $instrument, $getpost="get")
{
    global $tab;

    if($getpost == "get") {
        echo $tab[4];
        echo "<form action=\"" . $page ."\" method=\"get\">\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"d\" value=\"" .  $t_stamp . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"c\" value=\"" . $center . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"i\" value=\"" . $instrument . "\"/>\n";
        echo $tab[4];
        echo "<select name=\"f\" onchange=\"form.submit()\">\n";
    }
    else {
        echo $tab[4];
        echo "<select name=\"f\">\n";
    }
    echo $tab[5];
    echo "<option>FACILITY</option>\n";
    echo $tab[5];
    echo "<option>--------</option>\n";

    /* Query Facilities from given Center */
    $qry_facilities = "SELECT fac_id,name_short FROM fac_name WHERE center_id LIKE $center ORDER BY name_short";
    $rslt_facilities = sql_query($qry_facilities);

    $i=0;
    while($row_f = sql_row_keyed($rslt_facilities,$i)) {
       if($facility == $row_f["fac_id"]) {
          echo $tab[5];
          echo "<option value=\"".$row_f["fac_id"]."\" selected>".$row_f["name_short"]."</option>\n";
       }
       else{
          echo $tab[5];
          echo "<option value=\"".$row_f["fac_id"]."\">".$row_f["name_short"]."</option>\n";
       }
       $i++;
    }

    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "</form>\n";
} # end genFacilitySelector

# generates some html that can be used to select which instrument should be
# displayed.
function genInstrumentSelector($page, $t_stamp, $center, $facility, $instrument, $getpost="get")
{
    global $tab;

    if($getpost == "get") {
        echo $tab[4];
        echo "<form action=\"" . $page ."\" method=\"get\">\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"d\" value=\"" .  $t_stamp . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"c\" value=\"" . $center . "\"/>\n";
        echo $tab[4];
        echo "<input type=\"hidden\" name=\"f\" value=\"" . $facility . "\"/>\n";
        echo $tab[4];
        echo "<select name=\"i\" onchange=\"form.submit()\">\n";
    }
    else {
        echo $tab[4];
        echo "<select name=\"i\">\n";
    }
    echo $tab[5];
    echo "<option>INSTRUMENT</option>\n";
    echo $tab[5];
    echo "<option>----------<option>\n";

    /* Query Instruments from given Facility */
    $qry_equip = "SELECT equip_id,name_short FROM fac_equipment WHERE fac_id LIKE $facility and schedule like 1 ORDER BY name_short";
    $rslt_equip = sql_query($qry_equip);

    $i=0;
    while($row_e = sql_row_keyed($rslt_equip,$i)) {
       if($instrument == $row_e["equip_id"]) {
          echo $tab[5];
          echo "<option value=\"".$row_e["equip_id"]."\" selected>".$row_e["name_short"]."</option>\n";
       }
       else{
          echo $tab[5];
          echo "<option value=\"".$row_e["equip_id"]."\">".$row_e["name_short"]."</option>\n";
       }
        $i++;
    }

    echo $tab[4];
    echo "</select>\n";
    echo $tab[4];
    echo "</form>\n";
} # end genInstrumentSelector

/* Code to generate WEEK OF drop-down menu */
function genWeekOfSelector($thisdate) {
    global $tab;
    global $weekstarts;
    $sec_in_day = 86400;
    $day_of_week = strftime('%w', $thisdate);

    # get just the date, not hour/min/sec
    $thisdate = strtotime( strftime('%e %B %Y', $thisdate));

    # calculate Sunday or Monday of the week including thisdate
    $firstday = $thisdate - ($sec_in_day*$day_of_week - $sec_in_day*$weekstarts);
    $start = $firstday - ($sec_in_day*21);
    $end = $firstday + ($sec_in_day*21);

    echo $tab[5];
    echo "<form action=\"week.php\" method=\"get\" style=\"display:inline;\">\n";
    echo $tab[5];
    echo "<select id=\"weekOf\" name=\"d\">\n";
    $day_offset = $day_of_week * $sec_in_day;
    for($t=$start; $t<=$end; $t+=($sec_in_day*7)){
        $sunday = date("M j",$t);
        $saturday = date("M j",($t + ($sec_in_day*6)));
        if(($t + $day_offset) == $thisdate) {
            echo $tab[6];
            echo "<option value=\"" . $t . "\" onclick=\"submit();\" selected>";
        }
        else {
            echo $tab[6];
            echo "<option value=\"" . $t . "\" onclick=\"submit();\" >";
        }
        echo $sunday . " - " . $saturday;
        echo "</option>\n";
    }
    echo $tab[5];
    echo "</select>\n";
    echo $tab[6];
    echo "<a href=\"#\" onclick=\"submit();\">GO!</a>\n";
    echo $tab[5];
    echo "</form>\n";
}

/* Code to generate MONTH OF drop-down menu */
function genMonthOfSelector($thisdate) {
    global $tab;
    $thismonth = strftime('%m', $thisdate);
    $thisyear = strftime('%Y', $thisdate);

    echo $tab[5];
    echo "<form action=\"month.php\" method=\"get\" style=\"display:inline;\">\n";
    echo $tab[5];
    echo "<select id=\"monthOf\" name=\"d\">\n";
    for($m=-3; $m <= 3 ; $m++){

        if($thismonth + $m < 1) {
            # last year
            $month = 12 + ($thismonth + $m);
            $year = $thisyear - 1;
        }
        elseif($thismonth + $m > 12) {
            # next year
            $month = ($thismonth + $m) % 12; 
            $year = $thisyear + 1;
        }
        else {
            # this year
            $month = $thismonth + $m;
            $year = $thisyear;
        }
        $t = strtotime( $month . '/1/' . $year );

        if($month == $thismonth) {
            echo $tab[6];
            echo "<option value=\"" . $t . "\" onclick=\"submit();\" selected>";
        }
        else {
            echo $tab[6];
            echo "<option value=\"" . $t . "\" onclick=\"submit();\">";
        }
        echo date("F Y",$t);
        echo "</option>\n";
    }
    echo $tab[5];
    echo "</select>\n";
    echo $tab[6];
    echo "<a href=\"#\" onclick=\"submit();\">GO!</a>\n";
    echo $tab[5];
    echo "</form>\n";
}

function genBookTypeSelector($book_type = "normal")
{
    global $tab;
    echo $tab[4];
    echo "<select name=\"book_type\">\n";
    $booking_types = array("normal","training","maintenance","tentative","staffappt");
    foreach($booking_types as $type) {
        echo $tab[5];
        echo "<option value=\"". $type ."\"". ($type==$book_type ? " SELECTED" : "") . ">$type</option>\n";
    }
    echo $tab[4];
    echo "</select>\n";

}

function createAdminMenu(){
?>

<div id="facEdit">
	<h2>Facility Editing Menu</h2>
	<ul>
		<li><a href="admin.php?e=0">Facility Editing Home</a></li>
		<li><a href="admin.php?e=1">Facility Config</a></li>
		<li><a href="admin.php?e=2">Facility Description</a></li>
		<li><a href="admin.php?e=3">Instrument List</a></li>
	</ul>
</div>

<?php
}

# are these functions necessary?
function getNumInstruments($facility)
{
    # Query Instruments from given Facility
    $qry_equip = "SELECT COUNT(*) FROM fac_equipment 
                  WHERE fac_id LIKE $facility 
                  AND schedule LIKE '1'";
    $equip_num = sql_query1($qry_equip);
    if($equip_num == -1) {$equip_num = 0;}
    return $equip_num;
}

function getNumManagers($facility)
{
    # Query Instruments from given Facility
    $qry_equip = "SELECT COUNT(*) FROM fac_staff 
                  WHERE fac_id LIKE $facility 
                  AND title LIKE 'Manager'
                  AND schedule LIKE '1'";

    $equip_num = sql_query1($qry_equip);
    if($equip_num == -1) {$equip_num = 0;}
    return $equip_num;
}

function getBookings($facility, $time_start, $time_stop)
{
    $hours_start =  sql_syntax_timestamp_from_unix( $time_start );
    $hours_stop  =  sql_syntax_timestamp_from_unix( $time_stop );

    # Query equipment for given facility
    $instruments = $facility->instruments;

    # Query bookings for given equipment
    $qry_book_main = "SELECT * FROM fac_reserve_entry WHERE
                 start_time < $hours_stop AND end_time > $hours_start ";

    $bookings = array();
    $bcount = 0;
    foreach($instruments as $equip)
    {
        $qry_book = $qry_book_main . "AND equip_id LIKE '$equip->equip_id' ORDER BY end_time";
        $rslt_book = sql_query($qry_book);
        if($rslt_book) {
            for($i=0; ($row_b = sql_row_keyed($rslt_book,$i)); $i++) {
                $bookings[$bcount] = new ReserveBooking($row_b);
                $bcount++;
            }
        }
        else {
            $error = sql_error();
            echo "\n\nerror: $error\n\nINSTR_FGB\n\n<br>";
        }
    }
    #echo "<b>There were $bcount bookings</b>\n";
    #echo "<b>start:" . date("YmdGi",$time_start) . " stop:" . date("YmdGi", $time_stop) . "</b>\n";
    return $bookings;
}

function getBookingsInstrument($instrument, $time_start, $time_stop)
{
    $hours_start = sql_syntax_timestamp_from_unix( $time_start );
    $hours_stop  = sql_syntax_timestamp_from_unix( $time_stop );

    # Query bookings for given equipment
    $qry_book = "SELECT * FROM fac_reserve_entry WHERE
                 start_time < $hours_stop AND end_time > $hours_start 
                 AND equip_id LIKE '$instrument' ORDER BY start_time";

    $bookings = array();
    $bcount = 0;
    $rslt_book = sql_query($qry_book);
    if($rslt_book) {
        for($i=0; ($row_b = sql_row_keyed($rslt_book,$i)); $i++) {
            $bookings[$bcount] = new ReserveBooking($row_b);
            $bcount++;
        }
    }
    else {
        $error = sql_error();
        echo "\n\nerror: $error\n\nINSTR_FGBI\n\n<br>";
    }
    #echo "<b>There were $bcount bookings</b>\n";
    #echo "<b>start:" . date("YmdGi",$time_start) . " stop:" . date("YmdGi", $time_stop) . "</b>\n";
    return $bookings;

}

function showAccessDenied($message)
{
    echo "<b>\n";
    echo "Access Denied: " . $message . "\n";
    echo "</b>\n";
}

# manager schedules as bookings
function getManagerSchedules($facility, $today) {
    $today = getJustDay($today);

    # Query schedule(s) for given facility
    $qry_book = "SELECT * FROM fac_staff WHERE
                 fac_id LIKE '$facility' AND title LIKE 'Manager'
                 AND schedule LIKE '1'";

    $bookings = array();
    $bcount = 0;
    $rslt_book = sql_query($qry_book);
    if($rslt_book) {
        for($i=0; ($row_b = sql_row_keyed($rslt_book,$i)); $i++) {
            $book = array(
                "book_id" => "0",
                "start_time" => $today + ($row_b["hours_start"] * 3600),
                "end_time" => $today + ($row_b["hours_end"] * 3600),
                "book_type" => "staffhours",
                "timestamp" => $today,
                "created_by" => $row_b["user_id"],
                "description" => "Manager Schedule",
                "equip_id" => $row_b["user_id"],
                "user_id" =>  $row_b["user_id"]
            );
            $bookings[$bcount] = new ReserveBooking($book);
            $bcount++;
        }
    }
    else {
        $error = sql_error();
        echo "\n\nerror: $error\n\nFAC_FGMS\n\n<br>";
    }
    #echo "<b>There were $bcount bookings</b>\n";
    #echo "<b>" . $bookings[0]->PrintTime() . "</b><br/>\n";
    return $bookings;
}

# fake ReserveInstrument class to treat managers as equipment
class ReserveMgrEquip
{
    var $equip_id;
    var $name_short;
    function ReserveMgrEquip($db_ent)
    {
        $this->equip_id = $db_ent["user_id"];
        $this->name_short = $db_ent["firstname"]." ".$db_ent["lastname"];
    }
}

function getManagersAsEquipment($facility)
{
    # Query manager(s) as instrument(s) for given facility
    $qry_equip = "SELECT * FROM fac_staff WHERE
                  fac_id LIKE '$facility' 
                  AND title LIKE 'Manager'
                  AND schedule LIKE '1'";

    $managers = array();
    $ecount = 0;
    $rslt_equip = sql_query($qry_equip);
    if($rslt_equip) {
        for($i=0; ($row_e = sql_row_keyed($rslt_equip,$i)); $i++) {
            $managers[$ecount] = new ReserveMgrEquip($row_e);
            $ecount++;
        }
    }
    else {
        $error = sql_error();
        echo "\n\nerror: $error\n\nFAC_FGME\n\n<br>";
    }
    #echo "<b>There were $ecount managers</b>\n";
    return $managers;
}

# just the dd/mm/yy part of a timestamp
function getJustDay($timestamp)
{
    #return strtotime(date("l, F j, Y",$timestamp));
    return $timestamp - ($timestamp % 86400) + 14460; # -> 0:01
}

# full string version of a particular time
function getTimeString($timestamp)
{
    return date("l, F j, Y H:i",$timestamp);
}

function getBookingDB($book_id)
{
    # Query booking by book_id
    $qry_book = "SELECT * FROM fac_reserve_entry WHERE
                 book_id LIKE '$book_id'";

    $db_book = array();
    $rslt_book = sql_query($qry_book);
    if($rslt_book) {
        $db_book = sql_row_keyed($rslt_book,0);
        $booking = new ReserveBooking($db_book);
    }
    else {
        $error = sql_error();
        echo "\n\nerror: $error\n\nBOOK_IDGB\n\n<br>";
    }
    return $db_book;
}

include("classes.inc.php");
?>
