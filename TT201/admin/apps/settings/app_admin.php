<?php
	class mainContent {
		
		//Zmienne pomocnicze
		var $table = 'tentego_settings';

		
		// Metoda CSS
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
			display:block;
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
				'Ogólne' => 1,
				'Użytkownicy' => 2,
				'Wygląd' => 3,
				'Pozostałe' => 4
			);
		}
		
		//Wywolanie podstron
		function init($get) {
			switch($get) {
				case 1: $this->general(); break;
				case 2: $this->users(); break;
				case 3: $this->appearance(); break;
				case 4: $this->other(); break;
				default: $this->general();
			}
		}

		function general() {	
			if(isset($_POST['save'])) { 
				$query = mysql_query("UPDATE `$this->table` SET `title` = '".$_POST['title']."', `slogan` = '".$_POST['slogan']."',
				`description` = '".$_POST['description']."', `tags` = '".$_POST['tags']."', `objects_per_page` = '".$_POST['objects_per_page']."',
				`max_file_size` = '".$_POST['max_file_size']."' WHERE id='1'") or die(mysql_error());		
				if($query) {
					kernel::make_notify("Ustawienia zostały zaktualizowane");
				}
				else {
					kernel::make_notify("Nie udało się zapisać ustawień");
				}
			}
			$this->query(mysql_query("SELECT * FROM `$this->table` WHERE `id`='1'"));
			
			echo '<div id="apigui">
			<form method="post" action="?go=settings&feature=1">
			<div id="options"><input type="submit" value="Zapisz" name="save"></div>
			<div class="block">
			<h2>Nazwa strony</h2>
			<span class="description">Nazwa witryny. Nie powinna być dłuższa niż 66 znaków.</span>
			<input type="text" name="title" value="'.$this->pobierz('title').'" />
			</div>
			<div class="block">
			<h2>Slogan</h2>
			<span class="description">Krótki slogan witryny. Może być wyświetlany w nagłówku przez niektóre szablony.</span>
			<input type="text" name="slogan" value="'.$this->pobierz('slogan').'" />
			</div>
			<div class="block">
			<h2>Opis</h2>
			<span class="description">Opis strony. Będzie on umieszczony w meta-tagu <em>description</em>.</span>
			<input type="text" name="description" value="'.$this->pobierz('description').'" />
			</div>
			<div class="block">
			<h2>Słowa kluczowe</h2>
			<span class="description">Wymień po przecinku. Będą one umieszczone w meta-tagu <em>keywords</em>.</span>
			<input type="text" name="tags" value="'.$this->pobierz('tags').'" />
			</div>
			<div class="block">
			<h2>Ilość obiektów na stronę</h2>
			<span class="description">Określa liczbę wyświetlanych obiektów na jednej stronie.</span>
			<input type="text" name="objects_per_page" value="'.$this->pobierz('objects_per_page').'" />
			</div>
			<div class="block">
			<h2>Maksymalny rozmiar obiektu</h2>
			<span class="description">Określa maksymalną wielkość wgrywanego pliku w KB.</span>
			<input type="text" name="max_file_size" value="'.$this->pobierz('max_file_size').'" />
			</div>
			</form>
			</div>';
			
		}
		
		
		function users() {
			if(isset($_POST['save'])) { 
				$query = mysql_query("UPDATE `$this->table` SET `register` = '".$_POST['register']."', `req_code` = '".$_POST['req_code']."',
				`guest_add` = '".$_POST['guest_add']."' WHERE id='1'") or die(mysql_error());
				if($query) {
					kernel::make_notify("Ustawienia zostały zaktualizowane");
				}
				else {
					kernel::make_notify("Nie udało się zapisać ustawień");
				}
			}		
			$this->query(mysql_query("SELECT * FROM `$this->table` WHERE `id`='1'"));
			
			echo '<div id="apigui">
			<form method="post" action="?go=settings&feature=2">
			<div id="options"><input type="submit" value="Zapisz" name="save"></div>
			<div class="block">
			<h2>Rejestracja</h2>
			<span class="description">Zezwolić na rejestrację nowych użytkowników?</span>
			<input type="radio" name="register" value="1" '.($this->pobierz('register') ? 'checked' : '').' />Tak
			<input type="radio" name="register" value="0" '.(!$this->pobierz('register') ? 'checked' : '').' />Nie
			</div>
			<div class="block">
			<h2>Aktywacja</h2>
			<span class="description">Włączyć aktywację nowych kont przez email?</span>
			<input type="radio" name="req_code" value="1" '.($this->pobierz('req_code') ? 'checked' : '').' />Tak
			<input type="radio" name="req_code" value="0" '.(!$this->pobierz('req_code') ? 'checked' : '').' />Nie
			</div>
			<div class="block">
			<h2>Dodawanie przez gości</h2>
			<span class="description">Pozwolić niezarejestrowanym użytkownikom na dodawanie nowych treści?</span>
			<input type="radio" name="guest_add" value="1" '.($this->pobierz('guest_add') ? 'checked' : '').' />Tak
			<input type="radio" name="guest_add" value="0" '.(!$this->pobierz('guest_add') ? 'checked' : '').' />Nie
			</div>
			</form>
			</div>';
		}
		
		function appearance() {
			if(isset($_POST['save'])) { 
				if($_POST['watermark']==1) { $_POST['watermark'] = $_POST['watermark_url']; }
				$query = mysql_query("UPDATE `$this->table` SET `logo` = '".$_POST['logo']."', `object_title` = '".$_POST['object_title']."',
				`theme` = '".$_POST['theme']."', `watermark` = '".$_POST['watermark']."', `rewrite` = '".$_POST['rewrite']."' WHERE id='1'") or die(mysql_error());
				if($query) {
					kernel::make_notify("Ustawienia zostały zaktualizowane");
				}
				else {
					kernel::make_notify("Nie udało się zapisać ustawień");
				}
			}		
			$this->query(mysql_query("SELECT * FROM `$this->table` WHERE `id`='1'"));
			
			echo '<script type="text/javascript">
				$(document).ready(function(){
				$(\'input[value$="1"][name$="watermark"]\').click( function() { $(\'span[class$="url"]\').attr(\'style\', \'display:block;\'); }); 	
				$(\'input[value$="0"][name$="watermark"]\').click( function() { $(\'span[class$="url"]\').attr(\'style\', \'display:none;\'); }); 
				if ($(\'input[value$="1"][name$="watermark"]\').is(":checked")) {
					$(\'span[class$="url"]\').attr(\'style\', \'display:block;\');
				}
				});
				</script>';
			
			echo '<div id="apigui">
			<form method="post" action="?go=settings&feature=3">
			<div id="options"><input type="submit" value="Zapisz" name="save"></div>
			<div class="block">
			<h2>Kod logo</h2>
			<span class="description">Pozwala wprowadzić kod HTML, który będzie wyświetlany w nagłówku szablonu.</span>
			<input type="text" name="logo" value="'.htmlspecialchars($this->pobierz('logo')).'" />
			</div>
			<div class="block">
			<h2>Tytuły nad obiektami</h2>
			<span class="description">Wyświetlać nadane tytuły nad obiektami?</span>
			<input type="radio" name="object_title" value="1" '.($this->pobierz('object_title') ? 'checked' : '').' />Tak
			<input type="radio" name="object_title" value="0" '.(!$this->pobierz('object_title') ? 'checked' : '').' />Nie
			</div>
			<div class="block">
			<h2>Szablon</h2>
			<span class="description">Określa szablon witryny (katalog <em>_themes</em>).</span>
			<select name="theme">';
			$this->cat_list('../_themes');
				for( $x = 0, $cnt = count($this->katalogi); $x < $cnt; $x++ ) {
					echo '<option';
					if($this->pobierz('theme')==$this->katalogi[$x]) { echo ' selected'; }
					echo '>'.$this->katalogi[$x];
					echo '</option>';
				}
			echo '</select>
			</div>
			<div class="block">
			<h2>Znak wodny</h2>
			<span class="description">Wyświetlać znak wodny na obiektach graficznych?</span>
			<input type="radio" name="watermark" value="1" '.($this->pobierz('watermark') ? 'checked' : '').' />Tak
			<input type="radio" name="watermark" value="0" '.(!$this->pobierz('watermark') ? 'checked' : '').' />Nie
			<span class="url" style="display: none;">
			<span class="description" style="padding-top: 4px;">Podaj adres do znaku wodnego lub zostaw pole puste.</span>
			<input type="text" name="watermark_url" value="'.(!$this->pobierz('watermark') ? '' : $this->pobierz('watermark')).'" />
			</span>
			</div>
			<div class="block">
			<h2>Przyjazne linki</h2>
			<span class="description">Ta opcja uruchomi przyjazne linki.<br /><span style="color:#333;">UWAGA! Wszystkie dotychczas uzbierane lajki i udostępnienia znikną.</span></span>
			<input type="radio" name="rewrite" value="1" '.($this->pobierz('rewrite') ? 'checked' : '').' />Włączone
			<input type="radio" name="rewrite" value="0" '.(!$this->pobierz('rewrite') ? 'checked' : '').' />Wyłączone
			</div>
			</form>
			</div>';
		}
		
		function other() {
			if(isset($_POST['save'])) { 
				$query = mysql_query("UPDATE `$this->table` SET `regulations` = '".$_POST['regulations']."', `comments` = '".$_POST['comments']."' 
				WHERE id='1'") or die(mysql_error());
				if($query) {
					kernel::make_notify("Ustawienia zostały zaktualizowane");
				}
				else {
					kernel::make_notify("Nie udało się zapisać ustawień");
				}
			}		
			$this->query(mysql_query("SELECT * FROM `$this->table` WHERE `id`='1'"));
			
			echo '<div id="apigui">
			<form method="post" action="?go=settings&feature=4">
			<div id="options"><input type="submit" value="Zapisz" name="save"></div>
			<div class="block">
			<h2>Komentarze</h2>
			<span class="description">Włączyć komentarze pod obiektami?</span>
			<input type="radio" name="comments" value="1" '.($this->pobierz('comments') ? 'checked' : '').' />Tak
			<input type="radio" name="comments" value="0" '.(!$this->pobierz('comments') ? 'checked' : '').' />Nie
			</div>
			<div class="block">
			<h2>Regulamin</h2>
			<span class="description">Treść regulaminu. Dozwolony kod HTML.</span>
			<textarea name="regulations">'.$this->pobierz('regulations').'</textarea>
			</div>
			</form>
			</div>';
		}
	
		
		/*****************************************/
		/* DODATKOWE FUNKCJE
		/*****************************************/
		function query($q) {
			$db_query = $q;
			$this->zapytanie = mysql_fetch_array($db_query);
		}
		function pobierz($row) {
			return $this->zapytanie[$row];
		}
		function cat_list($katalog) { 
			$dir = opendir($katalog);
			while ($file = readdir($dir)) {
				if (is_dir($katalog.'/'.$file) && $file != "." && $file != "..") {
					$this->katalogi[] = $file;
				}
			}
		}
	
	}
?>
