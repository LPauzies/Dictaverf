<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of s_criteria_class
 *
 * @author arom
 */
class s_criteria_class {
    //put your code here
    var $age_from;
    var $age_to;
    var $sex;
    var $edu;
    var $spec;
    var $city;
    var $lang;
    var $base;
    var $chr;
    var $reg;
	var $sort;
	var $sr;
	var $srf;
	var $srt;
	
    function  __construct() {
        $this->s_criteria_class();;
    }
    function  s_criteria_class() {
        $this->age_from = "";
        $this->age_to = "";
        $this->sex = "";
        $this->edu = "";
        $this->spec = "";
        $this->city = "";
        $this->lang = "";
        $this->base = "";
        $this->chr = "";
        $this->reg = "0";
		$this->sort = "0";
		$this->sr = "0";
		$this->srf = "1";
		$this->srt = "1";
    }
    function parse($str){
        $d = explode(":", $str);
        $this->sex = $d[0];
        $this->age_from = $d[1];
        $this->age_to = $d[2];
        $this->edu = $d[3];
        $this->spec = $d[4];
        $this->city = $d[5];
        $this->lang = $d[6];
        $this->base = $d[7];
        $this->chr = $d[8];
        $this->reg = $d[9];
		$this->sort = $d[10]; 
    }
    function cookie(){
        $str = $this->sex.":".
               $this->age_from.":".
               $this->age_to.":".
               $this->edu.":".
               $this->spec.":".
               $this->city .":".
               $this->lang.":".
               $this->base.":".
               $this->chr.":".
               $this->reg.":".
			   $this->sort;
        return $str;
    }
}
?>
