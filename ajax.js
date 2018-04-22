/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


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
                document.getElementById("results").innerHTML="<div class='ui active centered inline loader'></div>";
        }
        if (xmlHttp.readyState==2){
                document.getElementById("results").innerHTML="<div class='ui active centered inline loader'></div>";
        }
        if (xmlHttp.readyState==3){
                document.getElementById("results").innerHTML="<div class='ui active centered inline loader'></div>";
        }
        if (xmlHttp.readyState==4 && xmlHttp.status == 200){
          //Traitement pour optimiser la passation des données. De plus d'un Méga à quelques 200/300 Ko
          //C'est ici que je dois voir ce qu'il se passe dans la variable resp !
                var resp = xmlHttp.responseText.replace(/:([0-9]+)/g, "&nbsp;($1)");
                resp = resp.replace(/{f ([^{}]+)}/g, "<td valign=\"top\" class=\"nchk\">$1</td>");
                resp = resp.replace(/{t ([^{}]+)}/g, "<td valign=\"top\">$1</td>");
                resp = resp.replace(/{([^{}]+)}/g, "<td valign=\"top\">$1</td>");
                resp = resp.replace(/\\([0-9]+)\\/g, "<b>$1</b>");
                resp = resp.replace(/<s#([0-9ABCDEF]+)/g, "<span onClick=\"color_descr('$1',this);\" style=\"cursor:pointer;color:#$1\"");
                resp = resp.replace(/<s#([0-9ABCDEF]+)/g, "<span style=\"cursor:hand;color:#$1\"");
                resp = resp.replace(/<\/s>/g, "</span>");
                resp = resp.replace(/\|/g, "<br>");
                //alert(resp);
                document.getElementById("results").innerHTML=resp;
//              alert(xmlHttp.responseText.length+":"+resp.length);
                resp = xmlHttp.responseText.match(/<!-- ([0-9]+) -->/g);
                var obj = document.getElementById("anketas");
                if(obj) obj.innerHTML=resp[1].toString().match(/[0-9]+/);
                //alert(resp); innerHTML parsing in the data in tables
                obj = document.getElementById("anketa");
                if(obj) obj.innerHTML=resp[0].toString().match(/[0-9]+/);
        }
//        edit_reg=0;
}

function color_descr(str,obj){
//        $a = array("#FFFFFF","#863B52","#006952","#CBC3F9", // 0000, 0001, 0010, 0011
//                   "#000000","#EB5EF1","#FF0F0F","#515D10", // 0100, 0101, 0110, 0111
//                   "#00A8F2","#EC7F23","#939393","#F7BACB", // 1000, 1001, 1010, 1011
//                   "#00CA29","#CBD39C","#9BDDCC","#51488A");// 1100, 1101, 1110, 1111
        s = "";
        if(obj) s="'"+obj.innerHTML+"' presents in ";
	switch (str){
		case "863B52": s +=                            "Canada"; break;
		case "006952": s +=                    "Suisse";         break;
		case "CBC3F9": s +=                    "Suisse, Canada"; break;
		case "000000": s +=          "Belgique";                 break;
		case "EB5EF1": s +=          "Belgique, "+     "Canada"; break;
		case "FF0F0F": s +=          "Belgique, Suisse";         break;
		case "515D10": s +=          "Belgique, Suisse, Canada"; break;
		case "00A8F2": s += "France";                            break;
		case "EC7F23": s += "France, "+                "Canada"; break;
		case "939393": s += "France, "+        "Suisse";         break;
		case "F7BACB": s += "France, "+        "Suisse, Canada"; break;
		case "00CA29": s += "France, Belgique";                  break;
		case "CBD39C": s += "France, Belgique, "+      "Canada"; break;
		case "9BDDCC": s += "France, Belgique, Suisse";          break;
		case "51488A": s += "all"; break;
	}
        alert(s+" dict(s)");
}
