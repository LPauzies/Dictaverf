<?php

if(isset($_GET['lang']) && isset($_GET['num']))
	db_gen_keys($_GET['lang'], $_GET['num']);

?>
<form>
		<?php echo $locale['t_lang']; ?>&nbsp;<select name="lang">
			<option value="ru" <?php if($_SESSION['i_lang'] == 'ru') echo "selected"; ?>><?php echo $locale['ru']; ?> </option>
			<option value="fr" <?php if($_SESSION['i_lang'] == 'fr') echo "selected"; ?>><?php echo $locale['fr']; ?> </option>
			<option value="en" <?php if($_SESSION['i_lang'] == 'en') echo "selected"; ?>><?php echo $locale['en']; ?> </option>
		</select>
		&nbsp;&nbsp;
		<?php echo $locale['n_keys']; ?>&nbsp;<input type=text name="nkeys" value="10">
		&nbsp;&nbsp;
		<input type=button value="<?php echo $locale['key_create']; ?>"
			onClick="gen_keys();">
	
</form>
<table width="100%" border=1>
<tr><td width="50px">&nbsp;</td><td><?php echo $locale['key']; ?></td><td><?php echo $locale['key_lang']; ?></td></tr>
<?php
	$rows = db_get_unused_keys();
	for($i=0; $i<count($rows); $i++){
		echo "<tr><td>".($i+1)."</td><td>{$rows[$i][0]}</td><td>{$locale[$rows[$i][1]]}</td></tr>";
	}
?>
</table>
