<?php
	ob_start();
	session_start();
	if(!isset($_GET['step'])) header("LOCATION: install.php?step=1");
	if(!isset($_SESSION['install'])) {
		$_SESSION['install'] = array();
		$_SESSION['install']['hostaddress'] = 'localhost';
		$_SESSION['install']['db_name'] = 'tentego';
		$_SESSION['install']['db_user'] = NULL;
		$_SESSION['install']['db_pass'] = NULL;
		$_SESSION['install']['admin_name'] = NULL;
		$_SESSION['install']['admin_pass'] = NULL;
		$_SESSION['install']['admin_pass_check'] = NULL;
		$_SESSION['install']['admin_email'] = NULL;
		$_SESSION['install']['admin_imie'] = NULL;
		$_SESSION['install']['admin_nazwisko'] = NULL;
	}
	if(isset($_POST['db'])) {
		$_SESSION['install']['hostaddress'] = $_POST['hostaddress'];
		$_SESSION['install']['db_name'] = $_POST['db_name'];
		$_SESSION['install']['db_user'] = $_POST['db_user'];
		$_SESSION['install']['db_pass'] = $_POST['db_pass'];
	}
	if(isset($_POST['admin'])) {
		$_SESSION['install']['admin_name'] = $_POST['admin_name'];
		$_SESSION['install']['admin_pass'] = $_POST['admin_pass'];
		$_SESSION['install']['admin_email'] = $_POST['admin_email'];
		$_SESSION['install']['admin_imie'] = $_POST['admin_imie'];
		$_SESSION['install']['admin_nazwisko'] = $_POST['admin_nazwisko'];
	}
	
	
	//Etapy
	$etap = (int) $_GET['step'];
	
	//Aplikacje zaimplementowane w system
	$apps = array("home","install",'manager','settings','ads','users','inbox');

	function app_install($app_name) {
		$xml = simplexml_load_file("apps/$app_name/about.xml");
		
		echo "<h2>Instalowanie <span style=\"color:#f9a800;\">$xml->name</b></h2>";
		
		$sql_file = "apps/$app_name/".$xml->sql_tables.".sql";
		if(file_exists($sql_file)) {
		
			$sql = file_get_contents("apps/$app_name/".$xml->sql_tables.".sql");
		
			$query = explode("!@#",$sql);
		
			foreach($query as $sql_query) {
				if(mysql_query($sql_query)) echo '<div class="sql_ok">'.nl2br(htmlspecialchars($sql_query, ENT_QUOTES)).'</div>';
				else { echo '<div class="sql_error">'.nl2br(htmlspecialchars($sql_query, ENT_QUOTES)).'</div>'; exit(); }
			}
		}
		mysql_query("INSERT INTO `tablicacms_apps` (`dir`) VALUES ('".$app_name."')");
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Instalacja TenTego2</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="Shortcut icon" href="img/favicon.png" />
		<script type="text/javascript" src="img/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="css/apigui.css" />
		<style type="text/css">
			#options {
				background:#F3F3F3;
				border-top:1px solid #E6E6E6;
				border-bottom:1px solid #F0F0F0;
				padding:15px;
			}
			h2 {
				padding:10px;
				border-bottom:1px solid #4D4D4D;
			}
			#cont {
				padding:10px;
			}
			table {
				margin:0;
			}
			#pods td {
				padding:5px 10px;
				color:#4D4D4D;
				font-size:16px;
			}
			.ok {
				padding:10px;
				color:green;
				border:1px solid green;
			}
			.error {
				padding:10px;
				color:red;
				border:1px solid red;
			}
			.sql_ok {
				background:#c8ffb0;
				color:#000;
				padding:10px;
			}
			.sql_error {
				background:#ff9b9d;
				color:#000;
				padding:10px;
			}
		</style>
	</head>
	<body>
		<div id="menu_header">
			<ul id="menu">
				<li style="font-variant:small-caps; padding:0 20px; border-top:1px solid yellow;">Tablica<span style="color:#fced30;">CMS</span></li>
				<li><a href="#">Instalacja</a></li>
			</ul>
		</div>
		<div id="content">
			<div id="header">
				<h1 style="background:url(img/leaf.png) no-repeat top left;">Witaj w instalatorze tablicy!</h1>
			</div>
			<div id="submenu">
					<ul>
						<?php
						switch($etap) {
							case 1: $active = array('class="active"','','',''); break;
							case 2: $active = array('','class="active"','',''); break;
							case 3: $active = array('','','class="active"',''); break;
							case 4: $active = array('','','','class="active"'); break;
							default: $active = array('','','','');
						}
						echo '<li><a href="install.php?step=1" '.$active[0].'>Etap 1 - Witaj!</a></li>';
						echo '<li><a href="install.php?step=2" '.$active[1].'>Etap 2 - Baza danych</a></li>';
						echo '<li><a href="install.php?step=3" '.$active[2].'>Etap 3 - Konfiguracja administratora</a></li>';
						echo '<li><a href="install.php?step=4" '.$active[3].'>Etap 4 - Podsumowanie</a></li>';
						?>
					</ul>
			</div>
			<div id="apigui">
			<?php
				switch($etap) {
					case 1: 
							echo '<div style="font-size:14px; color:#000; padding:10px; line-height:20px; text-align:center;">
								<img src="img/tentego2tablicacms.png" alt="TenTego 2 - Instalacja" />
							<br />
				
							<div style="text-align:center; margin-top:20px;">
								<form method="post" action="install.php?step='.($_GET['step']+1).'">
									<input type="submit" name="next" style="" value="Zacznij instalację!" />
								</form>
							</div>
						  </div>';
					break;
					case 2:
						echo '<form method="post" action="install.php?step='.($_GET['step']+1).'">
						<div id="options">
						<table style="margin:0;">
							<tr>
								<td>Serwer bazy danych:</td>
								<td><input type="text" name="hostaddress" value="'.$_SESSION['install']['hostaddress'].'" /></td>
								<td>adres bazy danych...</td>
							</tr>
							<tr>
								<td>Nazwa bazy danych:</td>
								<td><input type="text" name="db_name" value="'.$_SESSION['install']['db_name'].'" /></td>
								<td>... nazwa tabeli, w której ma zostać zainstalowana tablica...</td>
							</tr>
							<tr>
								<td>Użytkownik bazy danych:</td>
								<td><input type="text" name="db_user" value="'.$_SESSION['install']['db_user'].'" /></td>
								<td>... użytkownik...</td>
							</tr>
							<tr>
								<td>Hasło użytkownika:</td>
								<td><input type="password" name="db_pass" /></td>
								<td>... i hasło.</td>
							</tr>
						</table>
						</div>
						<div style="text-align:center; margin-top:20px;">
							<form method="post" action="install.php?step='.($_GET['step']+1).'">
								<input type="submit" name="db" style="" value="Przejdź do kolejnego etapu" />
							</form>
						</div>
						</form>';
					break;
					case 3:
						echo '<form method="post" action="install.php?step='.($_GET['step']+1).'">
						<div id="options">
						<table style="margin:0;">
							<tr>
								<td>Nazwa administratora</td>
								<td><input type="text" name="admin_name" value="'.$_SESSION['install']['admin_name'].'" /></td>
							</tr>
							<tr>
								<td>Hasło użytkownika:</td>
								<td><input type="password" name="admin_pass" /></td>
							</tr>
							<tr>
								<td>Adres e-mail</td>
								<td><input type="text" name="admin_email" value="'.$_SESSION['install']['admin_email'].'" /></td>
							</tr>
							<tr>
								<td>Imię administratora (opcjonalne):</td>
								<td><input type="text" name="admin_imie" value="'.$_SESSION['install']['admin_imie'].'" /></td>
							</tr>
							<tr>
								<td>Nazwisko administratora (opcjonalne):</td>
								<td><input type="text" name="admin_nazwisko" value="'.$_SESSION['install']['admin_nazwisko'].'" /></td>
							</tr>
						</table>
						</div>
						<div style="text-align:center; margin-top:20px;">
							<form method="post" action="install.php?step='.($_GET['step']+1).'">
								<input type="submit" name="admin" style="" value="Przejdź do kolejnego etapu" />
							</form>
						</div>
						</form>';
					break;
					case 4:
						$error = 0;
						if(isset($_POST['pass_check'])) {
							$_SESSION['install']['admin_pass_check'] = $_POST['admin_pass_check'];
						}
						echo '<h2>Podsumowanie</h2>
						<div id="options">
							Sprawdź poprawność danych zanim zaczniesz instalować skrypt. System w między czasie sprawdzi poprawność wpisanych danych. W przypadku źle wpisanych danych instalacja nie będzie możliwa.
						</div>
						<div id="cont">
							<h2>Połączenie z bazą danych</h2>
							<table id="pods">
								<tr>
									<td>Serwer bazy danych:</td>
									<td><b>'.$_SESSION['install']['hostaddress'].'</b></td>
								</tr>
								<tr>
									<td>Nazwa użytkownika:</td>
									<td><b>'.$_SESSION['install']['db_user'].'</b></td>
								</tr>
								<tr>
									<td>Hasło użytkownika:</td>
									<td><b>'.$_SESSION['install']['db_pass'].'</b></td>
								</tr>
								<tr>
									<td>Wybrana baza danych:</td>
									<td><b>'.$_SESSION['install']['db_name'].'</b></td>
								</tr>
							</table>
							';
								if(!@mysql_connect($_SESSION['install']['hostaddress'],$_SESSION['install']['db_user'],$_SESSION['install']['db_pass']) && !@mysql_select_db($_SESSION['install']['db_name'])) {
									$error++;
									echo '<div class="error">Nie można połączyć z bazą danych</div>';
								}
								else echo '<div class="ok">Połączono!</div>';
							echo'
							<h2>Dane administratora</h2>
							<table id="pods">
								<tr>
									<td>Nazwa użytkownika:</td>
									<td><b>'.$_SESSION['install']['admin_name'].'</b></td>
								</tr>
								<tr>
									<td>Hasło:</td>
									<td><b>*****</b></td>';
									if($_SESSION['install']['admin_pass'] === $_SESSION['install']['admin_pass_check']) {
										echo '<td style="color:green;">Potwierdzone!</td>';
									}
									else {
										$error++;
										echo '
										<td>Potwierdź hasło:</td>
										<td><form method="post" action="install.php?step=4"><input type="password" name="admin_pass_check" style="width:100px;" /><input type="submit" name="pass_check" value="Potwierdź" /></form></td>';
									}
								echo '
								</tr>
								<tr>
									<td>Email:</td>
									<td><b>'.$_SESSION['install']['admin_email'].'</b></td>
									<td>';
									if(!preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,4}$/',$_SESSION['install']['admin_email'])) {
										echo '<span style="color:red;">Adres email jest niepoprawny</span>';
										$error++;
									}
									echo '
									</td>
								</tr>
								<tr>
									<td>Imię i nazwisko:</td>
									<td><b>'.$_SESSION['install']['admin_imie'].' '.$_SESSION['install']['admin_nazwisko'].'</b></td>
								</tr>
							</table>
							<div style="margin-top:20px;">
							<form method="post" action="install.php?step=5">';
							if($error != 0) echo '<input type="submit" name="install" value="Nie można zainstalować" DISABLED/>';
							else echo '<input type="submit" name="install" value="Zainstaluj!" />';
							echo'
						</div>
						';
					break;
					case 5:
						if(isset($_POST['install'])) {
							echo '<h2>Instalowanie</h2>';
							mysql_connect($_SESSION['install']['hostaddress'],$_SESSION['install']['db_user'],$_SESSION['install']['db_pass']);
							mysql_select_db($_SESSION['install']['db_name']);
							mysql_query("SET NAMES utf8");
							echo '
							<table>
								<tr></tr>
								<tr>
									<td>Tabela <i>tablicacms_users</i></td>
									<td>';
									if(mysql_query("CREATE TABLE IF NOT EXISTS `tablicacms_users` (`id` int(11) NOT NULL AUTO_INCREMENT,`user` varchar(16) CHARACTER SET utf8 NOT NULL,`pass` varchar(32) CHARACTER SET utf8 NOT NULL,`email` varchar(128) CHARACTER SET utf8 NOT NULL,`name` varchar(32) CHARACTER SET utf8 NOT NULL,`surname` varchar(48) CHARACTER SET utf8 NOT NULL,`date` datetime NOT NULL,`last_seen` datetime NOT NULL,`ip` varchar(15) CHARACTER SET utf8 NOT NULL,`last_ip` varchar(15) CHARACTER SET utf8 NOT NULL,`rank` int(1) NOT NULL DEFAULT '2',`active` varchar(12) CHARACTER SET utf8 NOT NULL DEFAULT '1',`avatar` text CHARACTER SET utf8 NOT NULL,`remind` varchar(5) CHARACTER SET utf8 NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1") && mysql_query("INSERT INTO `tablicacms_users` (`id`, `user`, `pass`, `email`, `name`, `surname`, `date`, `rank`) VALUES (1, '".$_SESSION['install']['admin_name']."', '".md5($_SESSION['install']['admin_pass'])."', '".$_SESSION['install']['admin_email']."', '".$_SESSION['install']['admin_imie']."', '".$_SESSION['install']['admin_nazwisko']."', '".date("Y-m-d H:i:s")."', 0)")) echo 'OK';
									else exit('Błąd');
									echo '</td>
								</tr>
								<tr>
									<td>Tabela <i>tablicacms_plugins</i></td>
									<td>';
									if(mysql_query("CREATE TABLE IF NOT EXISTS `tablicacms_apps` (`id` int(11) NOT NULL AUTO_INCREMENT,`dir` varchar(32) COLLATE utf8_polish_ci NOT NULL,PRIMARY KEY (`id`),KEY `id` (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1")) echo 'OK';
									else exit('Błąd');
									echo '</td>
								</tr>
							</table>
							<h1>Instalowanie aplikacji...</h1>';
	
								foreach($apps as $app) {
									app_install($app);
								}
	
							echo '
							<table>
								<tr>
									<td>Tworzenie <i>config.php</i></td>
									<td>';
									$config_content = '
									<?php
										//Plik konfiguarcyjny wygenerowany przez instalator ('.date("Y-m-d H:i:s").')
										$host = "'.$_SESSION['install']['hostaddress'].'";
										$user = "'.$_SESSION['install']['db_user'].'";
										$pass = "'.$_SESSION['install']['db_pass'].'";
										$db = "'.$_SESSION['install']['db_name'].'";
									?>';
									if(file_put_contents('config.php',trim($config_content, ' \t'))) echo 'OK';									
									else exit('Błąd');
									echo '</td>
								</tr>
							</table>
							<h2>ZAINSTALOWANO! Nie zapomnij usunąć plik instalacyjny! Teraz możesz przejść do <a href="index.php">logowania</a></h2>
							';
						}
					break;
				}
			?>
			</div>
		</div>
	</body>
</html>
<?php ob_end_flush(); ?>
