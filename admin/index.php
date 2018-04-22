<?php include("../include/config.php");?>
<?php include("../include/db.php");?>
<?php include("../include/header.php"); ?>
<script language="javascript"> function do_onLoad(){}</script>

<?php 

$str = "select  description, count(*)  from resp inner join dict on resp.id_w = dict.id left join tests on tests.id=test group by description, test  order  by test;";

$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
if($conn){
     $result = pg_exec ($conn, $str);
     if($result){
	echo "<table border=1><tr><td><b>Dict</b></td><td><b>Responds</b></td><td>Questinaries</td></tr>";
 	for($i=0; $i< pg_numrows($result); $i++){
        	$arr = pg_fetch_array($result, $i);
		echo "<tr><td>{$arr[0]}</td><td>{$arr[1]}</td><td>~".floor($arr[1]/100.0)."</td></tr>";
	}	
	echo "</table>";
     }else{
	echo "No data";
     }
     disconnect($conn);
}else{
	echo "Connection error: connect($db_host, $db_port, ....";
}

?>
<?php
$links=array(
   "","intro","about","aboutj","aboutj1",
           "authors","authorsj","authorsj1",
           "help","helpj","helpj1",
           "dict","dictright","dictback","dictlist",
           "joint","rjoint","bjoint",
           "joint1","rjoint1","bjoint1"
);
$bs="http://dictaverf.nsu.ru/";
print "<table>";
foreach($links as $v){
?>
<tr><td>
<?php print "{$bs}{$v}";?></td><td><div class="fb-share-button" data-href="<?php print "{$bs}{$v}";?>" data-layout="button_count" data-size="small" data-mobile-iframe="false"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php print "{$bs}{$v}";?>&amp;src=sdkpreparse">Поделиться</a></div></td><td>
<script type="text/javascript"><!--
document.write(VK.Share.button({url: "<?php print "{$bs}{$v}";?>"},{type: "round", text: "Сохранить"}));
--></script></td></tr>
<?php	
}
print "</table>";
?>
<?php 
$url="http://dictaverf.nsu.ru";
include("../include/footer.php"); ?>
