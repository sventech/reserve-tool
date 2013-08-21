<?php

$qry_centers = "SELECT center_id FROM center_name";
$rslt_centers = sql_query($qry_centers);

/*
*/
$i = 0;
while($row_centers = sql_row($rslt_centers,$i++)):
	$centers[$i] = new ReserveCenter($row_centers[0]);
endwhile;

?>
					<ul>
						<li><a>Add Center</a></li>
<?

foreach($centers as $center){

	$center_id = $center->center_id;
	echo "\t\t\t\t\t\t";
	echo "<li><nobr><a>" . $center->name_short . "</a></nobr></li>\n";
	echo "\t\t\t\t\t\t";
	echo "<ul>\n";
	echo "\t\t\t\t\t\t\t";
	echo "<li><a>Description</a></li>\n";
	echo "\t\t\t\t\t\t\t";
	echo "<li><a>Configuration</a></li>\n";
	echo "\t\t\t\t\t\t\t";
	echo "<li><a>Facilities</a></li>\n";
	echo "\t\t\t\t\t\t\t";
	echo "<ul>\n";
	
	
	$qry_facilities = "SELECT fac_id FROM fac_name WHERE center_id = '$center_id'";
	$rslt_facilities = sql_query($qry_facilities);
	while($row_facilities = sql_row($rslt_facilities,$j++)):
		$facility = new ReserveFacility($j);
		echo "\t\t\t\t\t\t\t\t";
		echo "<li><a>" . $facility->name_short . "</a></li>\n";
		echo "\t\t\t\t\t\t\t\t";
		echo "<ul>\n";
		echo "\t\t\t\t\t\t\t\t\t";
		echo "<li><a>Description</a></li>\n";
		echo "\t\t\t\t\t\t\t\t\t";
		echo "<li><a>Configuration</a></li>\n";
		echo "\t\t\t\t\t\t\t\t\t";
		echo "<li><a>Instruments</a></li>\n";
		echo "\t\t\t\t\t\t\t\t\t";
		echo "<ul>\n";
		foreach($facility->instruments as $instrument){
			echo "\t\t\t\t\t\t\t\t\t\t";
			echo "<li><nobr><a>" . $instrument->name_short . "</a></nobr></li>\n";
			echo "\t\t\t\t\t\t\t\t\t\t";
			echo "<ul>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t";
			echo "<li><a>Description</a></li>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t";
			echo "<li><a>Configuration</a></li>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t";
			echo "<li><a>Users</a></li>\n";
			echo "\t\t\t\t\t\t\t\t\t\t";
			echo "</ul>\n";
		}
		echo "\t\t\t\t\t\t\t\t\t";
		echo "</ul>\n";
		echo "\t\t\t\t\t\t\t\t";
		echo "</ul>\n";
	endwhile;
	echo "\t\t\t\t\t\t\t";
	echo "</ul>\n";
	echo "\t\t\t\t\t\t";
	echo "</ul>\n";
}

?>
					</ul>
