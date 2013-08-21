<div id="admin">
	<h2>Facility Description</h2>
	
	<form name="editFacility" action="" method="post">
	
		<h3>Full Name</h3>
		<p>
		<span>The official title of the facility.</span><br />
		<input name="name_long" type="text" value="<? echo $facility->name_long; ?>" size="55" />
		</p>
				
		<h3>Abbreviated Name</h3>
		<p>
		<span>The shortened title or common name of the facility.</span><br />
		<input name="name_short" type="text" value="<? echo $facility->name_short; ?>" size="55" />
		</p>
				
		<h3>Brief Description</h3>
		<p>
		<span>Should be between 50 and 75 characters.</span><br />
		<textarea name="desc_short" cols="40" rows="6">
		<? echo $facility->desc_short; ?>
		</textarea>
		</p>
				
		<h3>Full Description</h3>
		<p>
		<span>The official description of the facility. Should be at least 200 words.</span><br />
		<textarea name="desc_long" cols="40" rows="10">
		<? echo $facility->desc_long; ?>
		</textarea>
		</p>
				
		<h3>Location</h3>
		<p>
		<span>The building and room numbers of all rooms associated with the facility.</span><br />
		<input name="location" type="text" value="<? echo $facility->location; ?>" size="55" />
		</p>
				
		<h3>Contact Information</h3>
		<p>
		<span>The general contact info of the facility.</span><br />
		<input name="contact_info" type="text" value="<? echo $facility->contact_info; ?>" size="55" />
		</p>
				
		<h3>Faculty Advisor</h3>
		<p>
		<input name="advisor" type="text" value="<? echo $facility->advisor; ?>" size="55" />
		</p>
		
		<h3>Rules</h3>
		<p>
		<span>List all general facility rules. Seperate each rule with a blank line. Rules about facility hours and reservation window will be added automatically.</span><br />
		<textarea name="rules" cols="40" rows="10">
		<? echo $facility->rules; ?>
		</textarea>
		</p>
				
		<h3>Schedule Facility</h3>
		<p>
		<a name="jumpSched"></a>
		<span>Use Reserve to schedule facility instruments?</span><br />
		<a name="schedule" href="#jumpSched" onclick="document.editFacility.schedule[0].checked=true;"><input name="schedule" type="radio" /> Yes</a> 
		<a name="schedule" href="#jumpSched" onclick="document.editFacility.schedule[1].checked=true;"><input name="schedule" type="radio" /> No</a> 
		</p>
		
		<div style="text-align:center; margin:10px; padding:6px 0px; border-top: 1px solid rgb(220,220,220);">
			<input type="submit" value="Save Changes" />
		</div>
		
	</form>
</div>