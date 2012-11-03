<?php
/*
 *	KERNEL
 *	Kopiowanie, udostępnianie lub rozpowszechnianie, przez osoby niekompetentne, pliku w wersji zmodyfikowanej bądź nie jest ZABRONIONE
 * 	Plik jest integralną częścią systemu zarządzania treścią TablicaCMS.
 * 
 * 	Autor projektu: Wojciech Król <me@wojciechkrol.eu>
 * 
 * 	WSZELKIE PRAWA ZASTRZEŻONE 
 * 
*/
	class kernel {
		
		var $systemDIR = 'sys';
		var $appsDIR = 'apps';
		var $notify;
		var $plugin_list;
		var $app = NULL;
		
		function __construct() {
			if(!file_exists("config.php")) header("LOCATION: install.php");
			
			if(!file_exists($this->systemDIR) || !is_dir($this->systemDIR))  exit("Blad systemu. Katalog SYSTEM nie istnieje.");
			if(!file_exists($this->appsDIR) || !is_dir($this->appsDIR))  exit("Blad systemu. Katalog PLUGIN nie istnieje.");
		}
		public static function version() {
			return "0.9.1";
		}
		public function session_start() {
			session_start();
			if(!isset($_SESSION['notification'])) $_SESSION['notification'] = array();
		}
		public function destroy_notify() {
			$_SESSION['notification'] = array();
		}
		public function init() {
			if(isset($_POST['submit_login'])) {
				
				$_POST['post_user'] = mysql_real_escape_string($_POST['post_user']);
				$_POST['post_pass'] = mysql_real_escape_string($_POST['post_pass']);
				
				$query = mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_users` WHERE `user`='".$_POST['post_user']."' AND `pass`='".md5($_POST['post_pass'])."' AND `rank`='0'"));
				if($query == 1) {
					$_SESSION[$this->host().'-us_user'] = $_POST['post_user'];
					$_SESSION[$this->host().'-us_pass'] = md5($_POST['post_pass']);
				}
				else {
					$this->make_notify("Niepoprawne dane logowania");
					exit(header("LOCATION: login.php"));
				}
			}
			if(isset($_SESSION[$this->host().'-us_user']) && isset($_SESSION[$this->host().'-us_pass'])) {
				$query = mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_users` WHERE `user`='".$_SESSION[$this->host().'-us_user']."' AND `pass`='".$_SESSION[$this->host().'-us_pass']."' AND `rank`='0'"));
				if($query == 0) header("LOCATION: login.php");
				else {
					if(@$_GET['feature'] == 'logout') {
						$_SESSION[$this->host().'-us_user'] = array();
						$_SESSION[$this->host().'-us_pass'] = array();
						$this->make_notify("Wylogowano");
						exit(header("LOCATION: login.php"));
					}
				}
			}
			else exit(header("LOCATION: login.php"));
		}
		public function host() {
			return 'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		}
		public function mysql($host = NULL, $user = NULL, $pass = NULL, $db = NULL) {
			if(empty($host) && empty($user) && empty($pass) && empty($db)) require_once('config.php');
				if(!@mysql_connect($host, $user, $pass)) $this->make_notify("Brak połączenia z serwerem bazy danych.", __FUNCTION__, 2);
				else if(!@mysql_select_db($db)) $this->make_notify("Wybrana baza danych nie istnieje.", __FUNCTION__, 2);
				mysql_query("SET NAMES utf8");
		}
		public function make_notify($cont, $func = NULL, $type = 0) {
			if($func != NULL) $from = " - komunikat z <u>".$func."</u>";
			else $from = NULL;
			$_SESSION['notification'][] = $cont.$from;
		}
		public function get_notify($int = NULL, $count = 0, $pattern = "<div>#CONTENT#</div>") {
			if($count == 1) return count($_SESSION['notification']);
			else if(empty($int)) {
				for($i=0;$i<count($_SESSION['notification']);$i++) {
					$return = str_replace("#CONTENT#", $_SESSION['notification'][$i], $pattern);
					echo $return;
				}
			}
			else if($int > count($_SESSION['notification'])) {
				$return = str_replace("#CONTENT#", "Array exceeded", $pattern);
				echo $return;
			}
			else {
				$int = $int-1;
				$show = $_SESSION['notification'][$int];
				$return = str_replace("#CONTENT#", $_SESSION['notification'][$int], $pattern);
				echo $return;
			}
		}
		public function load_app($app) {
			$about = $this->appsDIR.'/'.$app.'/about.xml';
			if(file_exists($about)) {
				$xml = @simplexml_load_file($about);
				$this->app = $xml;
			}
			else {
				$this->make_notify("Aplikacja nie posiada struktur informacyjnych.");
			}
		}
		public function get_apps_list($pattern) {
			$query = mysql_query("SELECT * FROM `tablicacms_apps` ORDER BY `id` ASC");
			while($app = mysql_fetch_array($query)) {
				$xml = simplexml_load_file($this->appsDIR.'/'.$app['dir'].'/about.xml');
				$inf = $pattern;
				$inf = str_replace("#URL#",$app['dir'],$inf);
				$inf = str_replace("#ICON#",$this->appsDIR.'/'.$app['dir'].'/'.$xml->icon,$inf);
				$inf = str_replace("#NAME#",$xml->name,$inf);
				$inf = str_replace("#COLOR#",colgen::generate($xml->name),$inf);
				echo $inf;
			}
		}
		public function app_content($get, $default = 'home') {
			if(empty($get)) $get = $default;
			if(mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_apps` WHERE `dir` = '".$get."'"))) {
				if(!include($this->appsDIR.'/'.$get.'/app_admin.php')) {
					//$this->make_notify("Nie można wczytać aplikacji <i>$get</i>",__FUNCTION__,1);
					return 0;
				}
				else {
					if($this->app->version <= $this->version()) return 1;
					else return 0;
				}
			}
			else return 0;
		}
		public static function loadLib($libname) {
			require_once("lib/".$libname.".lib.php");
		}
		public static function blockList() {
			return array('install','home','manager','ads','users','settings');
		}
	}
?>