<?php

	
	if($_GET['act']=='ch'){
	    db_edit_city($_GET['test'], $_GET['f'], $_GET['t']);
	}

	if($_GET['act'] == 'chr'){
	    db_edit_region($_GET['test'], $_GET['f'], $_GET['t']);
	}

	$rows = db_get_tests();
	$test = 0;
	echo "<form>";
	echo "{$locale['test']}&nbsp;<select name=\"test\" onChange=\"ch_cities();\">";
	for($i=0; $i<count($rows); $i++){
		echo "<option value=\"{$rows[$i][3]}\"";
		if($rows[$i][3] == $_GET['test']){ $test = $i; echo "selected"; }
		echo " >{$rows[$i][0]} ({$locale[$rows[$i][1]]})</option>";
	}
	echo "</select><br>";
	echo "{$locale['cre_date']}:&nbsp;{$rows[$test][0]}<br>";
	echo "{$locale['test_lang']}:&nbsp;{$locale[$rows[$test][1]]}<br>";
	echo "{$locale['descr']}:&nbsp;{$rows[$test][2]}<br>";
	echo "{$locale['used']}:&nbsp;{$rows[$test][4]}";
	echo "</form>";


?>
<table width="100%" border=1>
<tr>
	<td><b><?php echo $locale['tests']; ?></b></td>
	<td><b><?php echo $locale['cities']; ?></b></td>
	<td>&nbsp;</td>
	<td><b>Region</b></td>
	<td>&nbsp;</td></tr>
<?php
	$rows = db_get_cities($rows[$test][3]);
	for($i=0; $i<count($rows); $i++){
		echo "<tr><td>{$rows[$i][1]}</td>".
				  "<td>{$rows[$i][0]}</td>".
				  "<td align=\"center\"><div onClick=\"edit_city('".
				    addcslashes($rows[$i][0],"'").
				    "')\">Edit ".$locale['cities']."</div></td>".
				    "<td><div id='ereg{$i}'>".$locale['reg'.$rows[$i][2]]."</td>".
				    "<td align=\"center\"><div onClick=\"edit_region({$i}, ".
					"'".addcslashes($locale['reg'.$rows[$i][2]],"'")."', ".
					"'".addcslashes($rows[$i][0], "'")."');\">Edit Region</div></td>".
				    "</tr>";
	}
?>
</table>
