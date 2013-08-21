<?php

include('adminInst_processing.inc');

?>

<div id="admin">
	<h2>Instrument List</h2>
	
<?php

if($mi!=0){
	echo "<p><a href=\"admin.php?e=4&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "&amp;mi=0#this\">[+]</a> <strong>Add Instrument</strong></p>\n";
}
else{
	echo "<div>\n";
	echo "<a name=\"this\" href=\"admin.php?e=4&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "\">[-]</a> <strong><em>Add Instrument</em></strong><br />\n";
	
	echo "<form name=\"editInstrument\" action=\"admin.php?e=4&amp;mi=" . $mi . "#this\" method=\"post\">\n";
	
	echo "<h3>Full Instrument Name</h3>\n";
	echo "<p>\n";
	echo "<input name=\"name_long\" type=\"text\" value=\"\" size=\"50\" />\n";
	echo "</p>\n";

	echo "<h3>Short or Abbreviated Instrument Name</h3>\n";
	echo "<p>\n";
	echo "<span>Please limit to six (6) characters.</span><br />\n";
	echo "<input name=\"name_short\" type=\"text\" value=\"\" size=\"10\" />\n";
	echo "</p>\n";

	echo "<h3>Short or Abbreviated Description</h3>\n";
	echo "<p>\n";
	echo "<textarea name=\"desc_short\" cols=\"40\" rows=\"6\">\n";
	echo "</textarea>\n";
	echo "</p>\n";

	echo "<h3>Full Detailed Description</h3>\n";
	echo "<p>\n";
	echo "<textarea name=\"desc_long\" cols=\"40\" rows=\"10\">\n";
	echo "</textarea>\n";
	echo "</p>\n";

	echo "<h3>Physical Location of Instrument</h3>\n";
	echo "<p>\n";
	echo "<span>Room Number and Building that the Instrument is in.</span>\n";
	echo "<input name=\"location\" type=\"text\" value=\"\" size=\"50\" />\n";
	echo "</p>\n";
		
	echo "<h3>Schedule Instrument</h3>\n";
	echo "<p>\n";
	echo "<span>Use Reserve to schedule this instrument?</span><br />\n";
	echo "<a name=\"schedule\" href=\"#this\" onclick=\"document.editInstrument.schedule[0].checked=true;\"><input name=\"schedule\" type=\"radio\" value=\"1\" /> Yes</a> \n";
	echo "<a name=\"schedule\" href=\"#this\" onclick=\"document.editInstrument.schedule[1].checked=true;\"><input name=\"schedule\" type=\"radio\" value=\"0\" /> No</a> \n";
	echo "</p>\n";
	
	echo "<h3>View Order</h3>\n";
	echo "<p>\n";
	echo "<span>Which column would you like the instrument to appear? Duplicate values will be alphebetized.<br />(Leftmost being 1)</span>\n";
	echo "<select name=\"view_order\">\n";
	for($i=1; $i<=$facility->num_instruments; $i++){
		echo "<option>$i</option>\n";
	}
	echo "</select>\n";
	echo "</p>\n";
	
	echo "<div style=\"text-align:center; margin-top:10px; padding:2px; border-top:1px solid rgb(220,220,220);\">\n";
	echo "<input name=\"submit\" type=\"submit\" value=\"Add Instrument\" /> \n";
	echo "<input type=\"reset\" value=\"Clear Form\" />\n";
	echo "</div>\n";
	
	echo "</form>\n";

	echo "</div>\n";
}

