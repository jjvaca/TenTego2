<?php
	ob_start();
	require_once("sys/init.php"); //!important
	require_once("sys/kernel.php"); //!important
	$kernel = new kernel();
	$kernel->session_start();
	$kernel->mysql();
	$kernel->init();

		$get = @$_GET['go']; if(empty($get)) header("LOCATION: index.php?go=home");;

	$kernel->load_app($get);
	$kernel->loadLib("colgen");
	$kernel->loadLib("cache");
	$kernel->loadLib("apigui"); $api = new ApiGui();
	if($kernel->app_content(@$_GET['go'], $get)) if(class_exists("mainContent")) $content = new mainContent();
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $kernel->app->name; ?> - TablicaCMS</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link rel="Shortcut icon" href="img/favicon.png" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				var wheight = $(window).height();
				$("#content").css({"min-height":wheight-65});
			});
		</script>
		<script type="text/javascript" src="http://channel-tentego.wojciechkrol.eu/stats.js"></script>
		<?php
		$api->loadGUI();
		
		if(isset($content)) {
			if(method_exists($content, 'myCSS')) {
				echo "\n".'<style type="text/css">';
				echo $content->myCSS();
				echo '</style>';
			}
		}
		?>
		
	</head>
	<body>
		<div id="menu_header">
			<ul id="menu">
				<?php
				echo '<li style="font-variant:small-caps; padding:0 20px; margin:0; border-top:1px solid yellow;">Tablica<span style="color:#fced30;">CMS</span></li>';
				$kernel->get_apps_list('<li><a href="?go=#URL#" style="border-top-color:rgb(#COLOR#);">#NAME#</a></li>');
				echo '<li><a href="?feature=logout" style="border-top-color:rgb('.colgen::generate("Wyloguj się").');">Wyloguj się</a></li>';
				?>
			</ul>
		</div>
		<div id="content">
			<div id="header">
				<?php
				$icon = APP_PATH.$kernel->app->icon;
				?>
				<h1 style="background:url(<?php echo $icon; ?>) no-repeat top left;"><?php echo $kernel->app->name; ?></h1>
			</div>
			<?php
			if(isset($content)) {
				if(method_exists($content, 'subpages')) {
					echo '<div id="submenu">
					<ul>';
					$i=0;
					foreach($content->subpages() as $name => $url) {
						if(isset($_GET['feature'])) {
							if($_GET['feature'] == $url) $class = ' class="active" ';
							else $class = NULL;
						}
						else {
							if($i==0) $class = ' class="active" ';
							else $class = NULL;
						}
						
						echo '<li><a href="'.$kernel->host().'?go='.$get.'&feature='.$url.'"'.@$class.'>'.$name.'</a></li>';
						$i++;
					}
					echo '
					</ul>
					</div>';
				}
			}
			
			
			if(isset($content)) {
				if(class_exists("mainContent")) 
					if(method_exists($content, "init"))
						$content->init(@$_GET['feature']);
					else $kernel->make_notify("Metoda <i>init()</i> nie istnieje. Wtyczka nie zostanie wczytana", NULL, 1);
				else $kernel->make_notify("Klasa <i>mainContent</i> nie istnieje. Wtyczka nie zostanie wczytana", NULL, 1);
			}
			else {
				$kernel->make_notify("Aplikacja którą próbujesz uruchomić nie istnieje lub nie została zainstalowana.");
			}
			?>
			
		</div>
		<div id="notify"></div>
		<div id="footer">Powered by <a href="http://wojciechkrol.eu">Wojciech Król</a>. Wszelkie prawa zastrzeżone! <span style="color:#aaa;">TablicaCMS</span> w wersji: <span style="color:orange;"><?php echo $kernel->version(); ?></span></div>
	</body>
</html>
<?php
	if($kernel->get_notify(NULL, 1) > 0) {
		echo '<script type="text/javascript">
			$(document).ready(function(){
				$("#notify").show().html(\'';
			
				echo $kernel->get_notify(NULL, 0,"<div class=\"notify_div\">#CONTENT#</div>");
				
		echo '\').animate({"top":"20px"}, 500);
			setTimeout(function(){ $("#notify").fadeOut() }, 30000);
			$("#notify").click(function() { $(this).fadeOut(); });
			});
			</script>';
	}
	$kernel->destroy_notify();
	ob_end_flush();
?>
