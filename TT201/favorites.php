<?php
	ob_start();
	//Loading core of application
		require_once("_core/kernel.php");
		$kernel = new kernel();

	//Loading settings of application
		$kernel->load_module("settings");
			require_once("_core/rewrite.php");
		$kernel->load_module("manager");
		$kernel->load_module("ads");
		$kernel->load_module("users");
		$kernel->load_module("inbox");
	//Loading main theme
		$page = $kernel->settings;
		$theme = $page->load('theme');
		$img = $kernel->manager;
		$ads = $kernel->ads;
		$user = $kernel->users;
		$inbox = $kernel->inbox;
		$allowed = 'page,theme,img,ads,user,inbox,rewrite';

	//Loading functions of user
		$user->sessionTools();
		if(!$user->verifyLogin()) {
				header('Location: '.$rewrite->login);
				exit();
		}

	//Favorites api
		if(isset($_GET['do'])) {
			ob_end_clean();
			
			function check($id) {
				global $user;
				return mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_fav` WHERE `object_id`=".$id." AND `user_id`=".$user->userInfo('id')));
			}
			if(isset($_GET['check'])) {
				if(is_numeric($_GET['check'])) {
					$check = check($_GET['check']);
					echo $check;
				}
			}
			else if(isset($_GET['mf'])) {
				if(is_numeric($_GET['mf'])) {
					$check = check($_GET['mf']);
					if($check == 0) {
						if(mysql_query("INSERT INTO `tentego_img_fav` (`object_id`,`user_id`) VALUES (".$_GET['mf'].",".$user->userInfo('id').")")) echo 'Dodano do ulubionych.';
						else echo 'Wystąpił błąd podczas dodawania do ulubionych.';
					}
					else {
						if(mysql_query("DELETE FROM `tentego_img_fav` WHERE `object_id`=".$_GET['mf']." AND `user_id`=".$user->userInfo('id'))) echo 'Usunięto z ulubionych.';
						else echo 'Wystąpił błąd podczas usuwania z ulubionych.';
					}
				}
			}
			exit();
		}


	//Including header of theme
		$kernel->load_content("_themes/".$theme."/header.php", $allowed);
	//Including content of theme
		$kernel->load_content("_themes/".$theme."/favorites.php", $allowed);
	//Including footer of theme
		$kernel->load_content("_themes/".$theme."/footer.php", $allowed);
	ob_end_flush();
?>