function ch_right_dict(){
        ch_right_dict_(0);
}
function ch_back_dict(){
        ch_back_dict_(0);
}
function AdvSearch_r(){
        ch_right_dict_(1);
}
function AdvSearch_rj(){
        ch_rjoint_dict_(1);
}
function AdvSearch_bj(){
        ch_bjoint_dict_(1);
}
function AdvSearch_b(){
        ch_back_dict_(1);
}

function parse_adv_form(){
    str = "&sex=";
    // search for sex
    for(i=0; i<document.forms[0].sex.length; i++){
            if(document.forms[0].sex[i].checked)
                    str += document.forms[0].sex[i].value;
    }
    // search for age
    //alert(str);
    age = document.forms[0].age_from.value;
    str += "&af=";
    if(age.length >0){
            if(age > 3 && age <= 90){
                    str += age;
            }else{
                    alert("age_from_error");
                    return "";
            }
    }
    age = document.forms[0].age_to.value;
    str += "&at="
    if(age.length >0){
            if(age > 3 && age <= 90){
                    str += age;
            }else{
                    alert("age_to_error");
                    return "";
            }
    }
    //alert(str);
    // serch for edu
    checked = 0;
    str += "&edu=";
    for(i=0; i<document.forms[0].edu.length; i++){
            if(document.forms[0].edu[i].checked){
                    if(checked == 0){
                            checked = 1;
                            str += document.forms[0].edu[i].value;
                    }else{
                            str += "," + document.forms[0].edu[i].value;
                    }
            }
    }
    // search for spec
    //spec = document.forms[0].spec.value;
    //if(spec.length >0){
    //        str += "&spec=" + spec.toLowerCase();
    //}
    str += '&spec=';
    str += document.forms[0].spec.value;

    // search for city
    var city_o = document.forms[0].city;
    //alert(city_o);
    //alert(city_o.value);
    //if(city_o.value.length >0){
            str += "&city=" + city_o.value.toLowerCase();
    //}
    //if(document.forms[0].base.checked){
     //       str += "&base=1";
    //}else{
            str += "&base=0";
    //}
    //if(document.forms[0].nl.value != ""){
            str += '&nl=';
            str += document.forms[0].nl.value;
    //}
    //if(document.forms[0].chr.value != ""){
            str += '&chr=';
            str += document.forms[0].chr.value;
    //}
            str += '&reg=';
            str += document.forms[0].reg.value;

            str += '&sort=';
            str += document.forms[0].sort.value;
    //Retourne la string qui permet de générer la requête
    return str;
}

function dict_request(str){
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null){
            alert ("Your browser does not support AJAX!");
            return;
    }
    var url="/include/dict_queries.php?"+str;
    //alert(url);
    xmlHttp.onreadystatechange=stateChanged_data;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function chDict(ch){
  //Récupère la chaine rentrée en param et la colle dans la balise <input name="chr" value="" type="hidden"> dans le form <form action="">
    document.forms[0].chr.value = ch;
    //document.forms[index] renvoi le formulaire numéro "index" de la page.
    //document.forms[index].name.attribute renvoi la valeur de "attribute", de l'élément qui possède le nom "name", dans le formulaire numéro "index" de la page.
    AdvSearch_r();
}

function chDict_rjoint(ch){
    document.forms[0].chr.value = ch;
    AdvSearch_rj();
}

function chDict_bjoint(ch){
    document.forms[0].chr.value = ch;
    AdvSearch_bj();
}

function chDict_st(from ,to){
	str = 'test=';
	str += document.forms[0].test.value;
	str += parse_adv_form();
	str += '&st=1&srf='+from+'&srt='+to;
	dict_request(str);
}

function chDict_rs(from ,to){
	str = 'test=';
	str += document.forms[0].test.value;
	str += parse_adv_form();
	str += '&rs=1&srf='+from+'&srt='+to;
	dict_request(str);
}

function chDictj_st(zz){
	str = 'test=-1&jdict=back';
	str += parse_adv_form();
	str += '&st=1';
	dict_request(str);
}

function chDictj_rs(zz){
	str = 'test=-1&jdict=back';
	str += parse_adv_form();
	str += '&rs=1';
	dict_request(str);
}

//récupère sous forme normalisées les variables utiles à la requête faites dans dict_request
function ch_right_dict_(adv){
    str = 'test=';
    str += document.forms[0].test.value;
    if(adv){
            str += parse_adv_form();
    }// if adv
    dict_request(str);
}

