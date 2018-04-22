<?php

function keys_list($w){
	for($i=1; $i<count($w); $i++)
		$str .= $w[$i][0].",";
	return $str.$w[0][0];
}
function word_list($w){
	for($i=1; $i<count($w); $i++)
		$str .= "'".addcslashes($w[$i][1],"'")."',";
	return $str."'".addcslashes($w[0][1], "'")."'";
}

function collect_form(){
	global $locale;
	global $_SESSION;
	$w = array();
	if(isset($_SESSION['test_id']) && $_SESSION['test_id']==33) {
	    include('inc/ru_fr_ids.php');
            $res = array();
	    $k = array_keys($ru_fr);
	    for($i=0;$i<60;$i++) array_push($res,array($k[$i], $ru_fr[$k[$i]]));
	    array_splice($k, 0, 60); 
	    shuffle($k);
	    for($i=0;$i<40;$i++) array_push($res,array($k[$i], $ru_fr[$k[$i]]));
	    shuffle($res);
            $w = $res;
	} else {
	    $w = db_get_words($_SESSION['test_id']);
	} 
?>
<script language=JavaScript> 
	var w = 0;
	var n_words = <?php echo count($w); ?> ;
	var time_left_str = '<?php echo $locale['time_left']?>';
	var word_left_str = '<?php echo $locale['word_left']?>';
	var w_ids = new Array(<?php echo keys_list($w); ?>) ;
	var w_names = new Array(<?php echo word_list($w); ?>);
	var obj = null;
	var time_left = <?php if(isset($_SESSION['test_id']) && $_SESSION['test_id']==33) {echo "60*60"; }else {echo "15*60";} ?> ;
	var timerID;
	var empty = 0;

	function displ(){
		obj.innerHTML = w_names[w]+ '&nbsp;==&gt;&nbsp;<input name="resp" type=text/>'+
			'<p align=right><input type=button value="<?php echo $locale['next']?>" onClick="next_word();">';
		document.forms[0].resp.focus();
	}
	
	function getSecs(){
		time_left--;
		if(time_left == 0){
			clearTimeout(timerID);
			document.forms[0].failed.value = 1;
			document.forms[0].submit();
			return;
		}
		var to = document.getElementById('t_left');
		var tmp_m = Math.floor(time_left/60);
		var tmp_s = time_left%60;
		tmp_s = (tmp_s<10)?'0'+tmp_s:tmp_s;
		<?php if(isset($_SESSION['test_id']) && $_SESSION['test_id']!=33) { ?>
		to.innerHTML = time_left_str + ' ' + tmp_m + ':' + tmp_s;
		<?php } ?>
		timerID = setTimeout('getSecs()',1000);
	}
	
	function start_test(){
		obj = document.getElementById('data');
		if(!obj){ 
			alert('Test failed. contact system admin.');
			return;
		}
		document.forms[0].count.value = n_words;
		displ();
		timerID = setTimeout('getSecs()',1000);
	}
	function next_word(){
		val = "-"
		if(document.forms[0].resp.value.length == 0){
			empty ++;
		}else{
			val = document.forms[0].resp.value;
		}
		document.forms[0].words.value += w_ids[w]+':'+ val +';';
		w++;
		var to = document.getElementById('w_left');
		to.innerHTML = word_left_str + (n_words - w);
		if(w == n_words){
			clearTimeout(timerID);
			if(w*0.25 < empty)
				document.forms[0].failed.value = 1;
			document.forms[0].submit();;
			return;
		}
		displ();
	}
	function check(){
		if(w<n_words){
			next_word();
			document.forms[0].resp.focus();
			return false;
		}else{
			submit();
			return true;
		}
	}
</script>

<form method="post" onSubmit="return check();">
<input type="hidden" name="words" value="">
<input type="hidden" name="failed" value="0">
<input type="hidden" name="count" value="0">
<table border=0 width=400px height=300px class="login">
<tr>
		<td id=t_left align=right>&nbsp;</td>
</tr>
<tr>
		<td id=w_left align=right>&nbsp;</td>
</tr>
<tr>
<td id="data">
	<?php echo $locale['test_descr']; ?>
	<p align=right>
	<input type=button value="<?php echo $locale['go']; ?>" onClick="start_test();">
</td>
</tr>
</table>
</form>
<?php
}

function thank_form(){
	global $locale;
	echo $locale['thank_you'];
	if(isset($_SESSION['test_id']) && $_SESSION['test_id']==33) {
		echo "<p><input type=button onClick=\"window.location='/mkdict/redir.php?t=dfDSraSFgd4r3';\" value=\"Ok\">";
	} else {
		echo "<p><input type=button onClick=\"window.location='/experiment';\" value=\"Ok\">";
	}
}

?>
