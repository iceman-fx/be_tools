// BackendTools Scripts
// v1.6

$(function(){
	//minmize Sidebar
	var betsbclass = 'bet-sidebar';
	var betsbclasshover = 'bet-sidebar-hover';
	var betsbp = $('section.rex-main-frame');
		betsbp.addClass(betsbclass);
		$('.rex-main-frame.bet-sidebar .rex-main-sidebar').append('<div class="betsidebar-opener"><span></span></div>');

	var betsb = $('.rex-main-frame.bet-sidebar .col-lg-4');
		betsb.on('mouseenter', function() {
			betsbp.addClass(betsbclasshover);
			setTimeout(function(){ if (betsbp.hasClass(betsbclasshover)) { betsbp.removeClass(betsbclass); }}, 500);
		});
		betsb.on('mouseleave', function() { betsbp.removeClass(betsbclasshover); betsbp.addClass(betsbclass); });
});