function ch_rjoint_dict_(adv){
    str = 'jdict=right&test=';
    str += document.forms[0].test.value;
    if(adv){
            str += parse_adv_form();
    }// if adv
    dict_request(str);
}

function ch_bjoint_dict_(adv){
    str = 'jdict=back&test=';
    str += document.forms[0].test.value;
    if(adv){
            str += parse_adv_form();
    }// if adv
    dict_request(str);
}

function ch_list_dict_(adv){
    var obj;
    obj = document.getElementById('anketa');
    str = 'test=';
    str += document.forms[0].test.value;
    str += "&ank="+obj.innerHTML;

    if(adv){
            str += parse_adv_form();
    }// if adv
    dict_request(str);
}

//function ch_back_dict_(adv){
//    str = '&test=';
//    str += document.forms[0].test.value;
//    if(adv){
//            str += parse_adv_form();
//    }// if adv
//    s_on_add(0, 'back', str);
//}

function edit_city(city){
    var tmp = "";
    tmp = window.prompt("Substitute all '"+city+"' with", city);
    if(tmp != null){
            str = '&test=';
            str += document.forms[0].test.value;
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
        str1 = "&test="+document.forms[0].test.value+"&act=chr&f="+cname.replace(/'/g, "\\'")+"&t=";

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
                str += document.forms[0].test.value;
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
        str += document.forms[0].lang.value;
        str += '&descr=';
        str += document.forms[0].descr.value;
        s_on_add(2, 'tlist', str);
}

function del_test(id){
        str = '&del_test=1&test=';
        str += id;
        s_on_add(2, 'tlist', str);
}

function gen_keys(){
        str = '&lang=';
        str += document.forms[0].lang.value;
        str += '&num=';
        str += document.forms[0].nkeys.value;
        s_on_add(1, 'unused', str);
}
function ch_cities(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'city', str);
}

function ch_langs(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'lang', str);
}

function ch_specs(){
        str = '&test=';
        str += document.forms[0].test.value;
        s_on_add(3, 'spec', str);
}

function s_on(id, str){
        s_on_add(id, str, "");
}
function s_on_add(id, str, add){
//        var obj;
//        var m_ids = new Array(
//                                                new Array('right', 'back'),
//                                                new Array('used', 'unused'),
//                                                new Array('tlist', 'tlist'),
//                                                new Array('city', 'lang', 'spec')
//                                        );
//        for(i=0; i<m_ids.length; i++){
//                obj = document.getElementById('sm_'+m_ids[id][i]);
//                if(obj) { obj.className = "smenu_def"; }
//        }
//        obj = document.getElementById('sm_'+str);
//        obj.className = "smenu_act";
        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null){
                alert ("Your browser does not support AJAX!");
                return;
        }
        var url="include/dict_queries.php?p="+str + add;
        alert(url);
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
function getAnketa(diff){
    var obj1;
    var obj2;
    obj1 = document.getElementById('anketa');
    obj2 = document.getElementById('anketas');
    //alert(obj1);
    //alert(obj2);
    chg = 0;
    if(obj1 && obj2){
        prev = parseInt(obj1.innerHTML);
        nAnk = parseInt(obj2.innerHTML);
        res = prev + diff;
        //alert(res);
        if(res>0 && res <= nAnk){
            obj1.innerHTML = res;
        }
        if(res<=0){
            obj1.innerHTML = "1";
        }
        if(res>nAnk){
            obj1.innerHTML = nAnk;
        }
        if(prev != parseInt(obj1.innerHTML)){
            chg = 1;
        }
    }else{
        alert("Javascript error");
    }
    if(chg){
        ch_list_dict_(1);
    }
}

function getCookie(name) {
        var prefix = name + "="
        var cookieStartIndex = document.cookie.indexOf(prefix)
        if (cookieStartIndex == -1)
                return null
        var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length)
        if (cookieEndIndex == -1)
                cookieEndIndex = document.cookie.length
        return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex))
}

//function setVal(str1){
//    var cookie = getCookie(str1+"_s_criteria");
//    var f = document.forms[0];
//    if(cookie == null) return;
//    var m = cookie.toString().split(":");
//    if(m[0] == "M") f.sex[0].checked = true;
//    if(m[0] == "F") f.sex[1].checked = true;
//    if(m[0] == "E") f.sex[2].checked = true;
//    f.age_from.value = m[1];    f.age_to.value = m[2];
//    var e = m[3].split(",");
//    for(i=0; i<f.edu.length; i++){ f.edu[i].checked = false;}
//    for(i=0; i<e.length; i++)
//       f.edu[e[i]].checked = true;
//    f.spec.value = m[4];
//    f.city.value = m[5];
//    f.nl.value = m[6];
//    f.base.checked = (m[7])?true:false;
//    f.reg.value = m[9];
//}

