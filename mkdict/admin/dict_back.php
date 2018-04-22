<?php

//if(isset($_GET['lang']) && isset($_GET['descr']))
//	db_test_create($_GET['lang'], $_GET['descr']);

//if(isset($_GET['del_test']) && isset($_GET['test']))
//	db_test_delete($_GET['test']);


	$rows = db_get_tests();
	$test = 0;
	echo "<form><table  width=\"95%\" border=0><td>";
	echo "{$locale['test']}&nbsp;<select name=\"test\" onChange=\"ch_back_dict();\">";
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

	$test = $rows[$test][3];
?>
	</td>
	<td valign=top align=right>
	<input type=button value="<?php echo $locale['adv_search']; ?>" onClick="showAdv();">
	</td>
	</table>
<div id="adv_search" style="display:none;">
	<fieldset>
	<legend><?php echo $locale['adv_search']; ?></legend>
	<table width=100% border=0>
	<tr><td valign=top align=right><?php echo $locale['sex']; ?>:</td>
		<td valign=top>
			<input type=radio name="sex" value='M' <?php echo($_GET['sex'] == 'M')?"checked":""; ?>><?php echo $locale['sex_m']; ?><br>
			<input type=radio name="sex" value='F' <?php echo($_GET['sex'] == 'F')?"checked":""; ?>><?php echo $locale['sex_f']; ?><br>
			<input type=radio name="sex" value='E' <?php echo($_GET['sex'] == 'E' || 
				($_GET['sex'] != 'M' && $_GET['sex'] != 'F'))?"checked":""; ?>><?php echo $locale['donotcare']; ?>
		</td>
		<td valign=top align=right><?php echo $locale['edu']; ?>:</td>
		<td valign=top>
			<?php
				$arr = explode(",", $_GET['edu']);
				for($i=1; $i<6; $i++){
					echo "<input type=checkbox name=\"edu\" value=\"{$i}\" ";
					echo ((in_array($i, $arr))?"checked":"") ."> ".$locale['edu_'.$i]."<br>";
				}
			?>
		</td>
		<td valign=top><input type=checkbox name="base" <?php echo ($_GET['base'])?"checked":"" ?>> Base dict only
		</td>
		</tr>
		<tr>
		<td valign=top align=right><?php echo $locale['age']; ?>:</td>
		<td valign=top>
			<?php echo $locale['from']; ?> <input type=text name="age_from" value="<?php echo $_GET['af']?>" maxlength=2 style="width:40px"><br>
			<?php echo $locale['to']; ?> <input type=text name="age_to" value="<?php echo $_GET['at']?>" maxlength=2 style="width:40px">
		</td>
		<td valign=top align=right><?php echo $locale['spec']; ?>:</td>
		<td valign=top>
			<input type=text name="spec" value="<?php echo $_GET['spec']?>">
		</td>	
		<td>&nbsp;
		</td>
		</tr>
		<tr>
		<td valign=top align=right><?php echo $locale['lang']; ?>:</td>
		<td>
			<select name="nl">
		<?php $rows = db_get_lang($test);
			$k = -1;
			for($i=0; $i<count($rows); $i++){
				echo "<option value=\"{$rows[$i][0]}\"";
				if($_GET['nl'] == $rows[$i][0]) { $k =$i; echo " selected"; }
				echo ">";
				if(isset($locale[$rows[$i][0]])){
					echo $locale[$rows[$i][0]]." (".$rows[$i][0].")";
				}else{
					echo $rows[$i][0];
				}
				echo "</option>";
			}
			if($k == -1){ echo "<option value=\"\" selected>{$locale['donotcare']}</optino>";}
			else{ echo "<option value=\"\">{$locale['donotcare']}</optino>";}
		?>
		</select>
	</td>
		<td valign=top align=right> <?php echo $locale['city']; ?>:
			</td>
		<td>
		    <input type=text name="city" value="<?php echo $_GET['city']?>">
		</td>
		<td valign=bottom align=right>
			<input type=button value="<?php echo $locale['search']; ?>" onClick="AdvSearch_b();">
		</td>
		</tr>
	</table>
	</fieldset>
</div>
</form>

	
		
<table width="100%" border=1>
<tr><td>&nbsp;</td><td><?php echo $locale['resp']; ?></td>
	<td><?php echo $locale['stimul']; ?></td>
<?php
//	$rows = db_back_dict($rows[$test][3]);
	$rows = db_back_dict($test, $_GET['sex'], $_GET['af'], $_GET['at'], $_GET['edu'], $_GET['spec'], $_GET['city'], $_GET['base'], $_GET['nl']);
//	if (!($fp = fopen('/tmp/dict'.''.'.txt', 'w')))
//	    return;
	    
	for($i=0; $i<count($rows); $i++){
		echo "<tr>{".($i+1)."}{{$rows[$i][3]} {$rows[$i][0]}}{{$rows[$i][2]}}</tr>";
//		fwrite($fp, "<tr>{".($i+1)."}{{$rows[$i][0]}}{{$rows[$i][2]}}</tr>");
//		fwrite($fp, $rows[$i][0]."\t".$rows[$i][2]."\n");
	}
//	fclose($fp);
//	file('/tmp/dict'.''.'.txt');
//	unlink('/tmp/dict'.''.'.txt');
?>
</table>
