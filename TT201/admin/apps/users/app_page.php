<?php
class users {
	
	var $table = 'tablicacms_users';
	var $msg = NULL;
	
	function register($register,$email_active,$page_title) {
		
		if($register) {				
			if(isset($_POST['submit'])) {				
				if(empty($_POST['login']) || empty($_POST['pass']) || empty($_POST['email']) || empty($_POST['question']) || empty($_POST['rules'])) $this->msg('Wypełnij wszystkie pola!',1);
				else {
					$login = mysql_real_escape_string(htmlspecialchars($_POST['login']));
					$pass = md5($_POST['pass']);
					$email = mysql_real_escape_string($_POST['email']);
					$question = htmlspecialchars(strtolower($_POST['question']));
					$rules = $_POST['rules'];
					$date = date('Y-m-d H:i:s');
					$ip = $_SERVER['REMOTE_ADDR'];
					
					if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $this->msg('Adres email jest niepoprawny!',1);
					if($_SESSION['img_number'] != $question) $this->msg('Kod weryfikacyjny nie jest poprawny!',1);
					$query_login = mysql_query("SELECT * FROM `$this->table` WHERE `user`='$login'");
					$query_email = mysql_query("SELECT * FROM `$this->table` WHERE `email`='$email'");
					if(mysql_fetch_array($query_login)) $this->msg('Taki użytkownik już istnieje. Wymyśl inny login.',1);
					if(mysql_fetch_array($query_email)) $this->msg('Użytkownik o takim adresie e-mail już istnieje.',1);
				}
			
				if($this->msg == NULL) {			
					if($email_active) {
						$string = "aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789";
						$active_code = NULL;
						for($i=0;$i<12;$i++){
							$char_position = rand(0,strlen($string));
							$active_code .= $string{$char_position};
						}	
						$query = mysql_query("INSERT INTO `$this->table` (user, email, pass, date, ip, rank, active) VALUES ('$login', '$email', '$pass', '$date', '$ip', '2', '$active_code')");
						if($query && $this->mail_activation($email,$page_title,$active_code)) $this->msg('Rejestracja przebiegła pomyślnie, jednakże ta witryna wymaga aktywacji. Sprawdź swój email.',3);
						else $this->msg('Nie udało się zarejestrować nowego użytkownika.',1);
					}
					else {
						$query = mysql_query("INSERT INTO `$this->table` (user, email, pass, date, ip, rank, active) VALUES ('$login', '$email', '$pass', '$date', '$ip', '2', '1')");
						if($query) $this->msg('Rejestracja przebiegła pomyślnie! Możesz się teraz zalogować.',3);
						else $this->msg('Nie udało się zarejestrować nowego użytkownika.',1);
					}
				}
			}
		}
	}
	
	function registerForm($rewrite, $settings) {
		global $kernel;
		if(!isset($_GET['active'])) {
			if($settings['register']) {
				$this->register($settings['register'],$settings['req_code'],$settings['title']);
				echo '<form action="'.$rewrite->register.'" method="post">
					<label>Login <span class="required">*</span></label>
					<input type="text" name="login" maxlength="16" value="'.(isset($_POST['login'])?htmlspecialchars($_POST['login']):'').'" />
					<label>Hasło <span class="required">*</span></label>
					<input type="password" name="pass" value="'.(isset($_POST['pass'])?$_POST['pass']:'').'" />
					<label>E-Mail <span class="required">*</span></label>
					<input type="text" name="email" value="'.(isset($_POST['email'])?htmlspecialchars($_POST['email']):'').'" />
					<label><img src="'.$kernel->host().'/admin/lib/captcha/image.php" alt="Captcha"></label><br/>
					<input type="text" name="question">
					<label>Regulamin <span class="required">*</span></label>
					<input name="rules" type="checkbox" value="1"> Oświadczam, że akceptuję zawarty na stronie <a href="'.$rewrite->rules.'">regulamin</a>.<br/>
					<input type="submit" name="submit" value="Zarejestruj się!" />
				</form>';
			} else $this->msg('Rejestracja nowych użytkowników jest wyłączona.',1);
		} else $this->active();
	}
	