function my_print(str){
   var html = "<html><head>"+
        "<title>Search results</title>"+
        "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"+
        "<link rel=\"stylesheet\" type=\"text/css\" href=\"semantic.css\"/>"+
        "<script type=\"text/javascript\" language=\"JavaScript1.2\">"+
        "function setVal(cookie){"+
        "var f = document.forms[0];"+
        "if(cookie == null) return;"+
        "var m = cookie.toString().split(\":\");"+
        "if(m[0] == \"M\") f.sex[0].checked = true;"+
        "if(m[0] == \"F\") f.sex[1].checked = true;"+
        "if(m[0] == \"E\") f.sex[2].checked = true;"+
        "f.age_from.value = m[1];    f.age_to.value = m[2];"+
        "var e = (typeof m[3] != 'undefined') ? m[3].split(\",\"):''"+
        "for(i=0; i<f.edu.length; i++){ f.edu[i].checked = false;}"+
        "for(i=0; i<e.length; i++)"+
        "   f.edu[e[i]-1].checked = true;"+
        "f.spec.value = m[4];"+
        "f.city.value = m[5];"+
        "f.nl.value = m[6];"+
        //"f.base.checked = (m[7]==1)?true:false;"+
        "f.reg.value = m[9];"+
        "}"+

        "</script>"+
        "</head>"+
        "<body onLoad=\"javascript:setVal('"+getCookie(str+"_s_criteria")+"'); window.print();\">"+
        "<form>"+document.getElementById("s_criteria").innerHTML+"</form>"+
        document.getElementById("results").innerHTML+
        "</body></html>";
    var win = window.open("", "Search results");
    win.document.open();
    win.document.write(html);
    win.document.getElementById("buttonform").setAttribute("onclick","");
    win.document.getElementsByClassName("stackable")[0].className = "ui fixed large celled table";
    win.document.close();
}

function search_word(){
	document.forms[0].chr.value = document.forms[1].stimul.value;
	AdvSearch_r();
}

function search_jword(){
	document.forms[0].chr.value = document.forms[1].stimul.value;
	AdvSearch_rj(1);
}

$(function() {
$('#autocomplete').autocomplete({
       source: function(request, response) {
            $.ajax({
                url: '/include/dict_queries.php',
                dataType: "json",
                data: {
                    term : request.term,
                    dict : 'right'
                },
                success: function(data) {
                    response($.map(data, function(item){
                          return { label: item.word   }
                    }));
                }
            });
        },
        select: function(event,ui){
			document.forms[0].chr.value = ui.item.value;
			$('#autocomplete').val(ui.item.value);
			search_jword();
		}

      });
});

/****************************************************** Lucas part internship ******************************************************/

/*
 * Author : Lucas Pauzies
 * Date : 10/07/2017
 * Input : None
 * Output : erase value of stimulus
 */

function erase_stimulus() {
  document.getElementById("stimul_input").value="";
}

/*
 * Author : Lucas Pauzies
 * Date : 10/07/2017
 * Input : inputStr a string variable
 * Output : return true if inputStr is empty
 */

function isEmpty(inputStr) {
  return (inputStr == null || inputStr == "");
}

/*
 * Author : Lucas Pauzies
 * Date : 10/07/2017
 * Input : current_element, the current element of Dom
 * Output : return Array[array_numbers, word_list]
 *  array_numbers => array qui contient les statistiques de la série de données
 *  word_list => array d'objets pour utiliser le framework jQCloud
 */

/* WORD CLOUD */

