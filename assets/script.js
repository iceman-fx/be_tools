// Javascript for be_tools

$(function(){
	//set additional styles for mobiles
	if ($.browser.mobile) { $('html').addClass('mobile'); }
	if ($.browser.desktop) { $('html').addClass('desktop'); }
	
	$(".mobile .rex-is-logged-in nav.rex-nav-main").one("click", function(e){ e.preventDefault(); });
});