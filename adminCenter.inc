<?
$qry_center = "SELECT center_id,name_long FROM center_name order by name_long";
$rslt_center = sql_query($qry_center);
?>

<div id="admin">
	<h2>Center List</h2>
	
<?
echo "<p><a href=\"admin.php?e=1&amp;c=" . $row_center["center_id"] . "&amp;f=0#this\">[+]</a> <strong>Add Center</strong></p>\n";

$i = 0;
while($row_center = mysql_fetch_array($rslt_center)):
	(($i%2) == 0) ? $color='rgb(235,235,235)' : $color='transparent';
	echo "<p style=\"background-color:" . $color . "\"><a href=\"admin.php?e=1&amp;c=" . $row_center["center_id"] . "&amp;f=0#this\">[+]</a> " . $row_center["name_long"] . "</p>\n";
	$i++;
endwhile;
?>
	
</div>