<?
    $MONTH_VIEW = False;
    $WEEK_VIEW = False;
    $DAY_VIEW = True;
    if(isset($_GET["d"])){$today = date("l, F j, Y",$_GET["d"]);}else{$today = date("l, F j, Y");}
    $today_tmsp = strtotime($today);
    
    if(!isset($_GET["e"])){$e = 0;}else{$e = $_GET["e"];}

    include_once("functions.inc.php");
    check_auth();
    
    /* If they are not a logged in Staff Member, they will automatically be redirected to the LogIn page. */
    if($reserve_authed==''){header('Location: https://www.ccmr.cornell.edu/facilities/mrbs/reserve/login.php');}
    
    include_once("config.inc.php");
    include_once("$dbsys.inc.php");
    include_once("mincals.inc.php");

    $cookieValue = basename($PHP_SELF) ."?". $QUERY_STRING;
    setcookie("WhereTo",$cookieValue,time()+3600,'','',1);

    print_header();
?>
			<style media="all">
				#currentLocation{display:none;}
				#leftNarrow{padding:5px 0 0 0; font-size:0.9em; width:225px; height:500px; overflow:scroll;}
				#rightWide{float:right; margin-left:20px}
				#leftNarrow, #rightWide{
					border: solid 1px;
					border-color: rgb(220,220,220) rgb(150,150,150) rgb(125,125,125) rgb(200,200,200);
				}
				#leftNarrow ul{
					margin: 0 0 0 10px;
					padding: 0;
					list-style: none;
				}
				#leftNarrow a{
					color: rgb(0,0,0);
					border: none;
				}
				#content{margin-top:20px;}
			</style>
			
			<div id="content">

				<div id="rightWide">
					<h2>Options</h2>
				</div>
			
				<div id="leftNarrow">
<?php include('adminMenu.php'); ?>				
				</div>
			
			</div>
				    
<?php

print_footer();

?>
