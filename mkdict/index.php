<?php 

include("inc/config.php");
require_once("inc/db.php");
include("inc/login.php");
include("inc/anketa.php");
include("inc/collector.php");
require_once("inc/log.php");

session_start();

if(isset($_POST['i_lang']) && in_array($_POST['i_lang'], $valide_lang)){
	$_SESSION['i_lang'] = $_POST['i_lang'];
}else{
	if(!session_is_registered('i_lang') ||  !in_array($_SESSION['i_lang'], $valide_lang)){
		$_SESSION['i_lang'] = $valide_lang[0];
	}
}



if(!isset($_SESSION['test_id']) || ($_SESSION['test_id']<13 && $_SESSION['test_id']>19)){
	echo "<html><body onLoad=\"window.location='/experiment'\"></body></html>";
	return;
}

if($_SESSION['test_id'] == 33) $_SESSION['i_lang'] = 'rf'; // special case

include("inc/lang_".$_SESSION['i_lang'].".php");

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<table width="100%" height="100%" border="0">
<tr height="100%">
	<td valign="center">
		<center>

<?php
if(!session_is_registered('stage')){
	$_SESSION['stage']=0; // login
}

$err = "";

write_log("stage={$_SESSION['stage']}");

if($_SESSION['stage'] == 0){
	if($_POST['key']){
		$err = check_key($_POST['key']);
		if($err == ''){
			$_SESSION['stage'] = 1; // anketa
			$_SESSION['key'] = $_POST['key'];
			$_SESSION['test_lang'] = $Last_key_lang;
//			$_SESSION['test_id'] = $Test_id;
			write_log("auth OK");
		}else{
			login_form($err);
			write_log("auth failed: ".$err);
		}
	}else{
		login_form('');
		write_log("Print login form");
	}
}

if($_SESSION['stage'] == 1){
	$age = "";
	$sex = "";
	$edu = "";
	$lang = "";
	$spec = "";
	$city = "";
	if(isset($_POST['submit'])){
		unset($_POST['submit']);
		if($_POST['age'] == ''){
			$age=""; $err .= $locale['age_empty']." ";
		}else{
			if($_POST['age'] <= 90 || $_POST['age'] >= 3){	
				$age = $_POST['age'];
			}
			else{ $age="";				$err .= $locale['age_failed']; }
		}
		if($_POST['sex'] == 'M')	$sex = 'true';
		else						$sex = 'false';
		if($_POST['spec'] != '')	$spec = $_POST['spec']; 
		else						$err .= "<br>".$locale['spec_failed'];
		if($_POST['city'] != '')	$city = $_POST['city']; 
		else						$err .= "<br>".$locale['city_failed'];
		
		if($_POST['lang_n'] == 'oo'){
			if($_POST['other'] == '') $err .= "<br>".$locale['lang_failed'];
			else					  $lang = $_POST['other'];
		}else
			$lang = $_POST['lang_n']; 
		$edu = $_POST['edu'];
		
		if($err == ''){
			$_SESSION['stage'] = 2; //collection
			$_SESSION['age'] = $age;
			$_SESSION['sex'] = $sex;
			$_SESSION['edu'] = $edu;
			$_SESSION['spec'] = $locale[$spec];
			$_SESSION['city'] = $city;
			$_SESSION['lang_n'] = $lang;
			$_SESSION['region'] = $_POST['region'];
			write_log("Anketa filled out OK");
		}else{
			write_log("Anketa filled out with errors: ".$err);
			anketa_form($err);
		}
	}else{
		write_log("Print anketa");
		anketa_form('');
	}
}

if($_SESSION['stage'] == 2){
	if(isset($_POST['words'])){
		preg_match_all("|([0-9]+):([^;]+);|", $_POST['words'], $out, PREG_PATTERN_ORDER);
		write_log("Saved words. failed={$_POST['failed']} count=".count($out[0]));
		if($_POST['failed'] == 0){
			$err = db_save_words($_POST['count']);
			// check for error
			write_log("Saved words ".(($err == 0)? "OK":"failed ".$err));
		}
		$_SESSION['stage'] = 3; //out
	}else{
		collect_form();
		write_log("Print collection form");
	}
}

if($_SESSION['stage'] == 3){
	thank_form();
	write_log("Print thanks form");
	session_unset();
	session_destroy();
}

?>
		</center>
	</td>
</tr>
<tr><td align=center>Designed by <a href="http://ccfit.nsu.ru/arom/">ARom</a></td></tr>
</table>
</body>
</html>

