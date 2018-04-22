<?php

function write_log($str){
	global $_SESSION;
	global $_SERVER;

	$string = date("d-m-Y H:i")." {$_SERVER['REMOTE_ADDR']} '{$_SESSION['key']}' {$str}\n";
	$fh = fopen("log/log.txt", "a");
	fwrite($fh, $string);
	fclose($fh);
}

?>
