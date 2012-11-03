function message(text) {
	clearInterval(inter);
	$("div#vote_response").stop(true, true).show().html(text);
	inter=setTimeout(function() { $("div#vote_response").fadeOut(500); },2000);
}