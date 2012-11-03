<?php
	ob_start();

	//Loading core of application
		require_once("_core/kernel.php");
		$kernel = new kernel();

	//Loading settings of application
		$kernel->load_module("manager");
		$kernel->load_module("users");
	//Loading main theme
		$img = $kernel->manager;
		$user = $kernel->users;

	//Loading functions of user
		$user->sessionTools();
	
	if($user->verifyLogin() && $user->userInfo('rank') <= 1) {
		if(isset($_POST['vid']) && isset($_POST['mid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['mid'])) {
				$_POST['mid'] = mysql_real_escape_string($_POST['mid']);
				$query = mysql_query("SELECT `is_waiting` FROM `tentego_img` WHERE `id`=".$_POST['mid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Obiekt nie istienieje.';
				else {
					$item = mysql_fetch_array($query);
					if($item['is_waiting'] == 1)
						echo (mysql_query("UPDATE `tentego_img` SET `is_waiting`=0, `rel_date`='".date("YmdHis")."' WHERE `id`=".$_POST['mid']))?'Przeniesiono na główną.':'Wystąpił błąd podczas przenoszenia';
					else
						echo (mysql_query("UPDATE `tentego_img` SET `is_waiting`=1 WHERE `id`=".$_POST['mid']))?'Przeniesiono do poczekalni.':'Wystąpił błąd podczas przenoszenia';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['aid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['aid'])) {
				$_POST['aid'] = mysql_real_escape_string($_POST['aid']);
				$query = mysql_query("SELECT `is_waiting` FROM `tentego_img` WHERE `id`=".$_POST['aid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Obiekt nie istienieje.';
				else {
					$item = mysql_fetch_array($query);
					echo (mysql_query("UPDATE `tentego_img` SET `is_waiting`=2 WHERE `id`=".$_POST['aid']))?'Przeniesiono do archiwum. Operację można cofnąć klikając pierwszą opcję przenoszenia.':'Wystąpił błąd podczas przenoszenia';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['did'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['did'])) {
				$_POST['did'] = mysql_real_escape_string($_POST['did']);
				$query = mysql_query("SELECT `is_waiting`,`type`,`src` FROM `tentego_img` WHERE `id`=".$_POST['did']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Obiekt nie istienieje.';
				else {
					$item = mysql_fetch_array($query);
					echo (mysql_query("DELETE FROM `tentego_img` WHERE `id`=".$_POST['did']))?'Usunięto obiekt.':'Nie udało się usunąć obiektu.';
					echo '<br />';
					if($item['type'] == 'img')
						echo (unlink('upload/'.$item['src']))?'Usunięto obrazek z serwera':'Nie udało się usunąć obrazku z serwera';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['ubid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['ubid'])) {
				$_POST['ubid'] = mysql_real_escape_string($_POST['ubid']);
				$query = mysql_query("SELECT `rank` FROM `tablicacms_users` WHERE `id`=".$_POST['ubid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Użytkownik nie istienieje.';
				else {
					echo (mysql_query("UPDATE `tablicacms_users` SET `rank` = '3' WHERE `id`=".$_POST['ubid']))?'Zablokowano użytkownika.':'Nie udało się zablokować użytkownika.';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['uubid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['uubid'])) {
				$_POST['uubid'] = mysql_real_escape_string($_POST['uubid']);
				$query = mysql_query("SELECT `rank` FROM `tablicacms_users` WHERE `id`=".$_POST['uubid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Użytkownik nie istienieje.';
				else {
					echo (mysql_query("UPDATE `tablicacms_users` SET `rank` = '2' WHERE `id`=".$_POST['uubid']))?'Odblokowano użytkownika.':'Nie udało się odblokować użytkownika.';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['uaid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['uaid'])) {
				$_POST['uaid'] = mysql_real_escape_string($_POST['uaid']);
				$query = mysql_query("SELECT `active` FROM `tablicacms_users` WHERE `id`=".$_POST['uaid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Użytkownik nie istienieje.';
				else {
					echo (mysql_query("UPDATE `tablicacms_users` SET `active` = '1' WHERE `id`=".$_POST['uaid']))?'Aktywowano użytkownika.':'Nie udało się aktywować użytkownika.';
				}
			}
		}
		if(isset($_POST['vid']) && isset($_POST['uuaid'])) {
			if($_POST['vid'] == 'TT2' && is_numeric($_POST['uuaid'])) {
				$_POST['uuaid'] = mysql_real_escape_string($_POST['uuaid']);
				$query = mysql_query("SELECT `active` FROM `tablicacms_users` WHERE `id`=".$_POST['uuaid']);
				$verify = mysql_num_rows($query);
				if($verify != 1) echo 'Użytkownik nie istienieje.';
				else {
					echo (mysql_query("UPDATE `tablicacms_users` SET `active` = '0' WHERE `id`=".$_POST['uuaid']))?'Dezaktywowano użytkownika.':'Nie udało się dezaktywować użytkownika.';
				}
			}
		}
	}
?>
				