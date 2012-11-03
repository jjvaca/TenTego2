<?php

class settings {
	function load($what) {
		if($what != 'settings') {
			$query = mysql_query("SELECT `$what` FROM `tentego_settings` WHERE `id`=1");
			$result = mysql_fetch_array($query);			
			return $result[$what];
		} else {
			$query = mysql_query("SELECT * FROM `tentego_settings` WHERE `id`=1");
			$result = mysql_fetch_array($query);
			return $result;
		}
	}
	
	function host() {
		 $link = pathinfo($_SERVER['SCRIPT_NAME']);
          if($link['dirname'] == '/') $link['dirname'] = NULL;
          if($link['dirname'] == '\\') $link['dirname'] = NULL;
		return 'http://'.$_SERVER['SERVER_NAME'].$link['dirname'];
	}
	
	function contactForm() {
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`='1'"));
		if(isset($_POST['submit'])) {
			if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['content'])) {
				if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) { $this->msg('Adres email jest niepoprawny!',1); }
				else {
					$message = "$_POST[content]\n\nWysłał(a): $_POST[name] $_POST[surname]\n\nE-Mail: $_POST[email]"; 
					$header = "From: $_POST[name] $_POST[surname] <$_POST[email]>"; 
					$send = @mail($user['email'],"[".$this->load('title')."] $_POST[subject]",$message,$header); 
					if(!$send) $this->msg('Nie udało się wysłać wiadomości.', 1);
					else $this->msg('Wiadomość została poprawnie wysłana!', 3);
				}
			}
			else $this->msg('Wypełnij wszystkie wymagane pola formularza!', 1);
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
	
	function getUrl() {
	  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
	  $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
	  $url .= $_SERVER["REQUEST_URI"];
	  return $url;
	}
	
}
?>