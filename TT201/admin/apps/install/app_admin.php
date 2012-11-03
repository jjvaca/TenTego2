<?php
	class mainContent {
		
		var $plugins;
		
		function verifyPlugin($name) {
			$about_file = file_exists("apps/$name/about.xml");
			$content_file = file_exists("apps/$name/app_admin.php");
			if($about_file && $content_file) return 1;
			else return 0;
		}
		function pluginList() {
			$dir = opendir("apps");
			while($plugin = readdir($dir)) {
				if($plugin == "." OR $plugin == ".." OR !is_dir("apps/".$plugin)) continue;
				if($this->verifyPlugin($plugin)) $this->plugins[] = $plugin;
			}
			closedir($dir);
		}
			
				
		
		/* Metody konstrukcyjne */
		function myCSS() {
		return '
		.app {
		background:#fff;
		margin:5px 10px;
		box-shadow:-1px 1px 5px #ccc;
		border:1px solid #ccc;
		border-radius:5px;
		width:35%;
		color:#000;
		padding:15px;
		display:inline-block;
		text-align:left;
		font-size:12px;
		padding-right:100px;
		position:relative;
		}
		.app .install {
			background:green;
			padding:5px 15px;
			color:#FFF !important;
			display:block;
			font-weight:bold;
			font-size:14px;
			position:absolute;
			right:10px;
			border:4px solid #dcfed8;
			border-radius:3px;
			top:50%;
			margin-top:-10px;
		}
		.app .name {
		font-size:22px;
		font-variant:small-caps;
		color:#333;
		height:32px;
		padding-top:5px;
		padding-left:36px;
		}

		.naglowek {
			font-size:32px;
			color:#1E90FF;
			font-weight:bold;
			padding:10px;
			background:#F5FDFF;
			border-top:1px solid #D4F6FF;
			border-bottom:1px solid #D4F6FF;
		}
		.naglowek-mini {
			font-size:22px;
			padding:30px;
		}
		.naglowek-mini img {
			margin-bottom:-8px;
			margin-right:5px;
		}
		.informacje {
			margin:0;
			padding:0 30px;
			width:400px;
			font-size:12px;
			line-height:30px;
		}
		.install {
			text-align:center;
			padding:20px;
		}
		.install input {
			font-size:33px;
			color:#FFF;
			width:400px;
			background:#0BC70B;
			padding:10px 20px;
			border:5px solid #EFFFEF;
		}
		pre {
			font-family:\'Courier New\';
			padding:10px;
		}
		#console {
			padding:10px;
			border-top:1px solid #DE9000;
			background:#FFF683;
			color:#000;
			font-family:monospace;
			font-size:12px;
		}
		';
		}
		function subpages() {
			return array(
				'Pakiety' => 1
			);
		}
		function init($get) {
			
			$this->pluginList();
			if(isset($_GET['install'])) {
				$this->instaluj($_GET['install']);
			}
			else {
				switch($get) {
					case 1: $this->main(); break;
					default: $this->main();
				}
			}
		}
		function main() {
			echo '
			<div id="apigui" style="margin:auto;">';
			foreach($this->plugins as $app) {
				$xml = @simplexml_load_file('apps/'.$app.'/about.xml');
				if(mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_apps` WHERE `dir`='".$app."'"))) $inst_val = "Odinstaluj";
				else $inst_val = "Instaluj";
				echo '
					<div class="app">
<div class="name" style="background:url(apps/'.$app.'/'.$xml->icon.') no-repeat top left;">'.$xml->name.' <span style="color:#666; font-size:9px;">'.$xml->app_version.'</span></div>
						<div class="desc">'.$xml->desc.'</div>
						<div style="margin:5px 0 20px 0; color:#777; font-size:10px;">Wymaga Tablicy w wesji: '.$xml->version.'</div>';
				if(!in_array($app, kernel::blockList())) echo '<a href="?go=install&install='.$app.'">'.$inst_val.'</a>';
				else echo '<a href="#" style="color:#ccc;">Odinstaluj</a>';
				echo '
					</div>
				';
			}			
			echo '
			</div>
			';
		}
		

		function instaluj($dir) {
			$app = simplexml_load_file('apps/'.$dir.'/about.xml');
			if(is_dir("apps/$dir")) {
				if(isset($_POST['do'])) {
					echo '<div id="console">Konsola: <br />';
					switch($_POST['what']) {
						case 1:
							$status = 0;
							if(!empty($app->sql_tables)) {
								$sqls = explode(",",$app->sql_tables);
								foreach($sqls as $sql_table) {
									$sql = file_get_contents("apps/".$dir."/".$sql_table.".sql");
									$zapytanie = explode("!@#",$sql);
									foreach($zapytanie as $zapytania) {
										echo '<pre>'.$zapytania.'</pre>';
										if(!mysql_query($zapytania)) { $status++; echo "BŁĄD<br /><br /><br />"; }
										else echo "OK<br /><br /><br />";
									}
									if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$sql_table."'")) == 1) {
										if(mysql_query("INSERT INTO `tablicacms_apps` (`dir`) VALUES ('".$dir."')")) {
											echo '<pre>Dodano wpis do systemu</pre>';
										}
									}
								}
							}
							else {
								if(mysql_query("INSERT INTO `tablicacms_apps` (`dir`) VALUES ('".$dir."')")) {
									echo '<pre>Dodano wpis do systemu</pre>';
								}
							}
						break;
						case 2:
							if(empty($app->sql_tables)) {
								if(mysql_query("DELETE FROM `tablicacms_apps` WHERE `dir`='".$dir."'")) {
									echo '<pre>Wpis w systemie - Usunięto</pre>';
								}
							}
							else {
								$sqls = explode(",",$app->sql_tables);
								foreach($sqls as $sql_table) {
									if(mysql_query("DROP TABLE `".$sql_table."`")) {
										echo '<pre>'.$sql_table.' - Usunięto</pre>';
										if(mysql_query("DELETE FROM `tablicacms_apps` WHERE `dir`='".$dir."'")) {
											echo '<pre>Wpis w systemie - Usunięto</pre>';
										}
									}
							}
							}
					}
					echo '</div>';
				}
				$sqls = explode(",",$app->sql_tables);
				$tbl = NULL;
				$z = 0;
				if(empty($app->sql_tables)) $tbl = "Nie wymaga bazy danych";
				else {
					foreach($sqls as $sql_table) {
						if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$sql_table."'"))) {
							$tbl .= ' <span style="color:red;">'.$sql_table.'</span>';
						}
						else { $tbl .= ' '.$sql_table; $z++; }
					}
				}
				if($z == count($sqls)) {
					$install = 1;
					$install_val = "Zainstaluj";
				}
				else if($z == 0) {
					if(mysql_num_rows(mysql_query("SELECT * FROM `tablicacms_apps` WHERE `dir`='".$dir."'"))) {
						$install = 2;
						$install_val = "Odinstaluj";
					}
					else {
						if(empty($app->sql_tables)) {
							$install = 1;
							$install_val = "Zainstaluj";
						}
						else {
							$install = 0;
							$install_val = "Nie można zainstalować";
						}
					}
				}
					
				echo '<div class="naglowek">Instalator</div>';
				echo '<div class="naglowek-mini"><img src="apps/'.$dir.'/'.$app->icon.'" alt="'.$app->name.'" />'.$app->name.'</div>';
				echo '<div class="informacje">
						<b>Autor:</b> '.$app->author.'<br />
						<b>Opis:</b> '.$app->desc.'<br />
						<b>Wersja skryptu:</b> '.$app->version.'<br />
						<b>Nazwa tabeli bazy danych:</b> '.$tbl.'
					</div>';		   
				echo '
					<div class="install">
					<form method="post" action="?go='.$_GET['go'].'&install='.$_GET['install'].'">
					<input type="submit" name="do" value="'.$install_val.'" />
					<input type="hidden" name="what" value="'.$install.'" />
					</form>
					</div>';
			}
			else header("LOCATION: index.php?go=".$_GET['go']);
			}
		}
?>