	function active() {
		$code = mysql_real_escape_string(htmlspecialchars($_GET['active']));
		$query = mysql_query("SELECT * FROM `$this->table` WHERE `active`='$code'");
		if(!mysql_num_rows($query)) { $this->msg('Kod aktywacji jest błędny lub konto zostało już aktywowane.',1); }
		else {
			$query = mysql_query("UPDATE `$this->table` SET `active`='1' WHERE `active`='$code'");
			if($query) $this->msg('Konto zostało pomyślnie aktywowane! Możesz się teraz zalogować.',3);
			else $this->msg('Wystąpił błąd podczas aktywacji konta!',1);
		}
	}
	
	
	function msg($text, $type) {
		switch($type) {
			case 1: $this->msg = '<div class="msg error">'.$text.'</div>'; break;
			case 2: $this->msg = '<div class="msg alert">'.$text.'</div>'; break;
			case 3: $this->msg = '<div class="msg good">'.$text.'</div>';
		}
		echo $this->msg;
	}
	
	function mail_activation($email,$page_title,$code) {
		$subject = 'Aktywacja konta na '.$page_title;
		$message = 'Witaj!
		<br/><br/>
		Poniżej znajduje się link aktywacyjny do Twojego konta. Jeżeli to nie Ty rejestrowałeś się na naszej stronie, to po prostu zignoruj tą wiadomość lub usuń.
		<br/><br/>
		http://'.$this->host().'/register.php?active='.$code.'
		<br/><br/>
		Jeśli kliknięcie na powyższy link nie pomoże, wpisz lub skopiuj i wklej go do swojej przeglądarki.
		<br/><br/>
		Wiadomość została wygenerowana automatycznie. Prosimy nie odpowiadać.
		<br/><br/>
		Dziękujemy i pozdrawiamy,<br/>
		Administracja '.$page_title.'.';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "Content-Type: text/html;charset=utf-8\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$headers .= "FROM: Aktywacja konta <register@".$_SERVER['SERVER_NAME'].">";
		if(!mail($email, $subject, $message, $headers)) return false;
		else return true;
	}
	
	function host() {
		return $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/');
	}
	
	////////////////////////////////////////////////////
	// CZESC ODPOWIEDZALNA ZA LOGOWANIE ////////////////
	////////////////////////////////////////////////////
	
