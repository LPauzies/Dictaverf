<?php


	$rows = db_get_tests();
	$test = 0;
	echo "<form>";
	echo "{$locale['test']}&nbsp;<select name=\"test\" onChange=\"ch_langs();\">";
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
<tr><td><b><?php echo $locale['lang']; ?></b></td>
	<td><b><?php echo $locale['tests']; ?></b></td>
	<td>&nbsp;</td></tr>
<?php
	$rows = db_get_lang($rows[$test][3]);
	for($i=0; $i<count($rows); $i++){
		echo "<tr><td>";
		if(isset($locale[$rows[$i][0]])){
			echo $locale[$rows[$i][0]]." (".$rows[$i][0].")";
		}else{
			echo $rows[$i][0];
		}
		echo "</td>".
				  "<td>{$rows[$i][1]}</td>".
				  "<td align=\"center\">&nbsp;</td></tr>";
	}
?>
</table>
