
<script type="text/javascript" language="javascript">
<?php
if($dict == "list")
    echo "function do_onLoad(){ch_list_dict_(1);}";
elseif($dict == "right")
    echo "function do_onLoad(){\$('#autocomplete').autocomplete();}"; //AdvSearch_r();
else // back
	echo "function do_onLoad(){show_modal('bdict');}"; //AdvSearch_r();
?>

$(document).ready(function(){
	//get the height and width of the page
	var window_width = $(window).width();
	var window_height = $(window).height();

	//vertical and horizontal centering of modal window(s)
	// we will use each function so if we have more then 1 modal window we center them all
	$('.bdict_selector').each(function(){
    		//get the height and width of the modal
	    	var modal_height = $(this).outerHeight();
    		var modal_width = $(this).outerWidth();

	    	//calculate top and left offset needed for centering
    		var top = (window_height-modal_height)/2;
	    	var left = (window_width-modal_width)/2;

    		//apply new top and left css values
	    	$(this).css({'top' : top , 'left' : left});
	});
	$('.db_selector').each(function(){
    		//get the height and width of the modal
	    	var modal_height = $(this).outerHeight();
    		var modal_width = $(this).outerWidth();

	    	//calculate top and left offset needed for centering
    		var top = (window_height-modal_height)/2;
	    	var left = (window_width-modal_width)/2;

    		//apply new top and left css values
	    	$(this).css({'top' : top , 'left' : left});
	});
	$('.order').click(function(){
		var type = $(this).attr('name');
		close_modal("");
                if(type == 'respj' || type == 'wordj'){
			if(type == 'respj') {chDictj_rs();}
			if(type == 'wordj') {chDictj_st();}
		} else {
			$("#"+type+'_order').css({'display':'block'});
		}
	});
	$('.db_link').click(function(){
		var tid = $(this).attr('name');
		var date = new Date();
		close_modal("");
		date.setTime(date.getTime()+(30*24*60*60*1000));
		document.cookie='test='+tid+'; expires='+date.toGMTString();
		window.location="http://dictaverf.nsu.ru/dict";
	});
});
function close_modal(str){
    //hide the mask
    $('#mask').fadeOut(500);
    //hide modal window(s)
    if(str == "bdict")
		$('.bdict_selector').fadeOut(500);
    else
	if(str == "db")
		$('.db_selector').fadeOut(500);
	else{
    	$('.bdict_selector').fadeOut(500);
    	$('.db_selector').fadeOut(500);
	}
}
function show_modal(str){
    //set display to block and opacity to 0 so we can use fadeTo
    $('#mask').css({ 'display' : 'block', opacity : 0});
    //fade in the mask to opacity 0.8
    $('#mask').fadeTo(500,0.8);
     //show the modal window
	if(str == "bdict")
    		$('#bdict_selector').fadeIn(500);
	else
	if(str == "db")
		$('#db_selector').fadeIn(500);
	else{
		$('#bdict_selector').fadeIn(500);
		$('#db_selector').fadeIn(500);
	}
}


</script>
