<?php

if(isset($_GET['lang']) && isset($_GET['descr']))
	db_test_create($_GET['lang'], $_GET['descr']);

if(isset($_GET['del_test']) && isset($_GET['test']))
	db_test_delete($_GET['test']);


?>
<form>
		<?php echo $locale['t_lang']; ?>&nbsp;<select name="lang">
			<option value="ru" <?php if($_SESSION['i_lang'] == 'ru') echo "selected"; ?>><?php echo $locale['ru']; ?> </option>
			<option value="fr" <?php if($_SESSION['i_lang'] == 'fr') echo "selected"; ?>><?php echo $locale['fr']; ?> </option>
			<option value="en" <?php if($_SESSION['i_lang'] == 'en') echo "selected"; ?>><?php echo $locale['en']; ?> </option>
		</select>
		&nbsp;&nbsp;
		<?php echo $locale['descr']; ?>&nbsp;<input type=text name="descr" value="">
		&nbsp;&nbsp;
		<input type=button value="<?php echo $locale['test_create']; ?>"
			onClick="test_create();">
	
</form>
<table width="100%" border=1>
<tr><td><?php echo $locale['cre_date']; ?></td>
	<td><?php echo $locale['test_lang']; ?></td>
	<td><?php echo $locale['descr']; ?></td>
	<td>&nbsp;</td></tr>
<?php
	$rows = db_get_tests();
	for($i=0; $i<count($rows); $i++){
		echo "<tr><td>{$rows[$i][0]}</td>".
				  "<td>{$locale[$rows[$i][1]]}</td>".
				  "<td>{$rows[$i][2]}&nbsp;</td>".
				  "<td align=\"center\"><img src=\"../img/del.gif\" alt=\"delete\" onClick=\"del_test({$rows[$i][3]})\"></td></tr>";
	}
?>
</table>
