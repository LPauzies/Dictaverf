<?php

//Doc php
//pg_execute - exécute une requête préparée par pgSQL, param 1 : connexion, param 2 : requete SQL.
//pg_connect - établit une connexion PostgreSQL, host={$host} port={$port} dbname={$db} user={$user} password={$pass}
//pg_close - termine une connexion PostgreSQL, $conn objet de retour de pg_connect
//pg_fetch_array - lit une ligne de résultat PostgreSQL dans un tableau
//pg_num_rows - Retourne le nombre de lignes PostgreSQL

$Last_key_lang = "";
$Test_id = "";

function connect($host, $port, $db, $user, $pass, $enc){
	$conn = pg_connect("host={$host} port={$port} dbname={$db} user={$user} password={$pass}");
	echo "$conn";
	return $conn;
}

function disconnect($conn){
	@pg_close($conn);
}

function db_check_key($key){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	global $Last_key_lang;
	global $Test_id;
	$Test_id = 12;
	$Last_key_lang = 'fr';
	return 0;

	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "select lang, id from keys where used='false' and key='{$key}'");
		if(!$result) return 1;
		if(pg_numrows($result) != 1){
			disconnect($conn);
			return 2;
		}
		$res = pg_fetch_array($result, 0);
		$id = $res[1];
		$Last_key_lang = $res[0];
		$result = @pg_exec ($conn, "select max(id) from tests where lang='{$Last_key_lang}'");
		if(!$result) return 1;
		if(pg_numrows($result) != 1){
			disconnect($conn);
			return 3;
		}
		$res = pg_fetch_array($result, 0);
		$Test_id = $res[0];
		if($Test_id == ""){
			disconnect($conn);
			return 3;
		}
		$result = @pg_exec ($conn, "update keys set used='true' where id={$id}");
		if(!$result) {disconnect($conn); return 1;}
		$cmdtuples = pg_cmdtuples ($result);
		pg_freeresult($result);
		if($cmdtuples == 1) {
			disconnect($conn);
			return 0;
		}
		disconnect($conn);
		return 2;
	}else{
		return 1;
	}
	return 3;
}

function db_save_words($words){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	global $_SESSION;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	$new_user = "";
	$new_words = "";

	$new_user = "INSERT into users (age, sex, edu, spec, lang_n, key, city, id_t, region) ".
				"Values ('{$_SESSION['age']}', {$_SESSION['sex']}, {$_SESSION['edu']}, '{$_SESSION['spec']}', ".
					"'{$_SESSION['lang_n']}', '{$_SESSION['key']}', '{$_SESSION['city']}', {$_SESSION['test_id']}, {$_SESSION['region']});";
	write_log($new_user);
	preg_match_all("|([0-9]+):([^;]+);|", $_POST['words'], $out, PREG_PATTERN_ORDER);
	for($i=0; $i<count($out[0]); $i++){
			write_log("INSERT into resp (id_w, id_u, word) VALUES ({$out[1][$i]}, lid, '{$out[2][$i]}');");
	}


	if($conn){
		preg_match_all("|([0-9]+):([^;]+);|", $_POST['words'], $out, PREG_PATTERN_ORDER);
		$result = @pg_exec ($conn, "BEGIN WORK");
		if(!$result){disconnect($conn); return 5;}
		pg_freeresult($result);

		$str = "INSERT into users (age, sex, edu, spec, lang_n, key, city, id_t, region) ".
				"Values ('{$_SESSION['age']}', {$_SESSION['sex']}, {$_SESSION['edu']}, '{$_SESSION['spec']}', ".
					"'{$_SESSION['lang_n']}', '{$_SESSION['key']}', '{$_SESSION['city']}', {$_SESSION['test_id']}, {$_SESSION['region']})";
		//echo $str;
		$result = @pg_exec ($conn, $str);
		if(!$result){ @pg_exec ($conn, "ROLLBACK"); disconnect($conn); return 4;}

		$result = @pg_exec ($conn, "select max(id) from users where key='{$_SESSION['key']}'");
		if(pg_numrows($result) != 1){
			@pg_exec ($conn, "ROLLBACK");
			disconnect($conn);
			return 1;
		}
		$res = pg_fetch_array($result, 0);
		$lid = $res[0];

		for($i=0; $i<count($out[0]); $i++){
			$str = "INSERT into resp (id_w, id_u, word) ";
			$str .= "VALUES ({$out[1][$i]}, {$lid}, '".trim($out[2][$i])."')";
			$result = @pg_exec ($conn, $str);
			if(!$result){ @pg_exec ($conn, "ROLLBACK"); disconnect($conn); return 2;}
			pg_freeresult($result);
		}
		$result = @pg_exec ($conn, "COMMIT");
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_get_words($lang){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select id, word from dict where lang='{$lang}' order by random() limit 100");
		//$result = @pg_exec ($conn, "select id, word from
		//				(select dict.id as id, dict.word as word, count(resp.word) as cnt from
		//				    resp inner join dict on dict.id=resp.id_w
		//					where dict.lang='{$lang}'
		//					group by dict.word, dict.id order by cnt limit 500)
		//				as foo order by random() limit 100");
		if(!$result){
		    write_log("GET_WORDS: no words available");
		    disconnect($conn);
		    return Array();
		}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		write_log("GET_WORDS: ".pg_numrows($result)." selected");
		return $res;
	}else{
		write_log("GET_WORDS: db connection error");
		return Array();
	}
	return Array();
}

function db_get_unused_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select key, lang from keys where used='F'");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();

}

