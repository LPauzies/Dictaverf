<?php
session_start();
$t = "";
if(isset($_GET['t'])){
    $t = $_GET['t'];
    if($t == '4FRx9MlmlVNmA')
		$_SESSION["test_id"] = 13;
    elseif($t == '16IiQtRLgAYrA')
		$_SESSION["test_id"] = 14;
    elseif($t == 'OW0LU1jT7Hl9c')
		$_SESSION["test_id"] = 15;
    elseif($t == 'eMPamhq4rSWF2')
		$_SESSION["test_id"] = 16;
    elseif($t == '9npQgdtDeftpY')
		$_SESSION["test_id"] = 17;
    elseif($t == '1FhDnkdfHyHRA')
		$_SESSION["test_id"] = 18;
    elseif($t == '1LF623Vmx8Noc')
		$_SESSION["test_id"] = 19;
    elseif($t == 'IUAKiKszUxqA.')
		$_SESSION["test_id"] = 20;
    elseif($t == 'F5gWV4DJyr7aw')
		$_SESSION["test_id"] = 21;
    elseif($t == 'dfgtrbDFgd4r3')
		$_SESSION["test_id"] = 22;
    elseif($t == 'dfDSrbDFgd4r3')
		$_SESSION["test_id"] = 28;
    elseif($t == 'dfDSraSFgd4r3')
		$_SESSION["test_id"] = 33;
    else {
		echo "The link seems to be broken";
		$t = "";
	}
}
if($t != ""){
?>
<html>	
<body onLoad="document.forms[0].submit();">
<form method="post" action="index.php"></form>
</body>
</html>
<?php 
}
?>
