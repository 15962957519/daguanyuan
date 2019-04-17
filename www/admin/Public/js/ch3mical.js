$(document).ready(function() {
	var $centerwell_first = $('#centerwell li:first');
	$centerwell_first.animate({width: '455px'}, 300);
	$centerwell_first.find('h3').animate({backgroundPosition: '-39px'}, 300);
	
	

	$('#centerwell li').mouseover(function() {
		if(!$(this).is(':animated')){
			$(this).animate({width: '455px'}, 300).siblings().animate({width: '39px'}, 300);
			$(this).find('h3').animate({backgroundPosition: '-39px'}, 300).parent().siblings().find('h3').animate({backgroundPosition: '0px'}, 300);
		}
	});
});