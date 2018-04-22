<?php
include("../inc/config.php");
require_once("../inc/db.php");

session_start();


if(isset($_GET['i_lang']) && in_array($_GET['i_lang'], $valide_lang)){
	$_SESSION['i_lang'] = $_GET['i_lang'];
}else{
	if(!session_is_registered('i_lang')){
		$_SESSION['i_lang'] = $valide_lang[0];
	}
}

include("../inc/lang_".$_SESSION['i_lang'].".php");

if($_GET['p'] == 'unused') include("keys_unused.php");
if($_GET['p'] == 'used') include("keys_used.php");
if($_GET['p'] == 'tlist') include("test_list.php");
if($_GET['p'] == 'right') include("dict_right.php");
if($_GET['p'] == 'back') include("dict_back.php");
if($_GET['p'] == 'city') include("city_list.php");
if($_GET['p'] == 'lang') include("lang_list.php");
if($_GET['p'] == 'spec') include("spec_list.php");
?>
