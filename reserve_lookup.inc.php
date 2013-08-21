<?

## Looks up data in the LDAP
## Created 12 June 2003
function LDAPlookup($netid){
	## Make LDAP Connection
	$ds = ldap_connect("directory.mycompany.com") or die("LDAP Connection didn't succeed.");
	if($ds){
		$r = ldap_bind($ds);
		$sr = ldap_search($ds, "o=MyCompany, c=US", "uid=$netid") or die ("LDAP Query didn't work.");
		$info = ldap_get_entries($ds, $sr);
		ldap_close($ds);		
		return $info;
	}
}

?>
