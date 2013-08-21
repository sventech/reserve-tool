<?

$qry = "SELECT * FROM fac_reserve_config WHERE fac_id LIKE '$facility->fac_id'";
$rslt = sql_query($qry);
$row = mysql_fetch_array($rslt);

?>

<div id="admin">
	<h2>Facility Configuration</h2>
	
	<form name="editConfig" action="" method="post">
	
		<h3>Setting Facility Hours</h3>
		<p>
		<span>Start of Facility Day (hh:mm):</span><br />
		<input name="hours_start" type="text" value="<? echo $row["hours_start"]; ?>" size="2" /> : 
		<input name="hours_start" type="text" value="<? echo $row["hours_start"]; ?>" size="2" /> 
		<a href="#" onclick="document.editConfig.hours_startAMPM[0].checked=true;"><input name="hours_startAMPM" type="radio" value="am" /> am</a> 
		<a href="#" onclick="document.editConfig.hours_startAMPM[1].checked=true;"><input name="hours_startAMPM" type="radio" value="pm" /> pm</a>
		</p>
		
		<p>
		<span>End of Facility Day (hh:mm):</span><br />
		<input name="hours_stop" type="text" value="<? echo $row["hours_stop"]; ?>" size="2" /> : 
		<input name="hours_stop" type="text" value="<? echo $row["hours_stop"]; ?>" size="2" /> 
		<a href="#" onclick="document.editConfig.hours_stopAMPM[0].checked=true;"><input name="hours_stopAMPM" type="radio" value="am" /> am</a> 
		<a href="#" onclick="document.editConfig.hours_stopAMPM[1].checked=true;"><input name="hours_stopAMPM" type="radio" value="pm" /> pm</a>
		</p>
	
		<h3>Time Grid Resolution for Day/ Week View</h3>
		<p>
		<a name="editTimeRes"></a>
		<span>Time Resolution:</span><br />
		<a href="#editTimeRes" onclick="document.editConfig.time_res[0].checked=true;"><input name="time_res" type="radio" /> 60 min</a> 
		<a href="#editTimeRes" onclick="document.editConfig.time_res[1].checked=true;"><input name="time_res" type="radio" /> 30 min</a> 
		<a href="#editTimeRes" onclick="document.editConfig.time_res[2].checked=true;"><input name="time_res" type="radio" /> 15 min</a> 
		<nobr>
		<a href="#editTimeRes" onclick="document.editConfig.time_res[3].checked=true;"><input name="time_res" type="radio" /> Other: </a>
		<input name="time_res" type="text" value="" size="4" /> 
		<select style="font-size: 9px;">
			<option>minutes</option>
			<option>hours</option>
		</select>
		</nobr>
		</p>
	
		<h3>Time Period in which a Reservation can be Modified</h3>
		<p>
		<span>User can reserve an instrument:</span><br />
		<nobr>
		&bull; At most:  
		<input name="book_begin" type="text" value="<? echo $row["book_begin"]; ?>" size="4" /> 
		<select style="font-size: 9px;">
			<option>minutes</option>
			<option>hours</option>
			<option>days</option>
			<option>weeks</option>
		</select>
		</nobr>
		<br />
		<nobr>
		&bull; At least:  
		<input name="book_end" type="text" value="<? echo $row["book_end"]; ?>" size="4" /> 
		<select style="font-size: 9px;">
			<option>minutes</option>
			<option>hours</option>
			<option>days</option>
			<option>weeks</option>
		</select>
		</nobr><br/>
		<span>in advance of scheduled time.</span>
		</p>
		
		<h3>Time Period in which Schedule Can Be Viewed</h3>
		<p>
		<span>Schedule can be viewed:</span><br />
		<nobr>
		&bull;   
		<input name="book_begin" type="text" value="<? echo $row["book_begin"]; ?>" size="4" /> 
		<select style="font-size: 9px;">
			<option>day(s)</option>
			<option>week(s)</option>
			<option>month(s)</option>
			<option>year(s)</option>
		</select> prior to today.
		</nobr>
		<br />
		<nobr>
		&bull;   
		<input name="book_end" type="text" value="<? echo $row["book_end"]; ?>" size="4" /> 
		<select style="font-size: 9px;">
			<option>day(s)</option>
			<option>week(s)</option>
			<option>month(s)</option>
			<option>year(s)</option>
		</select> from today.
		</nobr>
		</p>
		
		<div style="text-align:center; margin:10px; padding:6px 0px; border-top: 1px solid rgb(220,220,220);">
			<input type="submit" value="Save Changes" /> 
			<input type="button" value="Default Values" />
		</div>
		
	</form>
</div>