<?php
	function host() {
		$link = pathinfo($_SERVER['SCRIPT_NAME']);
		return str_replace('/_js','','http://'.$_SERVER['SERVER_NAME'].$link['dirname']);
	}
	header('Content-Type: text/javascript');
	if (isset($_GET['favorites'])) {
?>
	//Scripted by Wojciech Krol

	var inter;
	function fav(itemID,tag) {
		$.get('<?php echo host(); ?>/favorites.php?do',{mf:itemID},function(x) {
			message(x);
			checkFav(itemID,tag);
		});
	}
	function checkFav(itemID,tag) {
		$.get('<?php echo host(); ?>/favorites.php?do',{check:itemID},function (y) {
				if(y == 1) {
					$(tag).text("Usuń z ulubionych").attr("class","del_fav");
				}
				else {
					$(tag).text("Dodaj do ulubionych").attr("class","add_fav");
				}
		});
	}
<?php
	}
	elseif (isset($_GET['vote'])) {
?>
	//Scripted by Wojciech Krol & Pawel Klockiewicz


	function checkRateColor(proc, what) {
		if(proc == '?') color = '#FFF';
		else if(proc > 50) color = '#06f000';
		else if(proc >= 30 && proc <= 50) color = '#f3c81a';
		else color = '#f35118';
		$(what).css({'color':color});
	}

	var postID;
	var voteDownScript='<?php echo host(); ?>/vote.php?type=down';
	var voteUpScript='<?php echo host(); ?>/vote.php?type=up';
	var voteGetRate='<?php echo host(); ?>/vote.php';
	var inter;
	// Glosowanie w gore
	function vote_up(itemID) {
		message("<h2>Zapisuję głos</h2>");
		
		$.post(voteUpScript,{vid:'TT2',id:itemID},function(response){
			message(response);
			$.get(voteGetRate, {get:itemID}, function(x) {
				$('.rate_'+itemID).html(x+'%');
				checkRateColor(x,'.rate_'+itemID);
			});
		});
	}

	//Glosowanie w dol
	function vote_down(itemID) {
		message("<h2>Zapisuję głos</h2>");
	
		$.post(voteDownScript,{vid:'TT2',id:itemID},function(response){
			message(response);
			$.get(voteGetRate, {get:itemID}, function(x) {
				$('.rate_'+itemID).html(x+'%');
				checkRateColor(x,'.rate_'+itemID);
			});
		});
	   
    }
<?php
	}
elseif (isset($_GET['mod'])) {
?>
	var modMove='<?php echo host(); ?>/mod.php';
	function mod_move(itemID) {
		message("<h2>Przenoszenie...</h2>");
		$.post(modMove, {vid:'TT2',mid:itemID}, function(response) {
			message(response);
		});
	}
	function mod_amove(itemID) {
		var verify = confirm("Czy na pewno chcesz przenieść ten obiekt do archiwum? Przywrócenie będzie możliwe TYLKO z poziomu administratora serwisu");
		if(!verify) return false;
		message("<h2>Przenoszenie do archiwum...</h2>");
		$.post(modMove, {vid:'TT2',aid:itemID}, function(response) {
			message(response);
		});
	}
	function mod_del(itemID) {
		var verify = confirm("Czy na pewno chcesz usunąć ten obiekt?");
		if(!verify) return false;
		message("<h2>Usuwanie...</h2>");
		$.post(modMove, {vid:'TT2',did:itemID}, function(response) {
			message(response);
		});
	}
	function mod_userBlock(userID) {
		message("<h2>Blokowanie...</h2>");
		$.post(modMove, {vid:'TT2',ubid:userID}, function(response) {
			message(response);
		});
	}
	function mod_userUnblock(userID) {
		message("<h2>Odblokowywanie...</h2>");
		$.post(modMove, {vid:'TT2',uubid:userID}, function(response) {
			message(response);
		});
	}
	function mod_userActive(userID) {
		message("<h2>Aktywacja...</h2>");
		$.post(modMove, {vid:'TT2',uaid:userID}, function(response) {
			message(response);
		});
	}
	function mod_userUnactive(userID) {
		message("<h2>Dezaktywacja...</h2>");
		$.post(modMove, {vid:'TT2',uuaid:userID}, function(response) {
			message(response);
		});
	}

<?php
	}
?>