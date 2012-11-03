<?php
	class mainContent {
		
		//Zmienne pomocnicze
		var $table = 'tablicacms_users';

		
		// Metoda CSS
		function myCSS() {
		return '	
		@import url(\'lib/shadowbox/shadowbox.css\');
		#apigui .block {
			background: white;
			margin: 5px 10px;
			box-shadow: -1px 1px 5px #CCC;
			border: 1px solid #CCC;
			border-radius: 5px;
			width: 35%;
			color: black;
			padding: 15px;
			display: inline-block;
			text-align: left;
			font-size: 12px;
			padding-right: 100px;
			position: relative;
		}
		#apigui .block .description {
			display: block;
			padding-left: 2px;
		}
		#apigui .block input[type~=text], #apigui .block input[type~=password] {
			width: 100% !important;
		}
		#apigui .block input[type~=radio] {
			margin: 3px 5px;
		}
		#apigui .block select {
			margin: 5px 0px;
			width: 100%;
		}
		#apigui .block h2 {
			font-weight: normal !important;
		}
		';
		}
		
		//Podstrony
		function subpages() {
			return array(
				'Użytkownicy' => 'list',
				'Dodaj nowego' => 'add',
				'Masowy Mailing' => 'mass_mail',
			);
		}
		
		//Wywolanie podstron
		function init($get) {
			switch($get) {
				case 'list': $this->users(); break;
				case 'add': $this->add_user(); break;
				case 'mass_mail': $this->mass_mail(); break;
				case 'edit': $this->edit(); break;
				default: $this->users();
			}
		}

		function users() {	
			if(isset($_GET['del'])) $this->delete($_GET['del']);
			kernel::loadLib('apiconfirm');
			apiconfirm::load();
			echo '<script src="lib/shadowbox/shadowbox.js"></script>
				<script type="text/javascript">
					Shadowbox.init();
				</script>';
			echo '<div id="apigui">
					<table>
						<tr>
							<th>login</th>
							<th>e-mail</th>
							<th>daty</th>
							<th>adresy ip</th>
							<th>obiekty</th>
							<th>pozostałe</th>
							<th>opcje</th>
						</tr>'.
				$this->pobierzUserow('<tr><td>#LOGIN#</td> <td>#E-MAIL#</td> <td><b>Zarejestrowany:</b> #REG_DATE#<br/><b>Ostatnio aktywny:</b> #LAST_DATE#</td> <td><b>IP rejestracji:</b> #REG_IP#<br/><b>Ostatnie IP:</b> #LAST_IP#</td> <td>#OBJECTS#</td> <td><b>Aktywny:</b> #ACTIVE#<br/><b>Status:</b> #STATUS#</td> <td><a href="?go=users&amp;feature=edit&amp;id=#ID#" rel="shadowbox;width=480">edytuj</a>, <a href="?go=users&amp;feature=list&amp;del=#ID#" title="Czy na pewno chcesz usunąć użytkownika <b>#LOGIN-EMPTY#</b>?" class="apiconfirm">usuń</a></td></tr>',$_GET['page'],10)	
				.'</table>
				<div style="margin-top:10px; text-align: center;">
				'.$this->pagination(' <a href="?go=users&page=#">&laquo;</a> ',' <a href="?go=users&page=#">#</a> ', ' [ # ] ', ' <a href="?go=users&page=#">&raquo;</a> ',@$_GET['page'],10).'
				</div>
			</div>';
			
		}
		
		function add_user() {
			if(isset($_POST['save'])) {
				if(!empty($_POST['login']) && !empty($_POST['email']) && !empty($_POST['password'])) { 
					$reg_ip = $_SERVER['REMOTE_ADDR']; $reg_date = date('Y-m-d H:i:s');
					$query = mysql_query("INSERT INTO `$this->table` (user, email, pass, date, ip, rank) 
					VALUES ('".$_POST['login']."', '".$_POST['email']."', '".md5($_POST['password'])."', '$reg_date', '$reg_ip', '".$_POST['status']."')") or die(mysql_error());
					if($query) kernel::make_notify("Użytkownik został dodany");
					else kernel::make_notify("Nie udało się dodać użytkownika");
				}
				else kernel::make_notify("Uzupełnij wszystkie pola");
			}
			echo '<div id="apigui">
				<form method="post" action="?go=users&feature=add">
					<div id="options"><input type="submit" value="Zapisz" name="save"></div>
					<div class="block">
					<h2>Login</h2>
						<span class="description">Nazwa użytkownika.</span>
						<input type="text" name="login" />
					</div>
					<div class="block">
					<h2>E-Mail</h2>
						<span class="description">Adres wirtualnej skrzynki pocztowej.</span>
						<input type="text" name="email" />
					</div>
					<div class="block">
					<h2>Hasło</h2>
						<span class="description">Hasło dostępu użytkownika.</span>
						<input type="password" name="password" />
					</div>
					<div class="block">
					<h2>Status</h2>
						<span class="description">Określa rangę w aplikacji.</span>
						<select name="status">
							<option value="2">użytkownik</option>
							<option value="1">moderator</option>
							<option value="0">administrator</option>
						</select>
					</div>
				</form>
			</div>';
		}
		
		function mass_mail() {
			if(isset($_POST['send'])) {
				if(!empty($_POST['subject']) && !empty($_POST['content'])) { 
					$settings =  mysql_fetch_array(mysql_query("SELECT `title` FROM `tentego_settings`"));
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= "Content-Type: text/html;charset=utf-8\n";
					$headers .= "Content-Transfer-Encoding: 8bit\n";
					$headers .= "FROM: ".$settings['title']." <info@".$_SERVER['SERVER_NAME'].">";
                    $subject = $_POST['subject'];
                    $content = nl2br($_POST['content']);
                    $user_query = mysql_query("SELECT * FROM `tablicacms_users`");
                    
                    while($user = mysql_fetch_array($user_query)) {
                        mail($user['email'], $subject, $content, $headers) or die($error = true);
                    }
					if($error) kernel::make_notify("Nie udało się wysłać wiadomości");
					else kernel::make_notify("Wiadomość została poprawnie wysłana");
				}
				else kernel::make_notify("Uzupełnij wszystkie pola");
			}
			echo '<div id="apigui">
				<form method="post" action="?go=users&feature=mass_mail">
					<div id="options"><input type="submit" value="Wyślij" name="send"></div>
					<div class="block" style="display:block;width:80%;">
					<h2>Tytuł</h2>
						<input type="text" name="subject" />
					</div>
					<div class="block" style="display:block;width:80%;">
					<h2>Treść</h2>
						<textarea name="content"></textarea>
					</div>
				</form>
			</div>';
		}
		
		function edit() {
			ob_end_clean();
			echo '
			<!DOCTYPE html>
			<html>
			<head>
			<title>Edytowanie</title>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<link rel="stylesheet" type="text/css" href="style.css" />
			<link rel="stylesheet" type="text/css" href="css/apigui.css" />
			<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
			<style type="text/css">
			* {
			margin:0;
			}
			html,body {
				width:auto;
				min-width:0;
				overflow:hidden;
			}
			body {
				background:#60d4ff;
			}
			#header {
				padding-top:5px;
				background:#FFF;
				box-shadow:0px 0px 5px #CCC;
			}
			#content {
				color:#FFF;
				margin:0;
			}
			#image {
				position:absolute;
				z-index:-2;
				opacity:0.05;
				width:100%;
			}
			td, th {
				padding:3px;
				font-size:16px;
				text-shadow:1px 1px 0px #4190ad;
				color:#FFF;
			}
			th { font-weight:bold; font-size:16px !important; font-family:Arial; }
			td input {
				font-size:16px !important;
			}
			#save {
				position:absolute;
				bottom:0;
				left:0;
				background:#fafafa;
				padding:20px;
				width:100%;
			}
			.notify_div, #notify {
				width:360px !important;
				text-align:center;
				color:#2c2c2c;
				text-shadow:#fffc7d;
			}
			#notify {
				background:#fffec4;
				border-color:#fffc80;
				margin-left:-180px;
			}
			.description {
				padding-top:4px;
				padding-left:3px;
				font-size:12px;
			}
			textarea {
				width: 95% !important;
				height: 100px;
			}
			</style>
			</head>
			<body>
			<div id="apigui">';
			
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `id`=".$_GET['id']);
			if(mysql_num_rows($query)) {
				if(isset($_POST['save'])) {
					if(!empty($_POST['password'])) {
						if(mysql_query("UPDATE `$this->table` SET `user`='".$_POST['login']."', `pass`='".md5($_POST['password'])."', `email`='".$_POST['email']."', `active`='".$_POST['active']."', `rank`='".$_POST['status']."' WHERE `id`=".$_GET['id'])) { $query = mysql_query("SELECT * FROM `$this->table` WHERE `id`=".$_GET['id']); kernel::make_notify("Zapisano"); }
						else kernel::make_notify("Bład podczas zapisu.");
					} else {
						if(mysql_query("UPDATE `$this->table` SET `user`='".$_POST['login']."', `email`='".$_POST['email']."', `active`='".$_POST['active']."', `rank`='".$_POST['status']."' WHERE `id`=".$_GET['id'])) { $query = mysql_query("SELECT * FROM `$this->table` WHERE `id`=".$_GET['id']); kernel::make_notify("Zapisano"); }
						else kernel::make_notify("Bład podczas zapisu.");			
					}
				}
				$user = mysql_fetch_array($query);
				$date_from_db = strtotime($ad['date']);
				
				echo '<form method="post" action="?go=users&amp;feature=edit&amp;id='.$user['id'].'">';						
				echo '
					<div id="content">
						<table>
						<tr>
							<th>Login:</th>
							<td><input type="text" name="login" value="'.$user['user'].'" /></td>
						</tr>
						<tr>
							<th>Nowe hasło:</th>
							<td><input type="password" name="password" /></td>
						</tr>
						<tr>
							<th>E-Mail:</th>
							<td><input type="text" name="email" value="'.$user['email'].'" /></td>
						</tr>
						<tr>
							<th>Status:</th>
							<td>
								<select name="status">
									<option value="3" '.($user["rank"]==3?'selected="selected"':'').'>zbanowany</option>
									<option value="2" '.($user["rank"]==2?'selected="selected"':'').'>użytkownik</option>
									<option value="1" '.($user["rank"]==1?'selected="selected"':'').'>moderator</option>
									<option value="0" '.($user["rank"]==0?'selected="selected"':'').'>administrator</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Aktywny:</th>
							<td>
								<select name="active">
									<option value="1" '.($user["active"]==1?'selected="selected"':'').'>tak</option>
									<option value="0" '.($user["active"]==0?'selected="selected"':'').'>nie</option>
								</select>
							</td>
						</tr>
						</table>
						<div id="save"><input type="submit" name="save" value="Zapisz zmiany" /></div>
					</div>
				';
																					   
				echo '</form>';
			}
			
			echo '
			</div>
			<div id="notify"></div>
			</body>
			</html>
			';
			if(kernel::get_notify(NULL, 1) > 0) {
				echo '<script type="text/javascript">
					$(document).ready(function(){
						$("#notify").show().html(\'';
				
				echo kernel::get_notify(NULL, 0,"<div class=\"notify_div\">#CONTENT#</div>");
					
				echo '\').animate({"top":"-15px"}, 500);
					setTimeout(function(){ $("#notify").fadeOut() }, 3000);
					$("#notify").click(function() { $(this).fadeOut(); });
					});
					</script>';
			}
			kernel::destroy_notify();
			exit();
		}
	
		
		/*****************************************/
		/* DODATKOWE FUNKCJE
		/*****************************************/
		function pobierzUserow($pattern, $currentPage, $objPerPage) {
			$i = 1;
			$return = NULL;
			$result = NULL;
			
			if(isset($currentPage) && is_numeric($currentPage) && $currentPage > 0) {
				$page = mysql_real_escape_string($currentPage-1)*$objPerPage;
			}
			else $page = 0;
			
			$query = mysql_query("SELECT * FROM `$this->table` ORDER BY `id` DESC LIMIT $page,$objPerPage") or die(mysql_error());
			
			while($user = mysql_fetch_array($query)) {
				$return = str_replace("#ID#",$user['id'], $pattern);
				switch($user['rank']) {
					case 3: $login = '<s>'.$user['user'].'</s>'; break;
					case 2: $login = $user['user']; break;
					case 1: $login = '<span style=\'color:green;\'>'.$user['user'].'</span>'; break;
					case 0: $login = '<span style=\'color:red;\'>'.$user['user'].'</span>';
				}
				$return = str_replace("#LOGIN#",$login, $return);
				$return = str_replace("#LOGIN-EMPTY#",$user['user'], $return);
				$return = str_replace("#E-MAIL#",$user['email'], $return);			
				$return = str_replace("#REG_DATE#",date('Y-m-d H:i',strtotime($user['date'])), $return);
				if($user['last_seen']==='0000-00-00 00:00:00') $last_seen='- - - - -'; else $last_seen=date('Y-m-d H:i',strtotime($user['last_seen']));
				$return = str_replace("#LAST_DATE#",$last_seen, $return);	
				$return = str_replace("#REG_IP#",$user['ip'], $return);
				$return = str_replace("#LAST_IP#",$user['last_ip'], $return);
				$objects = mysql_num_rows(mysql_query("SELECT `owner` FROM `tentego_img` WHERE `owner`='".$user['id']."'"));
				$return = str_replace("#OBJECTS#",$objects, $return);
				switch($user['active']) {
					case 0: $active = '<span style="color:red;">nie</span>'; break;
					case 1: $active = '<span style="color:green;">tak</span>';
				}	
				$return = str_replace("#ACTIVE#",$active, $return);
				switch($user['rank']) {
					case 3: $status = 'zbanowany'; break;
					case 2: $status = 'użytkownik'; break;
					case 1: $status = 'moderator'; break;
					case 0: $status = 'administrator';
				}			
				$return = str_replace("#STATUS#",$status, $return);
			
				$i++;
				$result .= $return;
			}
			if($return == NULL) kernel::make_notify("Brak rekordów");
			return $result;
		}
		
		function delete($id) {
			if(mysql_query("DELETE FROM `$this->table` WHERE `id`=".$id)) {
				 kernel::make_notify("Pomyślnie usunięto użytkownika");
			}
			else kernel::make_notify("Wystąpił błąd podczas usuwania użytkownika");
		}
		
		function pagination($back_pattern, $pattern, $current_pattern, $next_pattern, $current, $count) {
		
			$query = mysql_query("SELECT * FROM `$this->table`");
			if($current<1 || !isset($current) || !is_numeric($current)) $current = 1;
			$i = ceil(mysql_num_rows($query)/$count);
			$return = NULL;
			if($current > 1) $return .= str_replace("#",$current-1,$back_pattern);
			if($current > 6) $return .= str_replace("#",1,$pattern).' ... ';
			for(($current>5?$j=$current-5:$j=1);$j<$current;$j++) {
				$text = str_replace("#",$j,$pattern);
				$return .= $text;
			}
			$return .= str_replace("#", $current, $current_pattern);
			for($j=$current+1;($current+5<$i?$j<$current+6:$j<=$i);$j++) {
				$text = str_replace("#",$j,$pattern);
				$return .= $text;
			}
			if($current+5<$i) $return .= ' z '.str_replace("#", $i, $pattern);
			if($current < $i && $i > 1) $return .= str_replace("#",$current+1,$next_pattern);
			
			return $return;
		}
	
	
	}
?>