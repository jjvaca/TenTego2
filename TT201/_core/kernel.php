<?php
/*
 * KERNEL PAGE dla TenTego 2
 * Notka licencyjna:
 * 
 */
	session_start();
	
	class kernel {
		
		var $loadedApps = array();
		function __construct() {
			$this->mysql();
		}
		public static function mysql($host = NULL, $user = NULL, $pass = NULL, $db = NULL) {
			if(!file_exists('admin/config.php')) header("Location: admin/install.php");
			if(empty($host) && empty($user) && empty($pass) && empty($db)) require_once('admin/config.php');

			if(!@mysql_connect($host, $user, $pass)) exit("Brak połączenia z serwerem");
			else if(!@mysql_select_db($db)) exit("Brak połączenia z bazą danych");
			mysql_query("SET NAMES utf8");
		}
		public function isAdmin() {
			if(isset($_SESSION[$this->host().'-us_user']) && isset($_SESSION[$this->host().'-us_pass'])) {
				$query = mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_users` WHERE `user`='".$_SESSION[$this->host().'-us_user']."' AND `pass`='".$_SESSION[$this->host().'-us_pass']."' AND `rank`='0'"));
				if($query == 0) return false;
				else return true;
			}
			return 0;
		}
		public static function host() {
			$link = pathinfo($_SERVER['SCRIPT_NAME']);
			if($link['dirname'] == '/') $link['dirname'] = NULL;			if($link['dirname'] == '\\') $link['dirname'] = NULL;
			return 'http://'.$_SERVER['SERVER_NAME'].$link['dirname'];
		}
		function load_content($what, $other = NULL) {
			if($other != NULL) {
				$ex = explode(",",$other);
				for($i = 0; $i<count($ex); $i++) {
					global ${$ex[$i]};
				}
			}
			global $kernel;
			require($what);
		}
		
		function load_module($dir) {
			global ${$dir};
			if(!in_array($dir,$this->loadedApps)) {
				require("admin/apps/$dir/app_page.php");
				$class = $dir;
				$this->{$dir} = new $class();
				$this->loadedApps[] = $dir;
			}
		}
	}
?>
