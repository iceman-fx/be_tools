// BackendTools Scripts
// v1.7.9

$(function(){
	//add gotoTop-Button
	$gt = $("div.bet-gototop");
	$gt.click(function(e){
		e.preventDefault();
		$("body, html").animate({scrollTop: 0}, 350, "linear");
	});
	
	$(document).on('ready scroll', function(){
		var pos = $(this).scrollTop();
		if (pos > 275) {	
			$gt.addClass('bet-gototop-show');
		} else {
			$gt.removeClass('bet-gototop-show');
		}
	});
});