function db_get_used_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "Select key, lang from keys where used='T'");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();

}
function db_edit_city($test, $from, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set city='{$to}' where city='{$from}' and id_t={$test}");
		//echo( "update users set city=lower('{$to}') where lower(city)='{$from}' and id_t={$test}");
		//pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}
function db_edit_region($test, $for, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set region={$to} where city='{$for}' and id_t={$test}");
		//write_log("");
		//echo( "update users set city=lower('{$to}') where lower(city)='{$from}' and id_t={$test}");
		//pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_edit_spec($test, $from, $to){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "update users set spec=lower('{$to}') where lower(spec)='{$from}' and id_t={$test}");
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}
function db_gen_keys($lang, $num){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		for($i=0; $i<$num; $i++){
			$result = @pg_exec ($conn, "insert into keys (lang, key) values('{$lang}',substring(md5(RANDOM()),1,7));");
			pg_freeresult($result);
		}
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_del_keys(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){

		$result = @pg_exec ($conn, "delete from keys where used='t';");
		pg_freeresult($result);

		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_get_tests(){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "Select dstart, lang, description, tests.id, count(tests.id) ".
					    "from users left join tests on  users.id_t = tests.id ".
					    "group by  tests.id, tests.description, tests.dstart, ".
					    "tests.active, tests.lang order by dstart");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_get_stimulus($term){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select word from dict where test=24 and word like '$term%';");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_get_lang($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select lower(lang_n) as a, count(lang_n) as c from users where id_t={$test} group by a order by c desc");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}


function db_get_specs($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select lower(spec) as a, count(spec) as c from users where id_t={$test} group by a order by c desc, a");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}
function db_get_specs_list($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select lower(spec) as a from users where id_t={$test} group by a order by a");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}
function db_get_cities($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = pg_exec ($conn, "select city as a, count(city) as c, region  from users where id_t={$test} group by a, region  order by c desc, a, region");
		if(!$result){disconnect($conn); return Array();}
		for($i=0; $i< pg_numrows($result); $i++){
			array_push($res, pg_fetch_array($result, $i));
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_test_create($lang, $descr){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
			$description = addslashes($descr);
			$result = @pg_exec ($conn, "insert into tests (lang, dstart, description) values ".
					"('{$lang}', now(), '{$description}');");
			pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function db_test_delete($id){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$result = @pg_exec ($conn, "delete from tests where id={$id}");
		pg_freeresult($result);
		disconnect($conn);
		return 0;
	}else{
		return 1;
	}
	return 3;
}

function ccmp($aa, $bb){
//	preg_match("/^((un|une|le|la|les) )?(.*)$/i", $aa[0], $a);
//	preg_match("/^((un|une|le|la|les) )?(.*)$/i", $bb[0], $b);
	preg_match("/((un|une|le|la|les) )?(.*)$/i", $aa[0], $a);
	preg_match("/((un|une|le|la|les) )?(.*)$/i", $bb[0], $b);

	$patterns = array();
	$patterns[0] = '/(é|è|ê)/';
	$patterns[1] = '/(à|â)/';
	$patterns[2] = '/ô/';
	$patterns[3] = '/(î|ï)/';
	$patterns[4] = '/û/';
	$replacements = array();
	$replacements[0] = 'e';
	$replacements[1] = 'a';
	$replacements[2] = 'o';
	$replacements[3] = 'i';
	$replacements[4] = 'u';
	$aaa =  preg_replace($patterns, $replacements, $a[3]);
	$bbb =  preg_replace($patterns, $replacements, $b[3]);


	$res = strcasecmp($aaa, $bbb);
	return $res;
}

function rcmp($aa, $bb){
	if($aa[1] == $bb[1]) return 0;
	return ($aa[1] < $bb[1])? 1: -1;
}

function db_right_dict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $reg, $sort_order){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
                if(strlen($chr)>0) {
					$rest = substr(strtolower($chr), 1);
//                    if(strtolower($chr[0]) == 'e'){
//                        $search .= "AND  lower(dict.word) SIMILAR  TO '(e|é|è|ê)%'  ";
//                    }else{
//                        $search .= "AND lower(dict.word) like '".strtolower($chr[0])."%' ";
//                   }
                    if(strtolower($chr[0]) == 'e'){
                        $search .= "AND  lower(dict.word) SIMILAR  TO '((un|une|le|la|les) )*(e|é|è|ê){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'a'){
                        $search .= "AND  lower(dict.word) SIMILAR  TO '((un|une|le|la|les) )*(a|à|â){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'o'){
                        $search .= "AND  lower(dict.word) SIMILAR  TO '((un|une|le|la|les) )*(o|ô){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'c'){
                        $search .= "AND  lower(dict.word) SIMILAR  TO '((un|une|le|la|les) )*(c|ç){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'i'){
                        $search .= "AND  lower(dict.word)  SIMILAR  TO '((un|une|le|la|les) )*(i|î){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'u'){
                        $search .= "AND  (lower(dict.word) SIMILAR TO '((un|une|le|la|les) )*(u|û){$rest}%'  and ".
										"lower(dict.word) not similar to '(un|une) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == 'l'){
                        $search .= "AND  (lower(dict.word) similar to '((un|une|le|la|les) )*l{$rest}%' and ".
										"lower(dict.word) not similar to '(la|le|les) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == '?'){
                        $search .= "AND  lower(dict.word)  NOT SIMILAR  TO ".
							"'(1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%' ";
                    }else {

                        $search .= "AND lower(dict.word) similar to '((un|une|le|la|les) )*".strtolower($chr)."%' ";
                    }

                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";
                if($reg != 0) $search.= "AND users.region = {$reg} ";

		$result = @pg_exec ($conn, "select resp.word as rw, dict.word, count(resp.word) as cnt
										from resp inner join dict on dict.id=resp.id_w
											inner join users on users.id=resp.id_u
										where dict.test={$test} {$search}
										group by dict.word, rw
										order by dict.word, cnt desc, rw;");
		if(!$result){disconnect($conn); return Array();}
		$str = "";
		$word = "";
		$num = -1;
		$cnt = Array(0,0,0,0);
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
			if($word == ""){
				$word = $arr[1];
				if($arr[0] != '-'){
					$str = $arr[0];
					$num = $arr[2];
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$cnt[2] = ($arr[2] == 1)?1:0;
					$cnt[3] = 0;
				}else{
					$str = "";
					$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
					$num = $arr[2];
				}
			}else{
				if($word == $arr[1]){
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){
							$str .= " \\{$num}\\; ";
						}
						$str .= ", $arr[0]";
						$cnt[0] += $arr[2];
						$cnt[1] += 1;
						$cnt[2] += ($arr[2] == 1)?1:0;
						$num = $arr[2];
					}else{
						$cnt[0] += $arr[2];
						$cnt[3] += $arr[2];
//						$num = $arr[2];
					}
				}else{
					$str = preg_replace("/^, /", "", $str);
					$str = preg_replace("/; , /", "; ", $str);
					$str .= " \\{$num}\\";
					array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
					$num = -1;
					$word = $arr[1];
					$str = "";
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){
							$str .= " \\{$num}\\; ";
						}
						$str .= ", $arr[0]";
						$cnt[0] = $arr[2];
						$cnt[1] = 1;
						$cnt[2] = ($arr[2] == 1)?1:0;
						$cnt[3] = 0;
						$num = $arr[2];
					}else{
						$str = "";
						$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
						$num = $arr[2];
					}
				}
			}
		}
		$str = preg_replace("/^, /", "", $str);
		$str = preg_replace("/; , /", "; ", $str);
		$str .= " \\{$num}\\";
		if($word != "") array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
		if($sort_order==0) {
			usort($res, "rcmp");
		}else{
			usort($res, "ccmp");
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}


function db_back_dict($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $chr, $reg, $sort_order, $sr, $srf, $srt){
//function db_back_dict($test){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		$search1 = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if($sr == "0"){
                 if(strlen($chr)>0) {
					$rest = substr(strtolower($chr), 1);
                    if(strtolower($chr[0]) == 'e'){
                        $search .= "AND  lower(resp.word) SIMILAR  TO '((un|une|le|la|les) )*(e|é|è|ê){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'a'){
                        $search .= "AND  lower(resp.word) SIMILAR  TO '((un|une|le|la|les) )*(a|à|â){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'o'){
                        $search .= "AND  lower(resp.word) SIMILAR  TO '((un|une|le|la|les) )*(o|ô){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'c'){
                        $search .= "AND  lower(resp.word) SIMILAR  TO '((un|une|le|la|les) )*(c|ç){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'i'){
                        $search .= "AND  lower(resp.word)  SIMILAR  TO '((un|une|le|la|les) )*(i|î){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'u'){
                        $search .= "AND  (lower(resp.word) SIMILAR TO '((un|une|le|la|les) )*(u|û){$rest}%'  and ".
										"lower(resp.word) not similar to '(un|une) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == 'l'){
                        $search .= "AND  (lower(resp.word) similar to '((un|une|le|la|les) )*l{$rest}%' and ".
										"lower(resp.word) not similar to '(la|le|les) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == '?'){
                        $search .= "AND  lower(resp.word)  NOT SIMILAR  TO ".
							"'(1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%' ";
                    }else {

                        $search .= "AND lower(resp.word) similar to '((un|une|le|la|les) )*".strtolower($chr)."%' ";
                    }
                }
			}else{
	//			if($sr == 1){ // stimulus count are limited
	//				$search1 .= "HAVING ((count(dict.word) <= {$srf}) AND (count(dict.word) >= {$srt})) ";
	//			}
			}
        if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
		//Commented base dict
		//if($base == 1) $search.= "AND dict.base='T' ";
                if($reg != 0) $search.= "AND users.region = {$reg} ";
		//$search .= "AND resp.checked='f' ";

		//$result = pg_exec ($conn, "select dict.word, lower(resp.word) as rw, count(dict.word) as cnt
		$result = pg_exec ($conn, "select dict.word, resp.word as rw,  count(dict.word) as cnt, resp.checked as ch
							from resp inner join dict on dict.id=resp.id_w
								inner join users on users.id=resp.id_u
							where dict.test={$test} and resp.word<>'-' {$search}
							group by rw, dict.word, ch {$search1} order by rw, cnt desc, dict.word;");

		if(!$result){disconnect($conn); return Array();}
		$str = "";
		$word = "";
		$chk = 0;
		$cnt = Array(0,0);
		$num = -1;
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
			if($word == ""){
				$num = $arr[2];
				$word = $arr[1];
				$chk = $arr[3];
				$str = "$arr[0]";
				$cnt[0] = $arr[2];
				$cnt[1] = 1;
			}else{
				if($word == $arr[1]){
					if(($num != $arr[2]) && ($str !="")){
						$str .= " \\{$num}\\; ";
					}
					$str .= ", $arr[0]";
					$cnt[0] += $arr[2];
					$cnt[1] += 1;
					$num = $arr[2];
				}else{
					$str .= " \\{$num}\\; ";
					$str = preg_replace("/; , /", "; ", $str);
					if($sr == "0")
						array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					if($sr == 1 && $cnt[1] >= $srt && $cnt[1] <= $srf)
						array_push($res, Array($word, $cnt[1],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					if($sr == 2 && $cnt[0] >= $srt && $cnt[0] <= $srf)
						array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
					$word = $arr[1];
					$chk = $arr[3];
					$str = $arr[0];
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$num = $arr[2];
				}
			}
		}
		$str .= " \\{$num}\\; ";
		$str = preg_replace("/; , /", "; ", $str);
		if($word != ""){
			if($sr == "0")
				array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
			if($sr == 1 && $cnt[1] >= $srt && $cnt[1] <= $srf)
				array_push($res, Array($word, $cnt[1],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
			if($sr == 2 && $cnt[0] >= $srt && $cnt[0] <= $srf)
				array_push($res, Array($word, $cnt[0],  "{$str}<br>({$cnt[0]}, {$cnt[1]})", $chk));
		}
		if($sort_order == 0) {
			usort($res, "rcmp");
		}else{
			//echo "--".setlocale(LC_ALL, 'fr_FR')."==";
			//echo "--".setlocale(LC_ALL, 'fr_FR.UTF-8')."==";
			//echo "--".iconv('UTF-8', 'C', "Déjérine-Klumpke")."++";
			usort($res, "ccmp");
		}
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_get_user_ank($test, $sex, $af, $at, $edu, $spec, $city, $base, $nl, $ank, $reg){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND lower(users.lang_n) = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
		if(strlen($city)>0) $search .= "AND lower(users.city) like '".AddSlashes($city)."' ";
                if($reg != 0) $search.= "AND users.region = {$reg} ";

		$result = @pg_exec ($conn, "select id, sex, spec, lang_n, age, edu, city, region from users
                                                where users.id_t={$test} {$search}
                                                order by users.id limit 1 offset {$ank};");
                $userid = "";
		if(!$result){disconnect($conn); return Array();}
		$arr = Array();
		if( pg_numrows($result) == 1){
			$arr = pg_fetch_array($result, $i);
                        array_push($res, $arr);
                        $userid = $arr[0];
		}else{
                    return Array();
                }
                $result = @pg_exec ($conn, "select count(id) from users where users.id_t={$test} {$search};");
                if( pg_numrows($result) == 1){
			$arr = pg_fetch_array($result, $i);
                        array_push($res, $arr);
		}else{
                    return Array();
                }
//		$result = @pg_exec ($conn, "select resp.word, resp.checked, dict.word, dict.base,dict.id
//                                                from resp inner join users on users.id = resp.id_u
//                                                          inner join dict on resp.id_w=dict.id
//                                                where users.id={$userid};");
//                for($i=0; $i< pg_numrows($result); $i++){
//			$arr = pg_fetch_array($result, $i);
//                        array_push($res, $arr);
//                }
                $res1 = @pg_exec ($conn, "select dict.word, count(dict.word) from dict
                                                  left join resp on dict.id=resp.id_w
                                                  where resp.word !='-' and dict.test={$test} group by dict.word;");
		$answ = Array();
		for($i=0; $i< pg_numrows($res1); $i++){
	 		$arr = pg_fetch_array($res1, $i);
                        $answ = array_merge($answ, Array($arr[0]=>$arr[1]));
		}
		$result = @pg_exec ($conn, "select dict.word as dw, resp.word as rw , count(resp.word) as cnt
						from resp inner join dict on resp.id_w=dict.id
							where dict.test={$test} and dict.id||'_'||resp.word in
								(select dict.id||'_'||resp.word from resp
									inner join users on users.id = resp.id_u
									inner join dict on resp.id_w=dict.id
										where users.id={$userid})
							group by dw, rw order by cnt desc;");
                for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i);
			array_push($arr, pr(100*$arr[2]/$answ[$arr[0]]));
			array_push($res, $arr);
                }
		return $res;
	}else{
		return Array();
	}
	return Array();
}

function color_assignement ($w){
        $a = array("#FFFFFF","#863B52","#006952","#CBC3F9",
                   "#000000","#EB5EF1","#FF0F0F","#515D10",
                   "#00A8F2","#EC7F23","#939393","#F7BACB",
                   "#00CA29","#CBD39C","#9BDDCC","#51488A");
        return $a{$w & 15};
				//& is like modulo operator
}

function db_fjoint_dict($test, $sex, $af, $at, $edu, $spec,  $base, $nl, $chr ){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;


	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
                $search_chr = "";
                if(strlen($chr)>1) {
                     $search_chr = "AND lower(dict.word)='".strtolower($chr)."' ";
                }else{
                     $search_chr = "AND lower(dict.word) like '".strtolower($chr[0])."%' ";
                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";

                $res_com = @pg_exec ($conn, "select dict.word from dict where dict.test in (24,25,26,27) $search_chr group by dict.word order by dict.word;");

                if(!$res_com){disconnect($conn); return Array();}
		$res = Array();
		for($i=0; $i< pg_numrows($res_com); $i++){
			$arr = pg_fetch_array($res_com, $i);

                        array_push($res, Array($arr[0], "-", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;","&nbsp;"));
                }
          $response = array();
          $tl = Array();
          if($test == -1)  array_push($tl,-1,24,25,26,27);
	  if($test == -2)  array_push($tl,-1,29,30,31,32);

           foreach($tl as $test){
                $t_search = "dict.test={$test}";
                if($test == -1) $t_search = "dict.test in (".join(',',$tl).")";
		$result = @pg_exec ($conn, "select resp.word as rw, dict.word, count(resp.word) as cnt
										from resp inner join dict on dict.id=resp.id_w
											inner join users on users.id=resp.id_u
										where {$t_search} {$search_chr}
										group by dict.word, rw
										order by dict.word, cnt desc, rw;");
		if(!$result){disconnect($conn); return Array();}
	        if(pg_numrows($result)<=0){continue;}
		if( $test == -1){
			$tmp_row = pg_fetch_array($result, 0);
			$tmp_stim = $tmp_row[1];
			$tmp_stim_array = array();
			for($i=0; $i < pg_numrows($result); $i++){
				$tmp_row = pg_fetch_array($result, $i);
				if($tmp_row[1] == $tmp_stim ) {
					$tmp_stim_array{$tmp_row[0]}= 0; //array(24=>false,25=>false,26=>false,27=>false);
				} else {
					$response{$tmp_stim}=$tmp_stim_array;
					$tmp_stim = $tmp_row[1];
					$tmp_stim_array = array ($tmp_row[0]=> 0); //array(24=>false,25=>false,26=>false,27=>false));
				}
			}
			$response{$tmp_stim}=$tmp_stim_array; //for the last stimulus.
		} else {
                        $v = array($tl[4] => 1, $tl[3] => 2, $tl[2] => 4, $tl[1] => 8);
			// We do for each country a comparison of the word answered to sort
				for($i=0; $i < pg_numrows($result); $i++){
					$tmp_row= pg_fetch_array($result, $i);
					$response{$tmp_row[1]}{$tmp_row[0]} |= $v{$test};
				}

		}
		$str = "";
		$word = "";
		$num = -1;
		$cnt = Array(0,0,0,0,0,0,0); // tableau qui permet de compter
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i); // on parcourt l'ensemble des mots pour voir si $word est dans un des dico assoc aux pays
			if($word == ""){ // beginning
				$word =  $arr[1];
				if($arr[0] != '-'){ // possible not to have answer so equals - so I increment
					$str = "<span style=\"color:red;\">".$arr[0]."</span> "; // dictword
					$num = $arr[2]; // count
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$cnt[2] = ($arr[2] == 1)?1:0;
					$cnt[3] = 0;
				}else{
					$str = "";
					$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
					$num = $arr[2];
				}
			}else{
				if($word == $arr[1]){
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){ // verification si même nombre dans ce cas on ne le remet pas
							$str .= "</span> "." \\{$num}\\; ";
						}
						$str .= ", <span style=\"color:red;\">$arr[0]</span>";
						$cnt[0] += $arr[2]; // total number of responses
						$cnt[1] += 1; // different response
						$cnt[2] += ($arr[2] == 1)?1:0; // number of response which have only 1 stimulus / 1 response
						$num = $arr[2];
					}else{
						$cnt[0] += 0; //$arr[2];
						$cnt[3] += $arr[2];
//						$num = $arr[2];
					}
				}else{
					$str = preg_replace("/^, /", "", $str);
					$str = preg_replace("/; , /", "; ", $str);
					$str .= "</span> "." \\{$num}\\"."<span style=\"color:red;\">";
					//array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
				        for($j=0; $j< pg_numrows($res_com); $j++){
			                    $arr1 = pg_fetch_array($res_com, $j);
                                            if($arr1[0] == $word){
						if($test == $tl[0]) $res[$j][2] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
						if($test == $tl[1]) $res[$j][3] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
						if($test == $tl[2]) $res[$j][4] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
						if($test == $tl[3]) $res[$j][5] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
						if($test == $tl[4]) $res[$j][6] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
					        break;
                                            }
                                            //array_push($res, Array($arr[0], "", "", "", ""));
                                        }
	                                $num = -1;
					$word = $arr[1];
					$str = "";
					if($arr[0] != '-'){
						if(($num != $arr[2]) && ($str !="")){
							$str .= " \\{$num}\\; ";
						}
						$str .= ", <span style=\"color:red;\">$arr[0]</span>";
						$cnt[0] = $arr[2];
						$cnt[1] = 1;
						$cnt[2] = ($arr[2] == 1)?1:0;
						$cnt[3] = 0;
						$num = $arr[2];
					}else{
						$str = "";
						$cnt[0] = $arr[2]; $cnt[1] = 0; $cnt[2] = 0; $cnt[3] = $arr[2];
						$num = $arr[2];
					}
				}
			}
		}
		$str = preg_replace("/^, /", "", $str);
		$str = preg_replace("/; , /", "; ", $str);
		$str .= " \\{$num}\\";
		if($word != "") {
	        for($j=0; $j< pg_numrows($res_com); $j++){
		    $arr = pg_fetch_array($res_com, $j);
                    if($arr[0] == $word){
		        if($test == $tl[0]) $res[$j][2] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
		        if($test == $tl[1]) $res[$j][3] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
		        if($test == $tl[2]) $res[$j][4] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
		        if($test == $tl[3]) $res[$j][5] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
		        if($test == $tl[4]) $res[$j][6] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]} [".pr(100*$cnt[1]/$cnt[0])."%], {$cnt[3]}, {$cnt[2]})";
		        break;
                    }
                }
}
		//usort($res, "rcmp");
           } // foreach
      for($i=2; $i<7; $i++){
		   for($j=0; $j<pg_numrows($res_com); $j++){
				preg_match_all("/\<span style=\"color:red;\"\>([^\<]*)\<\/span\>/", $res[$j][$i], $words);
				foreach($words[1] as $word){
					$local_stim = $res[$j][0];
					$local_word = $response{$local_stim}{$word};
					$color_word = color_assignement($local_word);
					$res[$j][$i] = preg_replace("/\<span style=\"color:red;\"\>".preg_quote($word, '/')."\<\/span\>/","<s".$color_word.";\">".$word."</s>", $res[$j][$i], -1, $count_replace);
				}
			}
        }
        //var_dump($res[3][2]);
        //print_r($response);

		return $res;
	}else{
		return Array();
	}
	return Array();
}

function pr($v){ return sprintf("%.2f", $v);} //return $v with two numbers after the comma.

function db_fjoint_dict1($test, $sex, $af, $at, $edu, $spec,  $base, $nl, $chr ){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;


	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
                $search_chr = "AND lower(dict.word)='$chr' ";
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";

           $res = array();
	   $cnt = Array(0,0,0,0,0,0,0); // tableau qui permet de compter
           $tl = Array();
           if($test == -1)  array_push($tl,-1,24,25,26,27);
	   if($test == -2)  array_push($tl,-1,29,30,31,32);
           foreach($tl as $test){
                $t_search = "dict.test={$test}";
                if($test == -1) $t_search = "dict.test in (".join(',',$tl).")";
		$result = @pg_exec ($conn, "select resp.word as rw, dict.word, count(resp.word) as cnt
										from resp inner join dict on dict.id=resp.id_w
											inner join users on users.id=resp.id_u
										where {$t_search} {$search_chr} and resp.word != '-'
										group by dict.word, rw
										order by dict.word, cnt desc, rw;");
		if(!$result){disconnect($conn); return Array();}
	        if(pg_numrows($result)<=0){continue;}
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i); // on parcourt l'ensemble des mots pour voir si $word est dans un des dico assoc aux pays
		        if($test == -1) $cnt[0] += $arr[2];
			else            $cnt[$test-$tl[1]+1] += $arr[2];
		}
		if($test == -1) continue;
		$str = "";
		$word = "";
		$num = -1;
                $j = 0;
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i); // on parcourt l'ensemble des mots pour voir si $word est dans un des dico assoc aux pays
                        if($num == $arr[2] || $i == 0) {
				if($i == 0)  {$str = $arr[0]; $num = $arr[2];}else $str = $str."; ".$arr[0];
			}else{
				if($num == -1) $num = $arr[2];
		        	$res[$j][$test - $tl[1]] = "{$str} ".pr(100*$num/$cnt[$test-$tl[1]+1])."%, ".pr(100*$num/$cnt[0])."%";
                        	if(strlen($res[$j][0]) == 0) $res[$j][0] = "&nbsp;";
                        	if(strlen($res[$j][1]) == 0) $res[$j][1] = "&nbsp;";
                        	if(strlen($res[$j][2]) == 0) $res[$j][2] = "&nbsp;";
                        	if(strlen($res[$j][3]) == 0) $res[$j][3] = "&nbsp;";
                                $str = $arr[0];
                                $num = $arr[2];
				$j++;
			}
		}
		$res[$j][$test - $tl[1]] = "{$str} ".pr(100*$num/$cnt[$test-$tl[1]+1])."%, ".pr(100*$num/$cnt[0])."%";
                if(strlen($res[$j][0]) == 0) $res[$j][0] = "&nbsp;";
                if(strlen($res[$j][1]) == 0) $res[$j][1] = "&nbsp;";
                if(strlen($res[$j][2]) == 0) $res[$j][2] = "&nbsp;";
                if(strlen($res[$j][3]) == 0) $res[$j][3] = "&nbsp;";
		//usort($res, "rcmp");
           } // foreach

		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_ljoint_dict($test, $sex, $af, $at, $edu, $spec,  $base, $nl, $chr ){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;


	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
                $search_chr = "";
                if(strlen($chr)>0) {
					$rest = substr(strtolower($chr), 1);
//                    if(strtolower($chr[0]) == 'e'){
//                        $search_chr .= "AND  lower(resp.word) SIMILAR  TO '(e|é|è|ê)%'  ";
//                    }else{
//                        $search_chr .= "AND lower(resp.word) like '".strtolower($chr[0])."%' ";
//                   }
                    if(strtolower($chr[0]) == 'e'){
                        $search_chr .= "AND  lower(resp.word) SIMILAR  TO '^((un|une|le|la|les) )*(e|é|è|ê){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'a'){
                        $search_chr .= "AND  lower(resp.word) SIMILAR  TO '^((un|une|le|la|les) )*(a|à|â){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'o'){
                        $search_chr .= "AND  lower(resp.word) SIMILAR  TO '^((un|une|le|la|les) )*(o|ô){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'c'){
                        $search_chr .= "AND  lower(resp.word) SIMILAR  TO '^((un|une|le|la|les) )*(c|ç){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'i'){
                        $search_chr .= "AND  lower(resp.word)  SIMILAR  TO '^((un|une|le|la|les) )*(i|î){$rest}%' ";
                    }else
					if(strtolower($chr[0]) == 'u'){
                        $search_chr .= "AND  (lower(resp.word) SIMILAR TO '^((un|une|le|la|les) )*(u|û){$rest}%'  and ".
										"lower(resp.word) not similar to '(un|une) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == 'l'){
                        $search_chr .= "AND  (lower(resp.word) similar to '^((un|une|le|la|les) )*l{$rest}%' and ".
										"lower(resp.word) not similar to '(la|le|les) (1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%') ";
                    }else
					if(strtolower($chr[0]) == '?'){
                        $search_chr .= "AND  lower(resp.word)  NOT SIMILAR  TO ".
							"'(1|2|3|4|5|6|7|8|9|0|a|à|â|b|c|ç|d|e|é|è|ê|f|g|h|i|î|j|k|l|m|n|o|ô|p|q|r|s|t|u|û|v|w|x|y|z)%' ";
                    }else {

                        $search_chr .= "AND lower(resp.word) similar to '((un|une|le|la|les) )*".strtolower($chr)."%' ";
                    }
                   // $search .= $search_chr;
                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";

                $res_com = @pg_exec ($conn, "select resp.word from resp left join dict on dict.id = resp.id_w  where test in (24,25,26,27) $search_chr group by resp.word order by resp.word;");

                if(!$res_com){disconnect($conn); return Array();}
		$res = Array();
		for($i=0; $i< pg_numrows($res_com); $i++){
			$arr = pg_fetch_array($res_com, $i);

                        array_push($res, Array($arr[0], "-", "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;","&nbsp;"));
                }
          $response = array();
           $tl = Array();
           if($test == -1)  array_push($tl,-1,24,25,26,27);
	   if($test == -2)  array_push($tl,-1,29,30,31,32);
           foreach($tl as $test){
                $t_search = "dict.test={$test}";
                if($test == -1) $t_search = "dict.test in (".join(',',$tl).")";
//		$result = @pg_exec ($conn, "select resp.word as rw, dict.word, count(resp.word) as cnt
//										from resp inner join dict on dict.id=resp.id_w
//											inner join users on users.id=resp.id_u
//										where {$t_search} {$search_chr}
//										group by dict.word, rw
//										order by dict.word, cnt desc, rw;");
		$result = pg_exec ($conn, "select dict.word, resp.word as rw,  count(dict.word) as cnt
							from resp inner join dict on dict.id=resp.id_w
								inner join users on users.id=resp.id_u
							where {$t_search} {$search_chr} and resp.word<>'-' {$search}
							group by rw, dict.word  order by rw, cnt desc, dict.word;");

		if(!$result){disconnect($conn); return Array();}
	        if(pg_numrows($result)<=0){continue;}
		if( $test == -1){
			$tmp_row = pg_fetch_array($result, 0);
			$tmp_stim = $tmp_row[1];
			$tmp_stim_array = array();
			for($i=0; $i < pg_numrows($result); $i++){
				$tmp_row = pg_fetch_array($result, $i);
				if($tmp_row[1] == $tmp_stim ) {
					$tmp_stim_array{$tmp_row[0]}= 0; //array(24=>false,25=>false,26=>false,27=>false);
				} else {
					$response{$tmp_stim}=$tmp_stim_array;
					$tmp_stim = $tmp_row[1];
					$tmp_stim_array = array ($tmp_row[0]=> 0); //array(24=>false,25=>false,26=>false,27=>false));
				}
			}
			$response{$tmp_stim}=$tmp_stim_array; //for the last stimulus.
		} else {
                        $v = array($tl[4] => 1, $tl[3] => 2, $tl[2] => 4, $tl[1] => 8);
                        //$v = array(27 => 1, 26 => 2, 25 => 4, 24 => 8);
			// We do for each country a comparison of the word answered to sort
				for($i=0; $i < pg_numrows($result); $i++){
					$tmp_row= pg_fetch_array($result, $i);
					$response{$tmp_row[1]}{$tmp_row[0]} |=$v{$test}; // = true;
				}

		}
		$str = "";
		$word = "";
		$num = -1;
		$cnt = Array(0,0,0,0,0,0,0); // tableau qui permet de compter
		for($i=0; $i< pg_numrows($result); $i++){
			$arr = pg_fetch_array($result, $i); // on parcourt l'ensemble des mots pour voir si $word est dans un des dico assoc aux pays
			if($word == ""){ // beginning
				$word =  $arr[1];
				$str = "<span style=\"color:red;\">".$arr[0]."</span> "; // dictword
				$num = $arr[2]; // count
				$cnt[0] = $arr[2];
				$cnt[1] = 1;
			}else{
				if($word == $arr[1]){
					if(($num != $arr[2]) && ($str !="")){ // verification si même nombre dans ce cas on ne le remet pas
						$str .= "</span> \\{$num}\\; ";
					}
					$str .= ", <span style=\"color:red;\">$arr[0]</span>";
					$cnt[0] += $arr[2]; // total number of responses
					$cnt[1] += 1; // different response
					$num = $arr[2];
				}else{
					$str = preg_replace("/^, /", "", $str);
					$str = preg_replace("/; , /", "; ", $str);
					$str .= "</span> "." \\{$num}\\"."<span style=\"color:red;\">";
					//array_push($res, Array($word, "{$cnt[0]}", "{$str}<br>({$cnt[0]}, {$cnt[1]}, {$cnt[3]}, {$cnt[2]})"));
				        for($j=0; $j< pg_numrows($res_com); $j++){
			                    $arr1 = pg_fetch_array($res_com, $j);
                                            if($arr1[0] == $word){
					        if($test == $tl[0]) $res[$j][2] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
					        if($test == $tl[1]) $res[$j][3] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
					        if($test == $tl[2]) $res[$j][4] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
					        if($test == $tl[3]) $res[$j][5] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
					        if($test == $tl[4]) $res[$j][6] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
					        break;
                                            }
                                            //array_push($res, Array($arr[0], "", "", "", ""));
                                        }
					$str = "";
					$word = $arr[1];
					if(($num != $arr[2]) && ($str !="")){
						$str .= " \\{$num}\\; ";
					}
					$str .= ", <span style=\"color:red;\">$arr[0]</span>";
					$cnt[0] = $arr[2];
					$cnt[1] = 1;
					$num = $arr[2];
				}
			}
		}
		$str = preg_replace("/^, /", "", $str);
		$str = preg_replace("/; , /", "; ", $str);
		$str .= " \\{$num}\\";
		if($word != "") {
	        for($j=0; $j< pg_numrows($res_com); $j++){
		    $arr = pg_fetch_array($res_com, $j);
                    if($arr[0] == $word){
			if($test == $tl[0]) $res[$j][2] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
			if($test == $tl[1]) $res[$j][3] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
			if($test == $tl[2]) $res[$j][4] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
			if($test == $tl[3]) $res[$j][5] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
			if($test == $tl[4]) $res[$j][6] = "{$str}</span><br>({$cnt[0]}, {$cnt[1]})";
		        break;
                    }
                }
}
		//usort($res, "rcmp");
           } // foreach
      for($i=2; $i<7; $i++){
		   for($j=0; $j<pg_numrows($res_com); $j++){
				preg_match_all("/\<span style=\"color:red;\"\>([^\<]*)\<\/span\>/", $res[$j][$i], $words);
				foreach($words[1] as $word){
					$local_stim = $res[$j][0];
					$local_word = $response{$local_stim}{$word};
					$color_word = color_assignement($local_word);
					$res[$j][$i] = preg_replace("/\<span style=\"color:red;\"\>".preg_quote($word, '/')."\<\/span\>/","<s".$color_word.";\">".$word."</s>", $res[$j][$i], -1, $count_replace);
				}
			}
        }
        //var_dump($res[3][2]);
        //print_r($response);

		return $res;
	}else{
		return Array();
	}
	return Array();
}

function db_getStat($term){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array(Array(),Array(),Array(),Array()); // F, B, S, C
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	echo "<pre>\n";
	if($conn){
		$stim = pg_exec ($conn, "select word from dict where test=24;");
		if(!$stim){disconnect($conn); echo "Nothing...."; return 0;}
		$n_resp = 0;

		foreach(array(24,25,26,27) as $test){
  		   $n_stim = pg_numrows($stim); //$n_stim = 1;
		   $resp = pg_exec ($conn, "select count(*) from resp left join dict on resp.id_w = dict.id where dict.test=$test and resp.word !='-';");
		   $r = pg_fetch_array($resp, 0);
		   $n_resp = $n_resp + $r[0];

		   for($i=0; $i< $n_stim; $i++){

			$arr  = pg_fetch_array($stim, $i);
			$resp = pg_exec ($conn, "select resp.word as resp, count(*)as cnt  from resp ".
                                                        "left join dict on resp.id_w = dict.id where dict.test=$test and dict.word='{$arr[0]}' and resp.word !='-' ".
                                                        "group by  resp.word order by cnt desc limit $term;");
			for($j=0; $j< pg_numrows($resp); $j++){
				$r = pg_fetch_array($resp, $j);
				$res[$test-24][$r[0]] = $r[1];

			}
		   }
		} //$test
		$tst = Array('C','S','B','F');
		for($i=15; $i>0; $i--){
			$sum = 0;
			for($k=0; $k<4; $k++)
			if($i & (1<<$k)){
				echo $tst[$k];
				foreach(array_keys($res[$k]) as $j){
                         	        $r = $res[$k][$j];
					$sum += $r;
                        	}
			}
			echo " - $sum of $n_resp = ".pr(100*$sum/$n_resp)."%\n";
		}
		return 0;
	}else{
		echo "Connection to DB failed";
	}
	echo "</pre>\n";
}



function db_getStimStat($stim){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$res = Array(Array(),Array(),Array(),Array(),Array()); // F, B, S, C, Joint
	$out = Array();
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		foreach(array(24,25,26,27) as $test){
			$resp = pg_exec ($conn, "select resp.word as resp, count(*)as cnt  from resp ".
                                                        "left join dict on resp.id_w = dict.id where dict.test=$test and dict.word='{$stim}' and resp.word !='-' ".
                                                        "group by  resp.word order by cnt;");
			for($j=0; $j< pg_numrows($resp); $j++){
				$r = pg_fetch_array($resp, $j);
				if(array_key_exists($r[0],$res[$test-24])) $res[$test-24][$r[0]] = $res[$test-24][$r[0]]+$r[1];
				else				 $res[$test-24][$r[0]] = $r[1];

			}
		} //$test
		$tst = Array('C','S','B','F');
		for($i=15; $i>0; $i--) {
			$s = "";
			if($i & 1) $s = "C".$s;
			if($i & 2) $s = "S".$s;
			if($i & 4) $s = "B".$s;
			if($i & 8) $s = "F".$s;
			array_push($out, Array($s, "", 0, 0));
		}
		$resp = pg_exec ($conn, "select resp.word as resp, count(*)as cnt  from resp ".
                                                       "left join dict on resp.id_w = dict.id where dict.test in (24,25,26,27) and dict.word='{$stim}' and resp.word !='-' ".
                                                       "group by  resp.word order by cnt desc;");
		for($j=0; $j< pg_numrows($resp); $j++){
			$r = pg_fetch_array($resp, $j);
			$row = 0;
			$n = 0;
			$str = $r[0];
			$s = "$str ";
			if(array_key_exists($str,$res[0])) {$row = $row | 8; $s.=$res[0][$str].'/'; $n += $res[0][$str];}
			if(array_key_exists($str,$res[1])) {$row = $row | 4; $s.=$res[1][$str].'/'; $n += $res[1][$str];}
			if(array_key_exists($str,$res[2])) {$row = $row | 2; $s.=$res[2][$str].'/'; $n += $res[2][$str];}
			if(array_key_exists($str,$res[3])) {$row = $row | 1; $s.=$res[3][$str].'/'; $n += $res[3][$str];}

			$out[15-$row][1] .= substr($s,0, -1)."<br>\n";
			$out[15-$row][2] += 1;
			$out[15-$row][3] += $n;
		}
	}
	return $out;
}

function db_getStat1($term){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;
	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	echo "<pre>\n";
	if($conn){
		$stim = pg_exec ($conn, "select word from dict where test=24;");
		if(!$stim){disconnect($conn); echo "Nothing...."; return 0;}
		$n_resp = 0;

  		$n_stim = pg_numrows($stim); //$n_stim = 10;
		$fbsc = 0;   $_fbsc = 0; $f_bsc = 0; $fb_sc = 0; $fbs_c = 0; $_f_b_s_c = 0;
		$fb__sc = 0; $fs__bc = 0; $fc__bs = 0; $sc__fb = 0;

		echo "# stimulus = ".pg_numrows($stim)."\nTop $term of response(s) \n\n";
		echo "<table border=1>\n";
		echo "<tr><td>Stimulus</td><td>FBSC</td><td>FBS/C</td><td>FBC/S</td><td>FSC/B</td><td>BSC/F</td>";
		echo "<td>FB/SC</td><td>FS/BC</td><td>FC/BS</td><td>SC/FB</td><td>/F/B/S/C</td></tr>";
		for($i=0; $i< $n_stim; $i++){
		    $arr  = pg_fetch_array($stim, $i);
	            $res = Array(Array(),Array(),Array(),Array()); // F, B, S, C
		    foreach(array(24,25,26,27) as $test){

			$resp = pg_exec ($conn, "select resp.word as resp, count(*)as cnt  from resp ".
                                                        "left join dict on resp.id_w = dict.id where dict.test=$test and dict.word='{$arr[0]}' and resp.word !='-' ".
                                                        "group by  resp.word order by cnt desc limit $term;");
			for($j=0; $j< pg_numrows($resp); $j++){
				$r = pg_fetch_array($resp, $j);
				$res[$test-24][$r[0]] = $r[1];

			}
		   } // test
		   //echo $arr[0]."\n"; var_dump($res);
		   $fbsc_ = 0;   $_fbsc_ = 0; $f_bsc_ = 0; $fb_sc_ = 0; $fbs_c_ = 0; $_f_b_s_c_ = 0;
		   $fb__sc_ = 0; $fs__bc_ = 0; $fc__bs_ = 0; $sc__fb_ = 0;

		   foreach(array_keys($res[0])as $key){
			if(array_key_exists($key,$res[1]) && array_key_exists($key,$res[2]) && array_key_exists($key,$res[3]))  $fbsc_  = 1;
			if(!array_key_exists($key,$res[1]) && array_key_exists($key,$res[2]) && array_key_exists($key,$res[3])) $f_bsc_ = 1;
			if(array_key_exists($key,$res[1]) && !array_key_exists($key,$res[2]) && array_key_exists($key,$res[3])) $fb_sc_ = 1;
			if(array_key_exists($key,$res[1]) && array_key_exists($key,$res[2]) && !array_key_exists($key,$res[3])) $fbs_c_ = 1;
		   }
		   foreach(array_keys($res[1])as $key){
			if(!array_key_exists($key,$res[0]) && array_key_exists($key,$res[2]) && array_key_exists($key,$res[3])) $_fbsc_ = 1;
		   }

		   foreach(array_keys($res[0])as $key)
			if(!array_key_exists($key,$res[1]) && !array_key_exists($key,$res[2]) && !array_key_exists($key,$res[3])) $_f_b_s_c_ |= 1;
		   foreach(array_keys($res[1])as $key)
			if(!array_key_exists($key,$res[0]) && !array_key_exists($key,$res[2]) && !array_key_exists($key,$res[3])) $_f_b_s_c_ |= 2;
		   foreach(array_keys($res[2])as $key)
			if(!array_key_exists($key,$res[1]) && !array_key_exists($key,$res[0]) && !array_key_exists($key,$res[3])) $_f_b_s_c_ |= 4;
		   foreach(array_keys($res[3])as $key)
			if(!array_key_exists($key,$res[1]) && !array_key_exists($key,$res[2]) && !array_key_exists($key,$res[0])) $_f_b_s_c_ |= 8;

	   	   if($_f_b_s_c_ == 15) $_f_b_s_c_ = 1; else $_f_b_s_c_ = 0;

		   foreach(array_keys($res[0])as $key)
			if(array_key_exists($key,$res[1]) && !array_key_exists($key,$res[2]) && !array_key_exists($key,$res[3])) $fb__sc_ |= 1;
		   foreach(array_keys($res[0])as $key)
			if(!array_key_exists($key,$res[1]) && array_key_exists($key,$res[2]) && !array_key_exists($key,$res[3])) $fs__bc_ = 1;
		   foreach(array_keys($res[0])as $key)
			if(!array_key_exists($key,$res[1]) && !array_key_exists($key,$res[2]) && array_key_exists($key,$res[3])) $fc__bs_ = 1;
		   foreach(array_keys($res[3])as $key)
			if(!array_key_exists($key,$res[1]) && array_key_exists($key,$res[2]) && !array_key_exists($key,$res[0])) $sc__fb_ = 1;


		   echo "<tr><td>{$arr[0]}</td><td>$fbsc_</td><td>$fbs_c_</td><td>$fb_sc_</td><td>$f_bsc_</td><td>$_fbsc_</td>";
		   echo "<td>$fb__sc_</td><td>$fs__bc_</td><td>$fc__bs_</td><td>$sc__fb_</td><td>$_f_b_s_c_</td></tr>";
   		   $fbsc  += $fbsc_;
		   $_fbsc += $_fbsc_;
		   $f_bsc += $f_bsc_;
		   $fb_sc += $fb_sc_;
		   $fbs_c += $fbs_c_;
		   $_f_b_s_c += $_f_b_s_c_;
		   $fb__sc += $fb__sc_;
		   $fs__bc += $fs__bc_;
		   $fc__bs += $fc__bs_;
		   $sc__fb += $sc__fb_;
		} // stim

   		$fbsc  /= $n_stim; $fbsc  = pr(100*$fbsc);
		$_fbsc /= $n_stim; $_fbsc = pr(100*$_fbsc);
		$f_bsc /= $n_stim; $f_bsc = pr(100*$f_bsc);
		$fb_sc /= $n_stim; $fb_sc = pr(100*$fb_sc);
		$fbs_c /= $n_stim; $fbs_c = pr(100*$fbs_c);
		$_f_b_s_c = pr(100*$_f_b_s_c/$n_stim);
		$fb__sc = pr(100*$fb__sc/$n_stim);
		$fs__bc = pr(100*$fs__bc/$n_stim);
		$fc__bs = pr(100*$fc__bs/$n_stim);
		$sc__fb = pr(100*$sc__fb/$n_stim);

		echo "<tr><td>Total</td><td>$fbsc</td><td>$fbs_c</td><td>$fb_sc</td><td>$f_bsc</td><td>$_fbsc</td>";
		echo "<td>$fb__sc</td><td>$fs__bc</td><td>$fc__bs</td><td>$sc__fb</td><td>$_f_b_s_c</td></tr>";

		return 0;
	}else{
		echo "Connection to DB failed";
	}
	echo "</pre>\n";
}

//Pour récupérer les valeurs utiles au chart graph.

function db_fjoint_graph($test, $sex, $af, $at, $edu, $spec,  $base, $nl, $chr ){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;


	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
                $search_chr = "";
                if(strlen($chr)>1) {
                     $search_chr = "AND lower(dict.word)='".strtolower($chr)."' ";
                }else{
                     $search_chr = "AND lower(dict.word) like '".strtolower($chr[0])."%' ";
                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";

          $response = array();
          $tl = Array();
          if($test == -1)  array_push($tl,-1,24,25,26,27);
	  if($test == -2)  array_push($tl,-1,29,30,31,32);

           foreach($tl as $test){
                $t_search = "dict.test={$test}";
                if($test == -1) $t_search = "dict.test in (".join(',',$tl).")";
		$result = pg_exec ($conn, "select count(resp.word) as cnt
										from resp inner join dict on dict.id=resp.id_w
											inner join users on users.id=resp.id_u
										where {$t_search} {$search_chr}
										group by dict.word, resp.word order by cnt desc;");
                // print $result." ".$test." ".pg_numrows($result)."\n";
		if(!$result){disconnect($conn); return Array();}
                for($i=0; $i< pg_numrows($result); $i++)
	            $response[$test][$i] = pg_fetch_array($result,$i);
           }

          return $response;
      }
      return Array();
}

//Pour récupérer les valeurs utiles au Word Cloud

function db_cloud($test, $sex, $af, $at, $edu, $spec,  $base, $nl, $chr ){
	global $db_host, $db_user, $db_pass, $db_enc, $db_name, $db_port;


	$conn = connect($db_host, $db_port, $db_name, $db_user, $db_pass, $db_enc);
	if($conn){
		$search = "";
		if($sex == 'M') $search .= "AND users.sex='t' ";
		if($sex == 'F') $search .= "AND users.sex='f' ";
		if(strlen($nl)>0) $search .= "AND users.lang_n = '{$nl}' ";
		if(strlen($af)>0) $search .= "AND users.age > $af ";
		if(strlen($at)>0) $search .= "AND users.age < $at ";
		if(strlen($edu)>0) $search .= "AND users.edu in ($edu) ";
		if(strlen($spec)>0) $search .= "AND users.spec like '".AddSlashes($spec)."' ";
                $search_chr = "";
                if(strlen($chr)>1) {
                     $search_chr = "AND lower(dict.word)='".strtolower($chr)."' ";
                }else{
                     $search_chr = "AND lower(dict.word) like '".strtolower($chr[0])."%' ";
                }
		// commented  base dict
		//if($base == 1) $search.= "AND dict.base='T' ";

          $response = array();

          $t_search = "dict.test={$test}";
 /*         echo "select resp.word, count(resp.word) as cnt
                                        from resp inner join dict on dict.id=resp.id_w
                                                  inner join users on users.id=resp.id_u
                                                        where {$t_search} {$search_chr}
                                                        group by dict.word, resp.word order by cnt desc;";*/
          $result = pg_exec ($conn, "select resp.word, count(resp.word) as cnt
					from resp inner join dict on dict.id=resp.id_w
					          inner join users on users.id=resp.id_u
							where {$t_search} {$search_chr}
							group by dict.word, resp.word order by cnt desc;");
		if(!$result){disconnect($conn); return Array();}
                for($i=0; $i< pg_numrows($result); $i++){
                    $arr = pg_fetch_array($result,$i);
	            $response[$arr[0]] = $arr[1];
		}

          return $response;
      }
      return Array();
}


?>
