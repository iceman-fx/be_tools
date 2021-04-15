// BackendTools Scripts
// v1.5.4

$(function(){
	//minmize Sidebar
	var betsbclass = 'bet-sidebar';
	var betsbp = $('section.rex-main-frame');
		betsbp.addClass(betsbclass);
		$('.rex-main-frame.bet-sidebar .rex-main-sidebar').append('<div class="betsidebar-opener"><span></span></div>');

	var betsb = $('.rex-main-frame.bet-sidebar .col-lg-4');
		betsb.on('mouseenter', function() { betsbp.removeClass(betsbclass); });
		betsb.on('mouseleave', function() { betsbp.addClass(betsbclass); });
});