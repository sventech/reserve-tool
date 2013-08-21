<?
$qry_staff = "SELECT user_id,lastname,firstname FROM fac_staff ORDER BY lastname";
$rslt_staff = sql_query($qry_staff);
?>

<div id="admin">
	<h2>Staff List</h2>
	
<?
echo "<p><a href=\"admin.php?e=5&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "&amp;mi=0#this\">[+]</a> <strong>Add Staff Member</strong></p>\n";

$i = 0;
while($row_staff = mysql_fetch_array($rslt_staff)):
	(($i%2) == 0) ? $color='rgb(235,235,235)' : $color='transparent';
	echo "<p style=\"background-color:" . $color . "\"><a href=\"admin.php?e=5&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "&amp;mi=0#this\">[+]</a> " . $row_staff["lastname"] . ", " . $row_staff["firstname"] . "</p>\n";
	$i++;
endwhile;
?>
	
</div>