function data_Treatment_WordCloud(current_element) {
  var datas = current_element.cells[2].innerHTML;
  // Traitement des données
  datas = datas.replace(/ <b>/g,"/").replace(/<\/b>/g,""); //On vire les <b> et </b>
  var arr = datas.split("<br>");
  var numbers = arr[arr.length-1]; // Récupère les nombres pour les statistiques
  var str_datas = arr[0]; //Récupère la chaine de données
  var arr_datas = str_datas.split(";");
  var array_size = [36,33,30,27,24,21,18,15,12,9]; //Pour setup les tailles de police du word cloud
  var word_list = [];
  for (var i = 0; i < arr_datas.length; i++) {
      arr_datas[i] = arr_datas[i].split('/');
      //arr_datas[i][0] => words, arr_datas[i][1] => number of time that appears
      var words = arr_datas[i][0].replace(" ","").split(", ");
      //On récupère le nombre d'apparition des mots contenus dans le tableau
      var nb_apparition_mot = arr_datas[i][1];
      //On définit un nombre de mots maximum par taille pour éviter de surcharger le word cloud
      var nb_max_mots_meme_categorie = 15;
      //Pour chaque mot apparaissant un même nombre de fois
      if (words.length <= nb_max_mots_meme_categorie) {
        var lengthwords = words.length;
      } else {
        var lengthwords = nb_max_mots_meme_categorie;
      }
      for (var j = 0; j < lengthwords; j++) {
        //On crée un nouvel objet de type : {text:"mot",weight:TAILLE}
        if (i >10) {
          var object = {text:"<span class='default_cursor' data-tooltip='"+nb_apparition_mot+" times'>"+words[j]+"</span>", weight: 9};
        } else {
          var object = {text:"<span class='default_cursor' data-tooltip='"+nb_apparition_mot+" times'>"+words[j]+"</span>", weight: array_size[i]};
        }
        //On ajoute ça dans le tableau créé au début qui est normalisé pour le Framework JQCloud
        word_list.push(object);
      }
  }
  numbers = numbers.replace(')','').replace('(','');
  var array_numbers = numbers.split(',');
  return [array_numbers,word_list];
}

/*
 * Author : Lucas Pauzies
 * Date : 10/07/2017
 * Input : Dom element from RightDict
 * Output : WordCloud contained on the third <td> of Input Dom element
 */

function showWordCloudRightDict(current_element) {
  var arr = data_Treatment_WordCloud(current_element);
  var array_numbers = arr[0];
  var word_list = arr[1];
  /* Display des Statistiques du Stimulus */
  if (!(isEmpty(array_numbers[0]))) {
    document.getElementById("tot_responses").innerHTML = array_numbers[0];
  } else {
    document.getElementById("tot_responses").innerHTML = "Non défini";
  }
  if (!(isEmpty(array_numbers[1]))) {
    document.getElementById("dif_responses").innerHTML = array_numbers[1];
  } else {
    document.getElementById("dif_responses").innerHTML = "Non défini";
  }
  if (!(isEmpty(array_numbers[2]))) {
    document.getElementById("ref_responses").innerHTML = array_numbers[2];
  } else {
    document.getElementById("ref_responses").innerHTML = "Non défini";
  }
  if (!(isEmpty(array_numbers[3]))) {
    document.getElementById("uni_responses").innerHTML = array_numbers[3];
  } else {
    document.getElementById("uni_responses").innerHTML = "Non défini";
  }
  document.getElementById("stimulus_wordcloud").innerHTML = current_element.cells[1].innerHTML;
  //Pour éviter le stacking des nuages de mots
  document.getElementById("wordcloudRightDict").innerHTML = "";
  //Pour appeler le nuage de mots
  $('#wordcloudRightDict').jQCloud(word_list);
  //Pour montrer le popup
  $('#popup_wordcloud_rightdict').modal('show');
}

/*
 * Author : Lucas Pauzies
 * Date : 10/07/2017
 * Input : Dom element from BackDict
 * Output : WordCloud contained on the third <td> of Input Dom element
 */

function showWordCloudBackDict(current_element) {
  var arr = data_Treatment_WordCloud(current_element);
  var array_numbers = arr[0];
  var word_list = arr[1];
  /* Display des Statistiques du Stimulus */
  if (!(isEmpty(array_numbers[0]))) {
    document.getElementById("abs_occurences").innerHTML = array_numbers[0];
  } else {
    document.getElementById("abs_occurences").innerHTML = "Non défini";
  }
  if (!(isEmpty(array_numbers[1]))) {
    document.getElementById("sti_provoked").innerHTML = array_numbers[1];
  } else {
    document.getElementById("sti_provoked").innerHTML = "Non défini";
  }
  document.getElementById("reponse_wordcloud").innerHTML = current_element.cells[1].innerHTML;
  //Pour éviter le stacking des nuages de mots
  document.getElementById("wordcloudBackDict").innerHTML = "";
  //Pour appeler le nuage de mots
  $('#wordcloudBackDict').jQCloud(word_list);
  //Pour montrer le popup
  $('#popup_wordcloud_backdict').modal('show');
}