	function sessionTools() {
		if(isset($_GET['logout'])) {
			if(($_GET['logout']=='true')) {
				$_SESSION['login'] = array();
				$_SESSION['pass'] = array();
				header('Location:'.$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function setSession($submit, $login, $pass) {
		if(isset($_POST[$submit])) {
			$_SESSION['login'] = mysql_real_escape_string($_POST[$login]);
			$_SESSION['pass'] = md5($_POST[$pass]);
		}
	}
			
	function verifyLogin($rank = NULL) {
		if(isset($_SESSION['login']) && isset($_SESSION['pass'])) {
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `user`='".$_SESSION['login']."' AND `pass`='".$_SESSION['pass']."' AND `active`='1'");
			if(mysql_num_rows($query) == 0) {
				unset($_SESSION['login']);
				unset($_SESSION['pass']);
				return 0;
			}
			else if(mysql_num_rows($query) == 1) { 
				$user = mysql_fetch_array($query);
				if($user['rank'] == 3) header('Location: http://'.$this->host().'/banned.php');
				else {
					$date = date('Y-m-d H:i:s'); $ip = $_SERVER['REMOTE_ADDR'];
					mysql_query("UPDATE `$this->table` SET `last_seen`='$date', `last_ip`='$ip' WHERE `user`='".$_SESSION['login']."'");
					return 1; 
				}
				mysql_query("UPDATE `$this->table` SET `remind`=NULL WHERE `id`='".$user['id']."'");
			}
		}
	}
	
	function loginForm($rewrite) {
		if(isset($_GET['remind'])) {
			if(isset($_GET['code']) && isset($_GET['email'])) {
				if(!empty($_GET['code'])) {
					if(mysql_num_rows(mysql_query("SELECT * FROM `$this->table` WHERE `email`='".$_GET['email']."' AND `remind`='".$_GET['code']."'"))) {
						$new_pass = substr(md5(rand(10000,99999)),0,5);
						mysql_query("UPDATE `$this->table` SET `pass`='".md5($new_pass)."' WHERE `email`='".$_GET['email']."'");
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= "Content-Type: text/html;charset=utf-8\n";
						$headers .= "Content-Transfer-Encoding: 8bit\n";
						$headers .= "FROM: Przypomnienie hasła <remind@".$_SERVER['SERVER_NAME'].">";
						$email = $_GET['email'];
						$subject = 'Przypomnienie hasła. Etap 2.';
						$message = 'Twoje nowe hasło to: '.$new_pass;
						if(!mail($email, $subject, $message, $headers)) $this->msg('Nie udało się wysłać wiadomości z hasłem.');
						else {
							$this->msg('Na twój adres email została wysłana wiadomość zawierająca nowe hasło.',3);
							mysql_query("UPDATE `$this->table` SET `remind`=NULL WHERE `email`='".$_GET['email']."'");
						}
					}
				}
			}
			echo '<form action="'.$rewrite->login.'" method="post">
					<label>Adres e-mail:</label>
					<input type="text" name="email" />
					<br />
					<input type="submit" name="remind" value="Przypomnij" />
				</form>';
		}
		else {
			if(!$this->verifyLogin() && isset($_POST['submit'])) $this->msg('Niepoprawne dane logowania lub Twoje konto jest nieaktywne.',1);
			if(isset($_POST['remind'])) {
				$_POST['email'] = mysql_real_escape_string(htmlspecialchars($_POST['email']));
				if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $this->msg('Adres email jest niepoprawny.',1);
				else {
					$query = mysql_query("SELECT * FROM `$this->table` WHERE `email`='".$_POST['email']."'");
					if(mysql_num_rows($query)) {
						$code = substr(md5(rand(10000,99999)),0,5);
						mysql_query("UPDATE `$this->table` SET `remind`='".$code."' WHERE `email`='".$_POST['email']."'");
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= "Content-Type: text/html;charset=utf-8\n";
						$headers .= "Content-Transfer-Encoding: 8bit\n";
						$headers .= "FROM: Przypomnienie hasła <remind@".$_SERVER['SERVER_NAME'].">";
						$email = $_POST['email'];
						$subject = 'Przypomnienie hasła. Etap 1';
						$message = 'Nastąpiła prośba o przypomnienie hasła w serwisie http://'.$this->host().'.
								<br/> Jeżeli Ty próbowałeś odzyskać hasło to kliknij w poniższy link lub go skopiuj i wklej w pasek adresu:
								<br/> http://'.$this->host().'/'.$rewrite->login.'?remind&email='.$_POST['email'].'&code='.$code.
							    '<br/>Jeżeli nie Ty próbowałeś odzyskać hasło i jest topróba nieporządanego dostępu do konta to usuń tą wiadomość lub zignoruj.';
						if(!mail($email, $subject, $message, $headers)) $this->msg('Nie udało się wysłać wiadomości z przypomnieniem.');
						else $this->msg('Na twój adres email została wysłana wiadomość z linkiem potwierdzającym zmianę.',3);
					}
					else $this->msg('Adres email jest niepoprawny.',1);
				}
			}
					echo '<form action="'.$rewrite->login.'" method="post">
							<label>Login</label>
							<input type="text" name="login" maxlength="16" />
							<label>Hasło</label>
							<input type="password" name="pass" />
							<br/>
							<input type="submit" name="submit" value="Zaloguj się" />
							<div style="text-align:center;"><a href="?remind">Zapomniałeś hasła?</a></div>
						</form>';
		}
	}
	
	////////////////////////////////////////////////////
	// CZESC ODPOWIEDZALNA ZA PROFIL I DANE USERA //////
	////////////////////////////////////////////////////
	
	function changePass($rank = NULL) {
		if(isset($_POST['change_pass'])) {
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `user`='".$_SESSION['login']."' AND `pass`='".md5($_POST['pass'])."'");
			if(mysql_num_rows($query) == 1) {
				$query = mysql_query("UPDATE `$this->table` SET `pass`='".md5($_POST['new_pass'])."' WHERE `user`='".$_SESSION['login']."'");
				if($query) $this->msg('Hasło zostało poprawnie zaktualizowane. Zaloguj się ponownie.',3);
				else $this->msg('Niestety nie udało się zmienić hasła.',1);
			}
			else if(mysql_num_rows($query) == 0) $this->msg('Aktualne hasło nie jest poprawne.',1);;
		}
	}
	
	function changePassForm($rewrite) {
		echo '<form action="'.$rewrite->profile.'" method="post">
				<label>Aktualne hasło</label>
				<input type="password" name="pass">
				<label>Nowe hasło</label>
				<input type="password" name="new_pass">
				<br/>
				<input type="submit" name="change_pass" value="Zapisz" />
			</form>';
	}
	
	function uploadAvatar($input_name, $max_file_size) {
		$overwrite = 1;
		$dir = 'upload/avatars/';
		$file_types = array(1=>'jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG');
		$file_mimes = array(1=>'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
		$file_name = $_SESSION['login'].'_user';
		
		if(isset($_POST['send_avatar'])) {	
			if(!$_FILES[$input_name]['name']) { $this->msg('Nie wybrano pliku do załadowania!',1); }
			else { 
				if(filesize($_FILES[$input_name]['tmp_name']) <= $max_file_size*1024) {
					$file_ex = pathinfo($_FILES[$input_name]['name']);
					$image_info = @getimagesize($_FILES[$input_name]['tmp_name']);
						if(array_search($file_ex['extension'],$file_types) && array_search($image_info['mime'],$file_mimes)) {
							if($overwrite == 0 && file_exists($dir.$file_name.".".$file_ex['extension'])) $this->msg('Taki plik już istnieje.',1);
							if(!move_uploaded_file($_FILES[$input_name]['tmp_name'],$dir.$file_name.".".$file_ex['extension'])) $this->msg('Wgrywanie pliku nie powiodło się.',1);
							else { 
								$upload_dir = $dir.$file_name.".".$file_ex['extension'];
								mysql_query("UPDATE `$this->table` SET `avatar`='$upload_dir' WHERE `user`='".$_SESSION['login']."'");
								
								//Zmina rozmiaru avatara
								list($width, $height, $type, $attr) = getimagesize($upload_dir);
								if($width > 120 || $height > 120) {
									require_once('admin/lib/imageworkshop.lib.php');
									$createFolders = false;
									$backgroundColor = null;
									$imageQuality = 95;
									$imageLayer = new ImageWorkshop(array(
											"imageFromPath" => $upload_dir,
										));									 
									$imageLayer->resizeInPixel(120, 120, true);									
									$imageLayer->save($dir, $file_name.".".$file_ex['extension'], $createFolders, $backgroundColor, $imageQuality);
								}
								$this->msg('Avatar został pomyślnie załadowany.',3);
							}
						}
					else $this->msg('Niedozwolone rozszerzenie lub typ pliku!',1);
				}
				else $this->msg('Wybrany plik jest za wielki! Dozwolony rozmiar to '.$max_file_size.' kB.',1);
			}
		}
    }
	
	function uploadAvatarForm($rewrite) {
		return '<form action="'.$rewrite->profile.'" method="post" enctype="multipart/form-data">
			<input type="file" name="avatar" size="40">
			<input type="submit" name="send_avatar" value="Wgraj" />
			</form>';
	}
	
	function userTemplateInfo($for_guest = NULL, $for_user = NULL, $user_id = NULL) {
		global $rewrite, $kernel;
		if(!$this->verifyLogin() && $for_guest != NULL) {
			echo $for_guest;
		}
		if($user_id == NULL) {
			if($this->verifyLogin() && $for_user != NULL) {
				$query = mysql_query("SELECT * FROM `$this->table` WHERE `user` = '".$_SESSION['login']."'");
				$user = mysql_fetch_array($query);
				$return = NULL;
				$result = NULL;
					$return = str_replace("#ID#",$user['id'], $for_user);
					$return = str_replace("#LOGIN#",$user['user'], $return);
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
					if($user['avatar'] != NULL) $avatar = '<img src="'.$kernel->host().'/'.$user['avatar'].'" alt="avatar"/>';
					else $avatar = '<img src="'.$kernel->host().'/upload/avatars/default_profile.png" alt="avatar"/>';
					$return = str_replace("#AVATAR#",$avatar, $return);
					# MODERATOR #
					$mod_tools = '<div class="mod_tools">';
					$mod_tools .= '<a href="#">Zablokuj użytkownika</a>';
					$mod_tools .= '<a href="#">Aktywuj konto</a>';
					$mod_tools .= '</div>';
				
					$return = str_replace("#MOD_TOOLS#", $mod_tools, $return);
					# KONIEC MODERATOR #
					$result .= $return;
					echo $result;
			}
		}
		else {
			if(is_numeric($user_id)) {
				if($for_user != NULL) {
					$query = mysql_query("SELECT * FROM `$this->table` WHERE `id` = '$user_id'");
					$user = mysql_fetch_array($query);
					if($user) {
						$return = NULL;
						$result = NULL;
						
						$return = str_replace("#ID#",$user['id'], $for_user);
						$return = str_replace("#LOGIN#",$user['user'], $return);
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
						if($user['avatar'] != NULL) $avatar = '<img src="'.$kernel->host().'/'.$user['avatar'].'" alt="avatar"/>';
						else $avatar = '<img src="'.$kernel->host().'/upload/avatars/default.png" alt="avatar"/>';
						$return = str_replace("#AVATAR#",$avatar, $return);
						
						# MODERATOR #
						$return = str_replace("#MOD_TOOLS#", $this->mod_tools($user['id']), $return);
						# KONIEC MODERATOR #
						
						$result .= $return;
						echo $result;
					}
					else header('Location: '.$rewrite->index);
				}
				
			}
			else header('Location: '.$rewrite->index);
		}
	}
	
	function userInfo($col, $user_id = NULL) {
		if($user_id == NULL) {
			if(isset($_SESSION['login']) && isset($_SESSION['pass'])) {
				$query = mysql_fetch_array(mysql_query("SELECT `$col` FROM `$this->table` WHERE `user`='".$_SESSION['login']."' AND `pass`='".$_SESSION['pass']."'"));
				return $query[$col];
			}
		}
		else {
			if(is_numeric($user_id)) {
				$query = mysql_fetch_array(mysql_query("SELECT `$col` FROM `$this->table` WHERE `id`='$user_id'"));
				return $query[$col];
			}
		}
	}
	
	private function mod_tools($user_id) {
		if($this->verifyLogin()) {
			if($this->userInfo('rank') <= 1) {
				if($this->userInfo('rank', $user_id) > 1) {
					$mod_tools = '<div class="mod_tools">';	
					if($this->userInfo('rank', $user_id) == 3)
						$mod_tools .= '<a href="#" onClick="mod_userUnblock('.$user_id.'); return false;">Odblokuj Użytkownika</a>';
					else $mod_tools .= '<a href="#" onClick="mod_userBlock('.$user_id.'); return false;">Zablokuj Użytkownika</a>';
					if($this->userInfo('active', $user_id) == 0)
						$mod_tools .= '<a href="#" onClick="mod_userActive('.$user_id.'); return false;">Aktywuj Użytkownika</a>';
					else $mod_tools .= '<a href="#" onClick="mod_userUnactive('.$user_id.'); return false;">Dezaktywuj Użytkownika</a>';
					$mod_tools .= '</div>';
					
					return $mod_tools;
				}
				else return NULL;
			}
		}
	}
	
}
?>