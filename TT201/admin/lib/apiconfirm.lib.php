<?php
if(isset($_GET['js'])) {
	header("Content-type: text/javascript");
?>
$(document).ready(function() {
	$(".apiconfirm").click(function() {
		$("#apiconfirm_dialog").remove();
		var text = $(this).attr("title");
		var href = $(this).attr("href");
		$("body").append('<div id="apiconfirm_dialog" style="background:#56bfe5; display:none; position:fixed; z-index:1000; width:480px; padding:10px; left:50%; margin-left:-230px; top:200px; border:1px solid #CCC; border-radius:5px; box-shadow:0px 0px 30px #ccc;"><p style="padding:20px; font-family:Arial; font-size:16px; color:#fff; text-shadow:1px 1px 0px #4190ad; text-align:center;">'+text+'</p><div style="margin:-10px; border-radius:0 0 5px 5px; padding:10px; background:#fafafa;"><a href="'+href+'" style="background:#f0f0f0; border:1px solid #f9f9f9; padding:7px 20px; border-radius:5px; display:block; float:left; color:#000; font-weight:bold; text-decoration:none; text-shadow:1px 1px 0px #FFF;">OK</a><a href="#" onClick="$(\'#apiconfirm_dialog\').fadeOut(200, function() { $(\'#apiconfirm_dialog\').remove(); }); return false;" style="background:#f0f0f0; border:1px solid #f9f9f9; padding:7px 20px; border-radius:5px; display:block; float:right; color:#000; font-weight:bold; text-decoration:none; text-shadow:1px 1px 0px #FFF;">Anuluj</a><br style="clear:both;" /></div></div>');
		$("#apiconfirm_dialog").fadeIn(200);
		return false;
	});
});
<?php
}
else {
	class apiconfirm {
		function load() {
			echo '<script type="text/javascript" src="lib/apiconfirm.lib.php?js"></script>';
		}
	}
}
?>