/* CHART GRAPH */

function stringTreatment(string) {
  var arr_mots_nb = string.split("\n");
  var datas = arr_mots_nb[0].split(";");
  var len = datas.length;
  var datas_formalisees = [];
  for (var i = 0; i < len; i++) {
    datas_formalisees[i] = get_datas_formalisees(datas[i]);
    datas_formalisees[i][0] = remove_spaces_begin_end(datas_formalisees[i][0]);
    datas_formalisees[i][0] = datas_formalisees[i][0].split(", ");
  }
  /* datas_formalisees = Array [ Array[ Array[mot1,mot2,mot3,mot4,..,motn], Nombre ], Array[ Array[mot1,mot2,mot3,mot4,..,motn], Nombre ],
    ..., Array[ Array[mot1,mot2,mot3,mot4,..,motn], Nombre ] ] */
  var stats = arr_mots_nb[1].replace(" [",", ").replace("]","").replace(/ /g,"").replace("(","").replace(")","").split(",");
  /* Array [ nb1, nb2, pourcentage, nb3, nb4 ]; */
  return [datas_formalisees, stats];
}

/*
 * Author : Lucas Pauzies
 * Date : 11/07/2017
 * Input : string au format : "mot1, mot2, mot3, ... motN <space>nb"
 * Output : ["mot1,mot2,mot3","nb"]
 */

function get_datas_formalisees(string) {
  var i = string.length-1;
  var current_letter = "";
  var stack = "";
  do {
    current_letter = string[i];
    stack = current_letter + stack;
    i = i-1;
  } while (((current_letter != " ") && (i !== 0)));
  stack = stack.replace(" ","");
  return [string.replace(stack,""),stack];
}

/*
 * Author : Lucas Pauzies
 * Date : 11/07/2017
 * Input : string
 * Output : string sans espace au début et à la fin => " aimer " renvoie "aimer"
 */

function remove_spaces_begin_end(string) {
  if (string[0] == " ") {
    string = string.replace(" ","");
  }
  if (string[string.length-1] == " ") {
    string = string.substring(0,string.length-1);
  }
  return string;
}

/************************************ BEGIN JSON BUILD ************************************/

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : stimulus STRING, fontSize INT
 * Output : Object that represent the title of the chart
 */

