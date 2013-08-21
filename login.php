<?php
// $Id: login.php,v 1.6 2003/12/05 23:05:44 sven Exp $

if(!isset($_COOKIE["WhereTo"])) {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/";
}
else {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/" . $_COOKIE["WhereTo"];
}

// Password change page
$wheretopw = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/changepw.php" ;

include_once("functions.inc.php");
check_auth();

include_once("config.inc.php");
include_once("mysql.inc.php");

print_header();
?>

    <div id="content">
			
<?php

if($netID_secure == 'failure') {
    CCMR_Auth_drawLogin();
    if($mustChngPW == True) {
        header($wheretopw);
    }
}
else{
    header($whereto);
}

?>

    </div>				    

<?php

print_footer();

?>
