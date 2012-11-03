<?php
	ob_start();
	
	//Błędy :
	// v#1 - nie przesłano żadnych danych wejśiowych
	
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
		if(isset($_GET['get'])) {
				if(is_numeric($_GET['get'])) {
					$_GET['get'] = mysql_real_escape_string($_GET['get']);
					$query = mysql_query("SELECT COUNT(*),SUM(vote) FROM `tentego_img_vote` WHERE `object_id`=".$_GET['get']);
					$query_s = mysql_fetch_array($query);
					if($query_s['COUNT(*)'] > 0) echo ceil(($query_s['SUM(vote)']/$query_s['COUNT(*)'])*100);
					else echo "?";
				}
		}
		else if(!$user->verifyLogin()) echo 'Musisz być zalogowany, żeby oddać głos.';
		else {
			if(isset($_POST['vid']) && @$_POST['vid']==='TT2') {
				if(!isset($_POST['id']) OR !is_numeric($_POST['id'])) exit();
				$verify = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_vote` WHERE `object_id`='".$_POST['id']."' AND `user_id`='".$user->userInfo('id')."'"));
				if($verify) echo "Już głosowałeś/aś.";
				else {
					if(mysql_num_rows(mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$_POST['id'])) == 0) { echo 'Obiekt nie istnieje.'; exit(); }
					switch(@$_GET['type']) {
						case 'up':
							if(mysql_query("INSERT INTO `tentego_img_vote` (`object_id`,`user_id`,`vote`) VALUES (".$_POST['id'].",".$user->userInfo('id').",'1')")) echo "Głos oddany.";
							else echo "Wystąpił błąd podczas oddawania głosu.";
						break;
						case 'down':
							if(mysql_query("INSERT INTO `tentego_img_vote` (`object_id`,`user_id`,`vote`) VALUES (".$_POST['id'].",".$user->userInfo('id').",'0')")) echo "Głos oddany.";
							else echo "Wystąpił błąd podczas oddawania głosu.";
						break;
					}
				}
			}
			else echo 'Wystąpił błąd - nie można oddać głosu (v#1)';
		}
		
	ob_end_flush();
?>