function objectBuildTitle(stimulus, fontSize) {
  var title = {
    text : stimulus.toUpperCase(),
    fontSize : fontSize,
    padding : 20
  };
  return title;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : animationBoolean BOOLEAN
 * Output : BOOLEAN that verify if the framework displays animation
 */

function objectBuildAnimation(animationBoolean) {
  return animationBoolean;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : colorGrid STRING couleur de la grille, colorTick STRING couleur de de l'axe
 * Output : object that represents Axis X
 */

function objectBuildAxisX(colorGrid, colorTick) {
  var axisX = {
    gridColor : colorGrid,
    tickColor : colorTick,
    labelFormatter : function(e) {
      return e.value;
    },
    margin : 20,
    interlacedColor : "#E1FCFF"
  };
  return axisX;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : sharedBoolean BOOLEAN
 * Output : object that represents the display of the Box
 */

function objectBuildToolTip(sharedBoolean) {
  var toolTip = {
    shared : sharedBoolean,
    animationEnabled : true
  };
  return toolTip;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : themeString STRING
 * Output : STRING that identify what is the theme to use
 */

function objectBuildTheme(themeString) {
  return themeString;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : colorGrid STRING couleur de la grille, colorTick STRING couleur de de l'axe
 * Output : object that represents Axis Y
 */

function objectBuildAxisY(colorGrid, colorTick) {
  var axisY = {
    gridColor : colorGrid,
    tickColor : colorTick,
    margin : 20
  };
  return axisY;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : vertical STRING verticalPosition, horizontal STRING horizontalPosition
 * Output : object that represents the position of the legend
 */

function objectBuildLegend(vertical,horizontal) {
  var legend = {
    verticalAlign : vertical,
    horizontalAlign : horizontal
  };
  return legend;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : displayType STRING type of chart, displayInLegend BOOLEAN verifiy if it's displayed in legend, dotType STRING type of marker for a point,
 * arr_colors ARRAY that contains colors of the chart, arr_names ARRAY that contains legend datas, arr_datas ARRAY that contains datas to treat
 * askJoint BOOLEAN that verify if the user wants to display joint chart
 * Output : object that represents all the line of the chart
 */

function objectBuildData(displayType, displayInLegend, dotType, arr_colors, arr_names, arr_datas, askJoint) {
  var data = [];
  //parcours du tableau de données pour chaque pays
  for (var i = 0; i < arr_datas.length; i++) {
    var objectData = {
      type : displayType,
      showInLegend : displayInLegend,
      name : arr_names[i],
      markerType : dotType,
      color : arr_colors[i],
      dataPoints: objectBuildDataPoints(arr_datas[i])
    };
    data.push(objectData);
  }
  //If the user wants to display Joint, let's return it else just delete the first elem which represents Joint data serie
  if (askJoint) {
    return data;
  } else {
    data.shift();
    return data;
  }
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : arr_datas ARRAY that contains datas for one country => Array [ Array[2], Array[2], Array[2], Array[2], Array[2], Array[2], Array[2], Array[2], Array[2], Array[2], 361 more… ]
 * Output : ARRAY that contains each point for the data serie
 */


function objectBuildDataPoints(current_array) {
  var dataPoints = [];
  for (var j = 0; j < 50; j++) {
  //Pour chaque mot du tableau courant
    if (current_array[j] != null) {
      if (isNaN(parseInt(current_array[j][1]))) {
        var point = {
          x : j,
          label : current_array[j][0],
          y : 1
        };
        dataPoints.push(point);
      } else {
        var point = {
          x : j,
          label : current_array[j][0],
          y : parseInt(current_array[j][1])
        };
        dataPoints.push(point);
      }
    } else {
    var pointNull = {
      x : j,
      label : null,
      y : 0
    };
    dataPoints.push(pointNull);
    }
  }
  return dataPoints;
}

/*
 * Author : Lucas Pauzies
 * Date : 14/07/2017
 * Input : CF each functions above
 * Output : JSON for the framework
 */

function jsonBuild(stimulus, fontSize, animationBoolean, colorGridX, colorTickX, sharedBoolean, themeString, colorGridY, colorTickY, vertical, horizontal, displayType, displayInLegend, dotType, arr_colors, arr_names, arr_datas, askJoint) {
  var json = {
    title : objectBuildTitle(stimulus, fontSize),
    animationEnabled : objectBuildAnimation(animationBoolean),
    axisX : objectBuildAxisX(colorGridX, colorTickX),
    toolTip : objectBuildToolTip(sharedBoolean),
    theme : objectBuildTheme(themeString),
    axisY : objectBuildAxisY(colorGridY, colorTickY),
    legend : objectBuildLegend(vertical, horizontal),
    data : objectBuildData(displayType, displayInLegend, dotType, arr_colors, arr_names, arr_datas, askJoint)
  };
  return json;
}

/************************************ END JSON BUILD ************************************/

/************************************ BEGIN DATA NORMALIZATION ************************************/

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : [ [mot1, mot2, mot3, mot4, ..., motN], Nb ]
 * Output : [ [mot1, Nb], [mot2, Nb], [mot3, Nb], [mot4, Nb], ... [motN, Nb] ]
 */

function fit(array_mots_nb) {
  var array_mots = array_mots_nb[0];
  var len = array_mots.length;
  var nb = array_mots_nb[1]
  var res = [];
  for (var i = 0; i < len; i++) {
    var array_inter = [array_mots[i],nb];
    res.push(array_inter);
  }
  return res;
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : [ [ CONTENT 1 ], [ CONTENT 2 ], [ CONTENT 3] ] || [ [ [ CONTENT 1 ], [ CONTENT 2 ], [ CONTENT 3] ], [ [ CONTENT 4 ], [ CONTENT 5 ], [ CONTENT 6] ] ]
 * Output : [ CONTENT 1, CONTENT 2, CONTENT 3 ] || [ [ CONTENT 1 ], [ CONTENT 2 ], [ CONTENT 3 ], [ CONTENT 4 ], [ CONTENT 5 ], [CONTENT 6 ] ]
 */

function flatten(array_of_array) {
  return array_of_array.reduce(function(listLeft, listRight) {return listLeft.concat(listRight);},[]);
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : arr_treated = [ Joint (Array[1]), Normalized (Array[4])]
           Joint : [ [mot1, nb], [mot2, nb], [mot3, nb2], [mot4, nb3], ...],
 *         Normalized : [ [ [mot1, nb11], [mot3, nb12], ...], [ [mot2, nb], [mot3, nb2], [mot4, nb3], ...] ]
 * Output : Normalized sort array [ [[mot1, nb11], null, [mot3, nb12], ...], [ null, [mot2, nb], [mot3, nb2], [mot4, nb3], ...] ]
 * Fix issue : Pointer problem, use of the JSON.parse(JSON.stringify(ARRAY)) to make copies and avoid references problems
 */

function sortChart(arr_treated) {
  //premier element = tableau auquel on se réfère, le reste ce sont des séries de stats pour les pays
  var joint_arr = JSON.parse(JSON.stringify(arr_treated.shift()));
  var country_arr = JSON.parse(JSON.stringify(arr_treated));
  //On parcourt la totalité des mots
  var len_joint_arr = joint_arr.length;
  //Pour chaque mot dans le tableau global, j compte les mots
  var array = [];
  for (var i = 0; i < country_arr.length; i++) {
    array.push([]);
  }
  //array_res = [ [], [], [], [] ];
  var res = JSON.parse(JSON.stringify(array));
  for (var j = 0; j < len_joint_arr; j++) {
    var current_word = JSON.parse(JSON.stringify(joint_arr[j][0]));
    //Pour chaque mot, on traite chaque tableau, k parcours les séries des pays
    for (var k = 0; k < country_arr.length; k++) {
      var current_country = JSON.parse(JSON.stringify(country_arr[k]));
      //Si l'index existe dans le tableau du pays courant
      var index = JSON.parse(JSON.stringify(exist(current_word, current_country)));
      if (index !== null) {
        res[k].push(JSON.parse(JSON.stringify(current_country[index])));
      } else {
        res[k].push(null);
      }
    }
  }
  return [joint_arr].concat(res);
}

/*
 * Author : Lucas Pauzies
 * Date : 12/07/2017
 * Input : the word to verify the existence, normalized_array ARRAY [ARRAY[2], ARRAY[2], ..., ARRAY[2]]
 * Output : indexOfWord if exist otherwise null
 */

function exist(word, normalized_array) {
  var len = normalized_array.length;
  var res = null;
  for (var i = 0; i < len; i++) {
    if (word == normalized_array[i][0]) {
      res = i;
      break;
    }
  }
  return res;
}

/*
 * Author : Lucas Pauzies
 * Date : 13/07/2017
 * Input : arr_datas which contains array treated before by stringTreatment()
 * Output : res ARRAY that need to be sorted to be used for framework
 */

function dataTreatmentForChart(arr_datas) {
  // on parcours le tableau suivant : [arr_joint[0], arr_france[0], arr_belgique[0], arr_suisse[0], arr_canada[0]] (sans les statistiques)
  var res = [];
  for (var i = 0; i < arr_datas.length; i++) {
    //Pour chaque tableau on crée une copie dans current_array
    var current_array = JSON.parse(JSON.stringify(arr_datas[i]));
    //On prend sa longueur
    var len_current = current_array.length;
    for (var j = 0; j < len_current; j++) {
      //On fit le tableau
      current_array[j] = fit(current_array[j]);
    }
    //On flatten le tableau grâce à une copie de celui-ci
    var flatten_array = flatten(JSON.parse(JSON.stringify(current_array)));
    res.push(flatten_array);
  }
  return res;
}

/************************************ END DATA NORMALIZATION ************************************/

/*
 * Author : Lucas Pauzies
 * Date : 27/07/2017
 * Input : stat_array = [int, int, string, int, int], arr_colors ARRAY that contains color codes, askJoint BOOLEAN that ask for the Joint data serie display
 * Output : display statistics on the popup
 */

function displayStatistics(stat_array, arr_colors, askJoint) {
  //If the user ask for Joint serie display let's begin to 0, to include the data serie Joint, else start to 1.
  if (askJoint) {
    var i = 0;
  } else {
    var i = 1;
  }
  var length = stat_array.length;
  var arrayID = ["tot_responses_chart", "dif_responses_chart", "ref_responses_chart", "uni_responses_chart"];
  var s_tot_responses_chart = "";
  var s_dif_responsesl_chart = "";
  var s_ref_responses_chart = "";
  var s_uni_responses_chart = "";
  //Parcours des statistiques de chaque pays
  while (i < length) {
    if (i != (length-1)) {
      s_tot_responses_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][0] + "</span>, ";
      s_dif_responsesl_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][1] + " (" + stat_array[i][2] + ")</span>, ";
      s_ref_responses_chart += "<span style='color:" + arr_colors[i] + "'>"  + stat_array[i][3] + "</span>, ";
      s_uni_responses_chart += "<span style='color:" + arr_colors[i] + "'>"  + stat_array[i][4] + "</span>, ";
    } else {
      s_tot_responses_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][0] + "</span>.";
      s_dif_responsesl_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][1] + " (" + stat_array[i][2] + ")</span>.";
      s_ref_responses_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][3] + "</span>.";
      s_uni_responses_chart += "<span style='color:" + arr_colors[i] + "'>" + stat_array[i][4] + "</span>.";
    }
    i++;
  }
  //Display
  document.getElementById(arrayID[0]).innerHTML = s_tot_responses_chart;
  document.getElementById(arrayID[1]).innerHTML = s_dif_responsesl_chart;
  document.getElementById(arrayID[2]).innerHTML = s_ref_responses_chart;
  document.getElementById(arrayID[3]).innerHTML = s_uni_responses_chart;
}

/*
 * Author : Lucas Pauzies
 * Date : 27/07/2017
 * Input : current_checkbox
 * Output : replace onclick property with the user choice
 */

function changeBool(checkbox) {
  var clickableTD = document.getElementsByClassName("clickable_chart");
  console.log(clickableTD);
  console.log(checkbox.checked);
  if (checkbox.checked) {
    for (var i = 0; i < clickableTD.length; i++) {
      clickableTD[i].setAttribute("onclick","showChartDirectDict(this.parentNode.parentNode, true);");
    }
  } else {
    for (var i = 0; i < clickableTD.length; i++) {
      clickableTD[i].setAttribute("onclick","showChartDirectDict(this.parentNode.parentNode, false);");
    }
  }
}

/*
 * Author : Lucas Pauzies
 * Date : 11/07/2017
 * Input : Dom elemnt from DINAF direct dictionnary
 * Output : Chart in popup
 */

function showChartDirectDict(current_element, askJoint) {
  // Stimulus
  var stimulus = current_element.cells[1].innerText;
  //Datas for chart
  var joint = current_element.cells[2].innerText;
  var france = current_element.cells[3].innerText;
  var belgique = current_element.cells[4].innerText;
  var suisse = current_element.cells[5].innerText;
  var canada = current_element.cells[6].innerText;
  var arr_joint = stringTreatment(joint);
  var arr_france = stringTreatment(france);
  var arr_belgique = stringTreatment(belgique);
  var arr_suisse = stringTreatment(suisse);
  var arr_canada = stringTreatment(canada);
  // arr_joint[0] => Array[ Array[2], Array[2], ..., Array[2]];
  // Array[2] => [ Array[n], NombreDApparitions];
  // Array[n] => array de n mots qui apparaissent NombreDApparitions fois pour le stimulus de départ
  //Setup datas to create JSON Object
  var arr_colors = ["#ff0000", "#003aff", "#ffb200", "#11bf00", "#49002c"];
  var arr_country = ["Joint", "France", "Belgique", "Suisse", "Canada"];
  var arr_datas = [arr_joint[0], arr_france[0], arr_belgique[0], arr_suisse[0], arr_canada[0]];
  var arr_stats = [arr_joint[1], arr_france[1], arr_belgique[1], arr_suisse[1], arr_canada[1]];
  //data treatment, ready to be sorted
  var treated_datas = dataTreatmentForChart(arr_datas);
  //sort of datas to be used by jsonBuild Function
  var sorted_datas = sortChart(treated_datas);
  /* function jsonBuild(stimulus, fontSize, animationBoolean, colorGridX, colorTickX, sharedBoolean, themeString, colorGridY, colorTickY, vertical, horizontal, displayType, displayInLegend, dotType, arr_colors, arr_names, arr_datas, askJoint)
  Modular function that can be easily treated by developper, you can change parameters as you can see above to change chart display and chart behaviour */
  var json = jsonBuild(stimulus, 30, true, "Silver", "silver", true, "theme2", "Silver", "silver", "center", "right", "line", true, "circle", arr_colors, arr_country, sorted_datas, askJoint);
  displayStatistics(arr_stats, arr_colors, askJoint);
  $('#popup_chart_directdict').modal('show');
  var chart = new CanvasJS.Chart("chart_directdict", json);
  chart.render();
}
