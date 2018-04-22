<?php

$test = 12;
if(isset($_COOKIE["test"]))
	$test = $_COOKIE["test"];

$dict = $_COOKIE["dict"];
if(isset($_GET["dict"])) $dict = $_GET["dict"];
if($dict !="right" && $dict !="back" && $dict != "list") $dict="none";

$s_cr = new s_criteria_class();
if(isset($_COOKIE["{$dict}_{$test}_s_criteria"])){
    $s_cr->parse($_COOKIE["{$dict}_{$test}_s_criteria"]);
}
//var_dump($_COOKIE);
?>
<form class="ui form" action="" id="search_creteria">
    <table width=100% border=0 class="criteria">
	<tr>
		<td valign=top align="center"><?php echo $locale['sex']; ?> :</td>

		<td valign=top>
			<div class="ui radio checkbox">
				<input type=radio name="sex" value='M' <?php echo($s_cr->sex == 'M')?"checked":""; ?>/>
				<label><?php echo $locale['sex_m']; ?></label>
			</div><br>
			<div class="ui radio checkbox">
				<input type=radio name="sex" value='F' <?php echo($s_cr->sex == 'F')?"checked":""; ?>/>
				<label><?php echo $locale['sex_f']; ?></label>
			</div><br>
			<div class="ui radio checkbox">
				<input type=radio name="sex" value='E' <?php echo($s_cr->sex == 'E' || ($s_cr->sex != 'M' && $s_cr->sex != 'F'))?"checked":""; ?>>
				<label><?php echo $locale['donotcare']; ?></label>
			</div><br>
		</td>

		<td valign=top align="center"><?php echo $locale['edu']; ?> :</td>
		<td valign=top>
			<?php
				$arr = explode(",", $s_cr->edu);
				for($i=1; $i<6; $i++){
					echo "<div class='ui checkbox'><input type=checkbox name=\"edu\" value=\"{$i}\" ";
					echo ((in_array($i, $arr))?"checked":"") ."> <label>".$locale['edu_'.$i]."</label></div><br>";
				}
			?>
		</td>
	</tr>

	<tr>
		<td colspan="4"><br></td>
	</tr>

	<tr>
		<td valign="middle" align="center"><?php echo $locale['age']; ?> :</td>
		<td valign="middle">
			<?php echo $locale['from']; ?> <div class="ui input"><input type=text name="age_from" value="<?php echo $s_cr->age_from; ?>" maxlength=2 style="width:40px"></div>
			<?php echo $locale['to'];   ?> <div class="ui input"><input type=text name="age_to" value="<?php echo $s_cr->age_to; ?>" maxlength=2 style="width:40px"></div>
		</td>

		<td valign="middle" align="center"><?php echo $locale['spec']; ?> :</td>
		<td valign="middle">
			<!--<input type=text name="spec" value="<?php echo $s_cr->spec?>">-->
                    	<select name="spec" class="ui dropdown">
                        <?php $rows = db_get_specs_list($test);
                                $k = -1;
                                for($i=0; $i<count($rows); $i++){
                                        echo "<option value=\"{$rows[$i][0]}\"";
                                        if($s_cr->spec == $rows[$i][0]) { $k =$i; echo " selected"; }
                                        echo ">{$rows[$i][0]}</option>";
                                }
                                if($k == -1){ echo "<option value=\"\" selected>{$locale['donotcare']}</option>";}
                                else{ echo "<option value=\"\">{$locale['donotcare']}</option>";}
                        ?>
                        </select>
		</td>
	</tr>

	<tr>
		<td colspan="4"><br></td>
	</tr>

	<tr>
		<td valign="middle" align="center"><?php echo $locale['lang']; ?> :</td>
		<td>
			<select name="nl" class="ui dropdown">
		<?php $rows = db_get_lang($test);
			$k = -1;
			for($i=0; $i<count($rows); $i++){
				echo "<option value=\"{$rows[$i][0]}\"";
				if($s_cr->lang == $rows[$i][0]) { $k =$i; echo " selected"; }
				echo ">";
				if(isset($locale[$rows[$i][0]])){
					echo $locale[$rows[$i][0]]." (".$rows[$i][0].")";
				}else{
					echo $rows[$i][0];
				}
				echo "</option>";
			}
			if($k == -1){ echo "<option value=\"\" selected>{$locale['donotcare']}</option>";}
			else{ echo "<option value=\"\">{$locale['donotcare']}</option>";}
		?>
		</select>
		</td>

		<td valign="middle" align="center"><?php echo $locale['city']; ?> :</td>
		<td>
			<div class="ui input">
		    <input type=text name="city" value="<?php echo $s_cr->city?>">
			</div>
		</td>
	</tr>

	<tr>
		<td colspan="4"><br></td>
	</tr>

	<tr>
	  <td></td>
	  <td></td>
	  <td valign="middle" align="center"><?php echo $locale['region']; ?> :</td>
	  <td>
	    <select name="reg" class="ui dropdown">
				<?php
					$k = -1;
	      	$i = 1;
					while(isset($locale["reg".$i])){
						echo "<option value=\"".($i)."\"";
						if($s_cr->reg == $i) { $k =$i; echo " selected"; }
							echo ">";
							echo $locale["reg".$i];
							echo "</option>";
					    $i++;
					}
					if($k == -1){ echo "<option value=\"0\" selected>{$locale['donotcare']}</optino>";}
					else{ echo "<option value=\"\">{$locale['donotcare']}</optino>";}
				?>
	    </select>
		</td>
  </tr>

	<tr>
		<td colspan="4"><br></td>
	</tr>

	<tr>
		<td valign="bottom" align="center" colspan="4">
			<button id="buttonform" value="<?php echo $locale['renew']; ?>" class="ui black button" onClick="AdvSearch_r();"><?php echo $locale['renew']; ?></button>
			<!--	<input type=button value="<?php // echo $locale['renew']; ?>" onClick="AdvSearch_r();">-->
		</td>
	</tr>

	<tr>
		<td colspan="4"><br></td>
	</tr>

</table>
		<!-- permet d'identifier la valeur de test pour faire une requête -->
    <input type="hidden" name="test" value="<?php echo $test; ?>">
		<!-- récupère les informations rentrées par l'utilisateur soit par l'input Stimuli, soit par le onClick sur les lettres -->
    <input type="hidden" name="chr" value="<?php  echo $s_cr->chr; ?>">
		<!-- récupère les informations d'un type de tri -->
		<input type="hidden" name="sort" value="<?php  echo $s_cr->sort; ?>">
</form>
