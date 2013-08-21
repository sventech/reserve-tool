<?php

if(!isset($_COOKIE["WhereTo"])) {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/";
}
else {
   $whereto = "Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/" . $_COOKIE["WhereTo"];
}

include_once("functions.inc.php");
check_auth();

include_once("config.inc.php");
include_once("mysql.inc.php");

print_header();
?>

    <div id="content">
			
<?php

if($mustChngPW == True) {
    chngCCMRPW();
}
else{
    header($whereto);
}

?>

    </div>				    

<?php

print_footer();

?>
