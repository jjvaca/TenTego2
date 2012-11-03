//Przyklejenie stopki do dolu
$(function(){
    positionFooter(); 
    function positionFooter(){
        if($("#wrapper").height() < $(window).height()){
				var wheight = $(window).height();
				$("#wrapper").css({"min-height":wheight-126});
        }   
    }

    $(window).resize(positionFooter);

});

//Zmiana rozmiaru tekstu w naglowku obiektu
$(function($) {
	/**
	* Resizes an inner elements font so that the inner element completely fills the outer element.
	* @author Russ Painter WebDesign@GeekyMonkey.com
	* @version 0.1
	* @param {Object} Options which are maxFontPixels (default=40), innerTag (default='span')
	* @return All outer elements processed
	* @example <div class='mybigdiv filltext'><span>My Text To Resize</span></div>
	*/
	$.fn.textfill = function(options) {
		var defaults = {
			maxFontPixels: 40,
			innerTag: 'h2'
		};
		var Opts = jQuery.extend(defaults, options);
		return this.each(function() {
			var fontSize = Opts.maxFontPixels;
			var ourText = $(Opts.innerTag + ':visible:first', this);
			var maxHeight = 25;
			var maxWidth = 474;
			var textHeight;
			var textWidth;
			do {
				ourText.css('font-size', fontSize);
				textHeight = ourText.height();
				textWidth = ourText.width();
				fontSize = fontSize - 1;
			} while ((textHeight > maxHeight || textWidth > maxWidth) && fontSize > 3);
		});
	};
});

//Wysrodkowanie malych obrazkow
$(document).ready(function() {
	$('.block h2').textfill({ maxFontPixels: 22, innerTag: 'span'});
	
	$(".object img").load(function() {
		var imgheight = $(this).height();
		var oheight = $(this).parent().parent().height();
		if(imgheight < oheight) {
			$(this).css({"margin-top":((oheight-imgheight)/2)+"px"});
		}
	});
});

//Zakladki
$(function(){
	$('#tabs div').hide();
	$('#tabs div:first').show();
	$('#tabs ul li:first').addClass('active');
 
	$('#tabs ul li a').click(function(){
		$('#tabs ul li').removeClass('active');
		$(this).parent().addClass('active');
		var currentTab = $(this).attr('href');
		$('#tabs div').hide();
		$(currentTab).show();
		return false;
	});

});

//Rozwijane bloki w profilu (profile.php & user.php)
$(function() {
	$(".block.roll:not(:first)").css({"margin-bottom":100});
	$(".block.roll:not(:first)").children("div.tresc").hide();
	$(".block.roll").children("h1").click(function() {
		var div = $(this).next("div.tresc");
					
		if(div.is(":hidden")) {
			div.show("normal");
						
			$(".block.roll div.tresc").not(div).hide("normal");
			$(div).parent().animate({"margin-bottom":0},"normal");
			$(".block.roll:not(:last)").not($(div).parent()).animate({"margin-bottom":100},"normal");
		}
	});			 
});

//Div z formularzem wgrywania avatara
$(function() {
	$('div[class="select_avatar"]').hide();
	$('.avatar').click( function() { $('div[class="select_avatar"]').toggle(); }); 
});