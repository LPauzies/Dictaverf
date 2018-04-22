<?php

function login_form($err){
global $locale;
global $_SESSION 
?>

<table border=0 width="400px" height="150px" class="login">
<tr>
<td><center>&nbsp;<?php echo ($err != '')? $locale[$err]: ""; ?></center></td>
</tr>
<tr>
	<td><center>
	<b><?php echo $locale['title'];?></b>
	<p align=justify>
	<?php echo $locale['test_descr'];?>
	</center></td>
</tr>
<tr>
	<td nowrap><center>
<?php	if($_SESSION['test_id'] != 33) { ?>
		<form method="post">
		<?php echo $locale['ch_lang']; ?>&nbsp;<select name="i_lang" onChange="submit();">
			<option value="ru" <?php if($_SESSION['i_lang'] == 'ru') echo "selected"; ?>><?php echo $locale['ru']; ?> </option>
			<option value="fr" <?php if($_SESSION['i_lang'] == 'fr') echo "selected"; ?>><?php echo $locale['fr']; ?> </option>
			<option value="en" <?php if($_SESSION['i_lang'] == 'en') echo "selected"; ?>><?php echo $locale['en']; ?> </option>
		</select>
        	</form>
<?php   }  ?>
	<form method="post">
		<input type="hidden" name="key" value="<?php echo substr(md5(rand()), 0, 6); ?>">
			&nbsp;<input type=submit value="<?php echo $locale['login']?>">
	</form>
		</center>
	</td>
</tr>
</table>
<?php
}

function check_key($key){
	global $locale;
	$res = db_check_key($key);
	if($res == 0) return '';
	if($res == 1) return 'db_error';
	if($res == 2) return 'key_invalide';
	if($res == 3) return 'test_invalide';
	return 'unknown_error';
}

?>
