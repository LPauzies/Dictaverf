<?php
include("../inc/config.php");
require_once("../inc/db.php");

session_start();

if(isset($_POST['i_lang']) && in_array($_POST['i_lang'], $valide_lang)){
	$_SESSION['i_lang'] = $_POST['i_lang'];
}else{
	if(!session_is_registered('i_lang') ||  !in_array($_SESSION['i_lang'], $valide_lang)){	
		$_SESSION['i_lang'] = $valide_lang[0];
	}
}

include("../inc/lang_".$_SESSION['i_lang'].".php");

header('Content-type: text/html; charset=UTF-8');

	$reg = 1;
	$str = "var reg_list= new Array(";
	while(isset($locale["reg".$reg])){
		$str = $str."'".addcslashes($locale["reg".$reg], "'")."', ";
		$reg++;
	}
	$str = $str."'".addcslashes($locale["reg-1"], "'")."');";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="style.css" type="text/css">
<script language=Javascript>
<?php echo $str; ?>
var edit_reg=0;

function GetXmlHttpObject() {
  var xmlHttp=null;
  try {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  } catch (e) {
    // Internet Explorer
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}

function stateChanged_data() { 
	if (xmlHttp.readyState==1){
		document.getElementById("data_frame").innerHTML="Loading...";
	}
	if (xmlHttp.readyState==2){
		document.getElementById("data_frame").innerHTML="Loading...1";
	}
	if (xmlHttp.readyState==3){
		document.getElementById("data_frame").innerHTML="Loading...2";
	}
	if (xmlHttp.readyState==4){ 
		var resp = xmlHttp.responseText.replace(/:([0-9]+)/g, "&nbsp;($1)");
		resp = resp.replace(/{f ([^{}]+)}/g, "<td class=\"nchk\">$1</td>");
		resp = resp.replace(/{t ([^{}]+)}/g, "<td>$1</td>");
		resp = resp.replace(/{([^{}]+)}/g, "<td>$1</td>");
		resp = resp.replace(/\|/g, "<br>");
		document.getElementById("data_frame").innerHTML=resp;
//		alert(xmlHttp.responseText.length+":"+resp.length);
	}
	edit_reg=0;
}
	function ch_right_dict(){
		ch_right_dict_(0);
	}
	function ch_back_dict(){
		ch_back_dict_(0);
	}
	function AdvSearch_r(){
		ch_right_dict_(1);
	}
	function AdvSearch_b(){
		ch_back_dict_(1);
	}
	function parse_adv_form(){
		str = "";
		// search for sex
		for(i=0; i<document.forms[1].sex.length; i++){
			if(document.forms[1].sex[i].checked)
				str += "&sex=" + document.forms[1].sex[i].value;
		}
		// search for age
		age = document.forms[1].age_from.value;
		if(age.length >0){
			if(age > 3 && age <= 90){
				str += "&af=" + age;
			}else{
				alert(age_from_error);
				return;
			}
		}
		age = document.forms[1].age_to.value;
		if(age.length >0){
			if(age > 3 && age <= 90){
				str += "&at=" + age;
			}else{
				alert(age_to_error);
				return;
			}
		}
		// serch for edu
		checked = 0;
		for(i=0; i<document.forms[1].edu.length; i++){
			if(document.forms[1].edu[i].checked){
				if(checked == 0){
					checked = 1;
					str += "&edu="+document.forms[1].edu[i].value;
				}else{
					str += "," + document.forms[1].edu[i].value;
				}
			}
		}
		// search for spec
		spec = document.forms[1].spec.value;
		if(spec.length >0){
			str += "&spec=" + spec.toLowerCase();
		}
		// search for city
		var city_o = document.forms[1].city;
		//alert(city_o);
		//alert(city_o.value);
		if(city_o.value.length >0){
			str += "&city=" + city_o.value.toLowerCase();
		}
		if(document.forms[1].base.checked){
			str += "&base=1";
		}else{
			str += "&base=0";
		}
		if(document.forms[1].nl.value != ""){
			str += '&nl=';
			str += document.forms[1].nl.value;
		}
		return str;
	}
	function ch_right_dict_(adv){
		str = '&test=';
		str += document.forms[1].test.value;
		if(adv){
			str += parse_adv_form();
		}// if adv
		s_on_add(0, 'right', str);
	}

	function ch_back_dict_(adv){
		str = '&test=';
		str += document.forms[1].test.value;
		if(adv){
			str += parse_adv_form();
		}// if adv
		s_on_add(0, 'back', str);
	}

	function edit_city(city){
		var tmp = "";
		tmp = window.prompt("Substitute all '"+city+"' with", city);
		if(tmp != null){
			str = '&test=';
			str += document.forms[1].test.value;
			str +='&act=ch&f='+city+'&t='+tmp;
			s_on_add(3, 'city', str);
		}
	}
	function edit_region(id, name, cname){
		var obj = document.getElementById("ereg"+id);
		if(edit_reg == 1){
		    alert("You should press 'cancel' or  'submit' before continue");
		    return;
		}
		edit_reg = 1;
		
		if(!obj){
		    alert("Error1. Ask for administrator support.");
		    return;
		}
		str = "";
		for(i=0; i<reg_list.length-1; i++){
		    str += "<option value='"+(i+1)+"' ";
		    if(reg_list[i] == name) str += "selected";
		    str +=">"+reg_list[i]+"</option>";
		}
		str += "<option value='-1'";
		if(reg_list[i] == name) str += "selected";
		str +=">"+reg_list[i]+"</option>";
		str1 = "&test="+document.forms[1].test.value+"&act=chr&f="+cname.replace(/'/g, "\\'")+"&t=";
		
		obj.innerHTML="<select name='region' onChange=\"s_on_add(3, 'city','"+str1+"'+this.value);\">"+
				str+"</select> "+
			    "<input type='button' value='Cancel' "+
				    "onClick='document.getElementById(\"ereg\"+"+id+").innerHTML=\""+name+"\"; edit_reg=0;'>";
	}
	function edit_spec(spec){
		var tmp = "";
		tmp = window.prompt("Substitute all '"+spec+"' with", spec);
		if(tmp != null){
			str = '&test=';
			str += document.forms[1].test.value;
			str +='&act=ch&f='+spec+'&t='+tmp;
			s_on_add(3, 'spec', str);
		}
	}
	
	function del_used_keys(){
		str = '&del_keys=1';
		s_on_add(1, 'used', str);
	}

	function test_create(){
		str = '&lang=';
		str += document.forms[1].lang.value;
		str += '&descr=';
		str += document.forms[1].descr.value;
		s_on_add(2, 'tlist', str);
	}

	function del_test(id){
		str = '&del_test=1&test=';
		str += id;
		s_on_add(2, 'tlist', str);
	}

	function gen_keys(){
		str = '&lang=';
		str += document.forms[1].lang.value;
		str += '&num=';
		str += document.forms[1].nkeys.value;
		s_on_add(1, 'unused', str);
	}
	function ch_cities(){
		str = '&test=';
		str += document.forms[1].test.value;
		s_on_add(3, 'city', str);
	}

	function ch_langs(){
		str = '&test=';
		str += document.forms[1].test.value;
		s_on_add(3, 'lang', str);
	}

	function ch_specs(){
		str = '&test=';
		str += document.forms[1].test.value;
		s_on_add(3, 'spec', str);
	}

	function s_on(id, str){
		s_on_add(id, str, "");
	}
	function s_on_add(id, str, add){
		var obj;
		var m_ids = new Array(
							new Array('right', 'back'),
							new Array('used', 'unused'),
							new Array('tlist', 'tlist'),
							new Array('city', 'lang', 'spec')
						);
		for(i=0; i<m_ids.length; i++){
			obj = document.getElementById('sm_'+m_ids[id][i]);
			if(obj) { obj.className = "smenu_def"; }
		}
		obj = document.getElementById('sm_'+str);
		obj.className = "smenu_act";
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
  			alert ("Your browser does not support AJAX!");
  			return;
		} 
		var url="data.php?s="+id+"&p="+str+"&i_lang="
			<?php 
				echo "+'{$_SESSION[i_lang]}&".session_name()."=".session_id()."'";
			?> + add;
		xmlHttp.onreadystatechange=stateChanged_data;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
}
	function on(str){
		var obj;
		var m_ids = new Array('dict', 
		    'keys', 
		    'tests', 'lists');
		var sm_ids =  new Array('right',
		    'unused',
		    'tlist', 'city');
		var id = 0;
		var str1 = 'unused';
		for(i=0; i<m_ids.length; i++){
			obj = document.getElementById(m_ids[i]);
			obj.style.display = "none";
			obj = document.getElementById('m_'+m_ids[i]);
			obj.className = "menu_def";
			if(str == m_ids[i]){
				id = i;
				str1 = sm_ids[i];
			}
		}
		obj = document.getElementById(str);
		obj.style.display = "";
		obj = document.getElementById('m_'+str);
		obj.className = "menu_act";
		s_on(id, str1);		
	}

	function showAdv(){
		var obj;
		obj = document.getElementById('adv_search');
		if(obj){
			if(obj.style.display == 'none')
				obj.style.display = '';
			else
				obj.style.display = 'none';
		}
	}
</script>
</head>
<body onLoad="on('dict')">
<div align=right>
	<form method="post">
		<?php echo $locale['ch_lang']; ?>&nbsp;<select name="i_lang" onChange="submit();">
			<option value="ru" <?php if($_SESSION['i_lang'] == 'ru') echo "selected"; ?>><?php echo $locale['ru']; ?> </option>
			<option value="fr" <?php if($_SESSION['i_lang'] == 'fr') echo "selected"; ?>><?php echo $locale['fr']; ?> </option>
			<option value="en" <?php if($_SESSION['i_lang'] == 'en') echo "selected"; ?>><?php echo $locale['en']; ?> </option>
		</select>
	</form>
	
</div>
<div class="menu">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<td class="menu_def"  id="m_dict" onClick="on('dict');"><?php echo $locale['dict']; ?></td>
	<td class="menu_def"  id="m_keys" onClick="alert('keys locked');"><?php echo $locale['keys']; ?></td> 
	<td class="menu_def"  id="m_tests" onClick="alert('tests locked');"><?php echo $locale['tests']; ?></td>
	<td class="menu_def"  id="m_lists" onClick="on('lists');"><?php echo $locale['lists']; ?></td>
	<td class="menu_empty">&nbsp;</td>
	</table>
</div>
<div class="main_frame" id="keys" style="display:none">
	<div class="sub_menu">
		<div style="height:5px;"></div>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<td class="smenu_act"  id="sm_unused" onClick="s_on(1, 'unused');"><?php echo $locale['unused']; ?></td>
		<td class="smenu_def"  id="sm_used" onClick="s_on(1, 'used');"><?php echo $locale['used']; ?></td>
		<td class="menu_empty">&nbsp;</td>
		</table>
	</div>
</div>
<div class="main_frame" id="tests" style="display:none">
	<div class="sub_menu">
		<div style="height:5px;"></div>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<td class="smenu_act"  id="sm_tlist" onClick="s_on(2,'tlist');"><?php echo $locale['tlist']; ?></td>
		<!-- <td class="smenu_def"  id="sm_tnew" onClick="s_on(2,'tnew');"><?php echo $locale['tnew']; ?></td> -->
		<td class="menu_empty">&nbsp;</td>
		</table>
	</div>
</div>
<div class="main_frame" id="dict" style="display:none">
	<div class="sub_menu">
		<div style="height:5px;"></div>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<td class="smenu_act"  id="sm_right" onClick="s_on(0, 'right');"><?php echo $locale['right']; ?></td>
		<td class="smenu_def"  id="sm_back" onClick="s_on(0, 'back');"><?php echo $locale['back']; ?></td>
		<td class="menu_empty">&nbsp;</td>
		</table>
	</div>
</div>
<div class="main_frame" id="lists" style="display:none">
	<div class="sub_menu">
		<div style="height:5px;"></div>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<td class="smenu_act"  id="sm_city" onClick="s_on(3, 'city');"><?php echo $locale['cities']; ?></td>
		<td class="smenu_act"  id="sm_lang" onClick="s_on(3, 'lang');"><?php echo $locale['lang']; ?></td>
		<td class="smenu_act"  id="sm_spec" onClick="s_on(3, 'spec');"><?php echo $locale['specs']; ?></td>
		<td class="menu_empty">&nbsp;</td>
		</table>
	</div> 
</div>
<!-- <div class="main_frame" id="lang" style="display:none">
	<div class="sub_menu">
		<div style="height:5px;"></div>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<td class="smenu_act"  id="sm_lang" onClick="s_on(4, 'lang');"><?php echo $locale['lang']; ?></td>
		<td class="menu_empty">&nbsp;</td>
		</table>
	</div> 
</div>
-->
<div class="data_frame" id="data_frame">
Loading...
</div>
</body>
</html>