for ($i=0; $i<$facility->num_instruments; $i++) {

	if( ($i%2) == 0 ){
		$color='rgb(235,235,235)';
	}
	else{
		$color='transparent';
	}
	
	if($mi!=$facility->instruments[$i]->equip_id){
		echo "<p style=\"background-color:" . $color . "\">\n";
		echo "<a href=\"admin.php?e=4&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "&amp;mi=" . $facility->instruments[$i]->equip_id . "#this\">[+]</a> " . $facility->instruments[$i]->name_long . "\n";
		echo "</p>\n";
	}
	else{
		echo "<div style=\"background-color:" . $color . "\">\n";
		echo "<a name=\"this\" href=\"admin.php?e=4&amp;c=" . $facility->center_id . "&amp;f=" . $facility->fac_id . "\">[-]</a> <strong><em>" . $facility->instruments[$i]->name_long . "</em></strong><br />\n";
		echo "<form name=\"editInstrument\" action=\"admin.php?e=4&amp;mi=" . $mi . "#this\" method=\"post\">\n";
	
		echo "<h3>Delete Instrument</h3>\n";
		echo "<p>\n";
		echo "<span>This will delete the instrument from the database and CANNOT be undone.</span><br />\n";
		echo "<input type=\"button\" onclick=\"alert('This does not actually work just yet.');\" value=\"Delete Instrument\" />\n";
		echo "</p>\n";
		
		echo "<h3>Full Instrument Name</h3>\n";
		echo "<p>\n";
		echo "<input name=\"name_long\" type=\"text\" value=\"" . $facility->instruments[$i]->name_long . "\" size=\"50\" />\n";
		echo "</p>\n";
	
		echo "<h3>Short or Abbreviated Instrument Name</h3>\n";
		echo "<p>\n";
		echo "<span>Please limit to six (6) characters.</span><br />\n";
		echo "<input name=\"name_short\" type=\"text\" value=\"" . $facility->instruments[$i]->name_short . "\" size=\"10\" />\n";
		echo "</p>\n";
	
		echo "<h3>Short or Abbreviated Description</h3>\n";
		echo "<p>\n";
		echo "<textarea name=\"desc_short\" cols=\"40\" rows=\"6\">\n";
		echo $facility->instruments[$i]->desc_short;
		echo "</textarea>\n";
		echo "</p>\n";
	
		echo "<h3>Full Detailed Description</h3>\n";
		echo "<p>\n";
		echo "<textarea name=\"desc_long\" cols=\"40\" rows=\"10\">\n";
		echo $facility->instruments[$i]->desc_long;
		echo "</textarea>\n";
		echo "</p>\n";
	
		echo "<h3>Physical Location of Instrument</h3>\n";
		echo "<p>\n";
		echo "<span>Room Number and Building that the Instrument is in.</span>\n";
		echo "<input name=\"location\" type=\"text\" value=\"" . $facility->instruments[$i]->location . "\" size=\"50\" />\n";
		echo "</p>\n";
			
		echo "<h3>Schedule Instrument</h3>\n";
		echo "<p>\n";
		echo "<span>Use Reserve to schedule this instrument?</span><br />\n";
		echo "<a name=\"schedule\" href=\"#this\" onclick=\"document.editInstrument.schedule[0].checked=true;\"><input name=\"schedule\" type=\"radio\" /> Yes</a> \n";
		echo "<a name=\"schedule\" href=\"#this\" onclick=\"document.editInstrument.schedule[1].checked=true;\"><input name=\"schedule\" type=\"radio\" /> No</a> \n";
		echo "</p>\n";
	
		echo "<h3>View Order</h3>\n";
		echo "<p>\n";
		echo "<span>Which column would you like the instrument to appear? Duplicate values will be alphebetized.<br />(Leftmost being 1)</span>\n";
		echo "<select name=\"view_order\">\n";
		for($j=1; $j<=$facility->num_instruments; $j++){
			echo "<option";
			if($facility->instruments[$i]->view_order == $j){echo " selected";}
			echo ">$j</option>\n";
		}
		echo "</select>\n";
		echo "</p>\n";
		
		echo "<div style=\"text-align:center; margin-top:20px; padding:2px; border-top:1px solid rgb(220,220,220);\">\n";
		echo "<input name=\"submit\" type=\"submit\" value=\"Update Info\" /> \n";
		echo "<input type=\"reset\" value=\"Current Info\" />\n";
		echo "</div>\n";
		
		echo "</form>\n";
	
		echo "</div>\n";
	}

}

?>

</div>