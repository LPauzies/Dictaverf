<?php

if(isset($_GET['del_keys'])) db_del_keys();

?>
<form>
		<input type=button value="<?php echo $locale['keys_delete']; ?>"
			onClick="del_used_keys();">
	
</form>
<table width="100%" border=1>
<tr><td width="50px">&nbsp;</td><td><?php echo $locale['key']; ?></td><td><?php echo $locale['key_lang']; ?></td></tr>
<?php
	$rows = db_get_used_keys();
	for($i=0; $i<count($rows); $i++){
		echo "<tr><td>".($i+1)."</td><td>{$rows[$i][0]}</td><td>{$locale[$rows[$i][1]]}</td></tr>";
	}
?>
</table>
