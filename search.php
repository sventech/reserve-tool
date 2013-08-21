<?php
# $Id: search.php,v 1.3 2004/02/27 22:01:10 sven Exp $

    $MONTH_VIEW = False;
    $WEEK_VIEW = False;
    $DAY_VIEW = True;
    $INDEX_VIEW = False;



    include "config.inc.php";
    include "functions.inc.php";
    include "$dbsys.inc.php";

    # If we dont know the right date then make it up 
    if(!isset($d))
    {
        $d   = time();
    }

    if(empty($f))
	$f = get_default_facility();

    # Need all these different versions with different escaping.
    # search_str must be left as the html-escaped version because this is
    # used as the default value for the search box in the header.
    if (!empty($search_str)) 
    {
        $search_text = unslashes($search_str);
        $search_url = urlencode($search_text);
        $search_str = htmlspecialchars($search_text);
    }

    print_header();

# begin main code

if (!empty($advanced))
{
	echo "<h3>" . $vocab["advanced_search"] . "</h3>";
	echo "<form method=get action=\"search.php\">";
	echo $vocab["search_for"] . " <input type=text size=25 name=\"search_str\"><br>";
	echo $vocab["from"]. " ";
	genSearchDateSelector ("search.php", $d, $c, $f);
	echo "<br><input type=submit value=\"" . $vocab["search_button"] ."\">";
        print_footer();
	exit;
}

if (!$search_str)
{
	echo "<h3>" . $vocab["invalid_search"] . "</h3>";
        print_footer();
	exit;
}

# now is used so that we only display entries newer than the current time
echo "<h3>" . $vocab["search_results"] . " \"<font color=\"blue\">$search_str</font>\"</h3>\n";

$now = date();

# This is the main part of the query predicate, used in both queries:
$sql_pred = "( " . sql_syntax_caseless_contains("E.created_by", $search_text)
		. " OR " . sql_syntax_caseless_contains("E.user_id", $search_text)
		. " OR " . sql_syntax_caseless_contains("E.description", $search_text)
		. ") AND sql_syntax_timestamp_to_unix(E.end_time) > $now";

# The first time the search is called, we get the total
# number of matches.  This is passed along to subsequent
# searches so that we don't have to run it for each page.
if(!isset($total))
	$total = sql_query1("SELECT count(*) FROM fac_reserve_entry E WHERE $sql_pred");

if($total <= 0)
{
	echo "<em>" . $vocab["nothing_found"] . "</em>\n";
        print_footer();
	exit;
}

if(!isset($search_pos) || ($search_pos <= 0))
	$search_pos = 0;
elseif($search_pos >= $total)
	$search_pos = $total - ($total % $search["count"]);

# Now we set up the "real" query using LIMIT to just get the stuff we want.
$sql = "SELECT E.book_id, E.created_by, E.user_id, E.description, sql_syntax_timestamp_to_unix(E.start_time), I.fac_id
        FROM fac_reserve_entry E, fac_equipment I
        WHERE $sql_pred
        AND E.equip_id = I.equip_id
        ORDER BY E.start_time asc "
    . sql_syntax_limit($search["count"], $search_pos);

# this is a flag to tell us not to display a "Next" link
$result = sql_query($sql);
if (! $result) fatal_error(0, sql_error());
$num_records = sql_count($result);

$has_prev = $search_pos > 0;
$has_next = $search_pos < ($total-$search["count"]);

if($has_prev || $has_next)
{
	echo "<em>" . $vocab["records"] . ($search_pos+1) . $vocab["through"] . ($search_pos+$num_records) . $vocab["of"] . $total . "</em><br>";

	# display a "Previous" button if necessary
	if($has_prev)
	{
		echo "<a href=\"search.php?search_str=$search_url&search_pos=";
		echo max(0, $search_pos-$search["count"]);
		echo "&total=$total&d=$d\">";
	}

	echo "<em>" . $vocab["previous"] . "</em>";

	if($has_prev)
		echo "</a>";

	# print a separator for Next and Previous
	echo(" | ");

	# display a "Next" button if necessary
	if($has_next)
	{
		echo "<a href=\"search.php?search_str=$search_url&search_pos=";
		echo max(0, $search_pos+$search["count"]);
		echo "&total=$total&d=$d\">";
	}

	echo "<em>". $vocab["next"] ."</em>";

	if($has_next)
		echo "</a>";
}
?>
  <p>
  <table border=2 cellspacing=0 cellpadding=3>
   <tr>
    <th><?php echo $vocab["entry"]       ?></th>
    <th><?php echo $vocab["createdby"]   ?></th>
    <th><?php echo $vocab["namebooker"]  ?></th>
    <th><?php echo $vocab["description"] ?></th>
    <th><?php echo $vocab["start_date"]  ?></th>
   </tr>
<?php
for ($i = 0; ($row = sql_row($result, $i)); $i++)
{
	echo "<tr>";
	echo "<td><a href=\"view_entry.php?id=$row[0]\">$vocab[view]</a></td>\n";
	echo "<td>" . htmlspecialchars($row[1]) . "</td>\n";
	echo "<td>" . htmlspecialchars($row[2]) . "</td>\n";
	echo "<td>" . htmlspecialchars($row[3]) . "</td>\n";
	// generate a link to the day.php
	$link = getdate($row[4]);
	echo "<td><a href=\"day.php?d=$d&f=$row[5]\">"
	.  time_date_string($row[4]) . "</a></td>";
	echo "</tr>\n";
}

echo "</table>\n";
print_footer();
?>
