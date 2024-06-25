// BackendTools SliceTimer
// v1.0

$(function(){
	$(document).on("rex:ready", function(){ bet_slicetimer_init(); });
	bet_slicetimer_init();
	

	function bet_slicetimer_init()
	{	//prepare all slicetimer-Buttons
		$('a.btn-slicetimer').on('click', function(e){
			if (!$(this).hasClass('btn-slicetimer-disabled')) {
				$(this).toggleClass('btn-slicetimer-opened');			//$(this).toggleClass('btn-slicetimer-active');
				$(this).parents('.panel-heading').nextAll('.bet-slicetimer').slideToggle('fast');
			}
		});
		
			checkSlicetimerButton();		
			function checkSlicetimerButton()
			{	//disable button or not
				$("a.btn-slicetimer").each(function(){
					dst = $(this).parents(".panel");
					dstP = dst.parents("li.rex-slice");
					if (dst.hasClass('panel-add') || dst.hasClass('panel-edit') || dstP.hasClass('rex-slice-offline')) { $(this).addClass('btn-slicetimer-disabled'); }
					else { $(this).removeClass('btn-slicetimer-disabled'); }					
				});
			}		
				
		
		//datepicker
		$.datetimepicker.setLocale('de');
		$('.bet_st_datepicker-widget input').each(function(){
			if ($(this).val() == '__.__.____' || $(this).val() == '__.__.____ __:__') { $(this).val(""); }						//Kalender-Value bei Reload korrigieren
			
			lazy = ($(this).attr('data-datepicker-lazy') == 'true' ? true : false);
			mask = ($(this).attr('data-datepicker-mask') == 'true' ? true : false);
			time = ($(this).attr('data-datepicker-time') == 'true' ? true : false);
			format = (time ? 'd.m.Y H:i' : 'd.m.Y');
			now = new Date();
				start = 2024;
				end = now.getFullYear() + 5;
			$(this).datetimepicker({
				format: format, formatDate: 'd.m.Y', formatTime: 'H:i', yearStart: start, yearEnd: end, dayOfWeekStart: 1,
				mask: mask, lazyInit: lazy, week: true, timepicker: time, step: 15
			});
		});
		
		$('.bet_st_datepicker-widget a').click(function(){
			dst = $(this).attr('data-datepicker-dst');
			if (dst != "" && dst != 'undefined') { $('#'+dst).datetimepicker('show'); }
		});	
		
		
		//form submit
		$("form.bet-slicetimer-form").on('submit', function(e){
			e.preventDefault();
			var btn = $(this).find('button');
			var	btntext = btn.html();
			var sid = parseInt($(this).find('input[name=bet_sform_sid]').val());
			
			var data = '';
				$(this).find('input[type!=checkbox], button').each(function(){
					data += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
				});
				data += '&bet_sform_status=';
				data += ($(this).find('input[type=checkbox]').is(":checked") ? 1 : 0);
						
			btn.html('<i class="rex-icon fa-refresh fa-spin"></i>');			
			
			if (data.length > 1) { 
				$.post("index.php?rex-api-call=bet_slicetimer_save" + data, function(resp){ 
					if (resp == 'saved') {
						btn.html('<i class="rex-icon fa-check"></i>').addClass('bet-success');
					} else {
						console.log('Error: saving failed (SID: '+sid+')');
						btn.html('<i class="rex-icon fa-times"></i>').addClass('bet-failed');
					}
					
					getSlicetimerInfo(sid);
					setTimeout(function(){ btn.html(btntext).removeClass('bet-success bet-failed');	}, 3000);
					
				}).fail(function(){
					console.log('Error: request for saving failed (SID: '+sid+')');
					btn.html('<i class="rex-icon fa-times"></i>').addClass('bet-failed');
				});
			}
			
		});
		

		//set infoblocks
		getSlicetimerInfo();
		function getSlicetimerInfo(sid = 0)
		{	s = (sid > 0 ? 'li#slice'+sid : 'li.rex-slice.rex-slice-output');
		
			$(s).each(function(){
				var sid = $(this).attr('id');
					sid = sid.toString().replace(/[^0-9]/g, "");
				var dst = $(this).find(".panel-default > header.panel-heading");
				
				$.get("index.php?rex-api-call=bet_slicetimer_getInfo&bet_sid=" + sid, function(resp){
					dst.nextAll('.bet-slicetimerInfo').slideUp('fast', function() { $(this).remove(); });
					//active slicetimer found
					if (resp != "" && resp != 'undefined') {
						//show infoblock
						dst.after(resp).nextAll('.bet-slicetimerInfo').fadeIn('fast')
							.on('click', function(){ $(this).toggleClass('bet-slicetimerInfo-opened'); });

						//show button status
						dst.find('a.btn-slicetimer').addClass('btn-slicetimer-active');
					} else {
						//reset button status
						dst.find('a.btn-slicetimer').removeClass('btn-slicetimer-active');
					}
				});
			});
		}
		
		
	}
});