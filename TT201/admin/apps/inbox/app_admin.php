<?php
class mainContent {
	
	//Zmienne pomocnicze
	var $table = 'tentego_inbox_conf';
	
	function myCSS() {
		return '
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
			padding-right:6px;
		}
		#apigui .block input[type~=text] {
			width: 100% !important;
		}
		#apigui .block input[type~=radio] {
			margin: 3px 5px;
		}
		#apigui .block select {
			margin: 5px 0px;
			width: 100%;
		}
		#apigui .block textarea {
			margin: 5px 0px;
			width: 100%;
			height: 150px;
		}
		#apigui .block h2 {
			font-weight: normal !important;
		}
		';
	}
	
	//Podstrony
	function subpages() {
		return array(
			'Ustawienia' => 'settings',
			'Masowa Korespondencja' => 'mass',
		);
	}
		
	//Wywolanie podstron
	function init($get) {
		switch($get) {
			case 'settings': $this->settings(); break;
			case 'mass': $this->mass(); break;
			default: $this->settings();
		}
	}
	
	function settings() {	
		if(isset($_POST['save'])) {
					$query = mysql_query("UPDATE `$this->table` SET `active`='".$_POST['active']."', `bbcode`='".$_POST['bbcode']."'") or die(mysql_error());
					if($query) kernel::make_notify("Zmiany zostały zapisane");
					else kernel::make_notify("Nie udało się dodać użytkownika");
			}
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `id`='1'");
			$conf = mysql_fetch_array($query);
			echo '<div id="apigui">
				<form method="post" action="?go=inbox&feature=settings">
					<div id="options"><input type="submit" value="Zapisz" name="save"></div>
					<div class="block">
					<h2>Włączyć</h2>
						<span class="description">Uaktywnić funkcję prywatnych wiadomości?</span>
						<input type="radio" name="active" value="1" '.($conf['active'] ? 'checked' : '').' />Tak
						<input type="radio" name="active" value="0" '.(!$conf['active'] ? 'checked' : '').' />Nie
					</div>
					<div class="block">
					<h2>BBCode</h2>
						<span class="description">Zezwolić na kod BBCode?</span>
						<input type="radio" name="bbcode" value="1" '.($conf['bbcode'] ? 'checked' : '').' />Tak
						<input type="radio" name="bbcode" value="0" '.(!$conf['bbcode'] ? 'checked' : '').' />Nie
					</div>
				</form>
			</div>';
		}
	
	function mass() {	
		if(isset($_POST['send'])) {
				$query = mysql_query("SELECT * FROM `tablicacms_users`");
				while($user = mysql_fetch_array($query)) {
					$date = date('Y-m-d H:i:s');
					$query_pm = mysql_query("INSERT INTO `tentego_inbox` (`subject`,`content`,`from`,`to`,`date`,`read`) 
					VALUES ('".$_POST['subject']."', '".$_POST['content']."', '1', '".$user['id']."', '$date', '0')") or die(mysql_error());
					if($query_pm) $error .= NULL;
					else $error .= 1;
				}
			if($error == NULL) kernel::make_notify("Wiadomości zostały poprawnie wysłane");
			else kernel::make_notify("Nie udało się wysłać wiadomości");
		}
			echo '<div id="apigui">
				<form method="post" action="?go=inbox&feature=mass">
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
}
?>