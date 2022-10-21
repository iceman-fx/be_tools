// BackendTools Scripts
// v1.7.5

$(function(){
	//add gotoTop-Button
	$gt = $("div.bet-gototop");
	$gt.click(function(e){
		e.preventDefault();
		$("body, html").animate({scrollTop: 0}, 650, "linear");
	});
	
	$(document).on('ready scroll', function(){
		var pos = $(this).scrollTop();
		if (pos > $(window).height()) {	
			$gt.addClass('bet-gototop-show');
		} else {
			$gt.removeClass('bet-gototop-show');
		}
	});
});