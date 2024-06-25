// BackendTools Nav
// v1.7

$(function(){
	//set additional styles for mobiles
	if ($.browser.mobile) { $('html').addClass('mobile'); }
	if ($.browser.desktop) { $('html').addClass('desktop'); }
	
	
	//minimize Navbar
	//prepare click-event for mobile
	$(".mobile .rex-is-logged-in nav.rex-nav-main, .mobile .rex-is-logged-in div.rex-nav-main").one("click", function(e){ e.preventDefault(); });
	
	//set sticky-Button
	function initBetNav()
	{	var betdst = $('body');
		var betnav = $('.rex-is-logged-in nav.rex-nav-main, .rex-is-logged-in div.rex-nav-main');
			betnav.append('<div class="betnav-stickyBtn" title="'+betlang.stickybtn+'"><i class="rex-icon fa-thumb-tack"></i></div>');
			betnav.find('.betnav-stickyBtn').click(function(){
				if (Cookies.get("betnav") == "sticked") { Cookies.set("betnav", "", { expires: 365 }); betdst.removeClass('betnav-sticked'); }
				else { Cookies.set("betnav", "sticked", { expires: 365 }); betdst.addClass('betnav-sticked'); }
			});
		if (Cookies.get("betnav") == "sticked") { betdst.addClass('betnav-sticked'); }			//set status@start
	}
	
	$(document).on("rex:ready", function(){	initBetNav(); });
	initBetNav();
});