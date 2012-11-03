<?php
	class mainContent {
		function myCSS() {
			return '
			#site { display:none; }
			';
		}
		function init() {
			kernel::loadLIB("rconf");
			
			$content = @file_get_contents("http://channel-tentego.wojciechkrol.eu/?".@$_GET['show']);
			if(empty($content) OR !function_exists('file_get_contents')) {
				echo '<script type="text/javascript">$.get("http://channel-tentego.wojciechkrol.eu", {url:"'.$_SERVER['HTTP_HOST'].'"}, function(x) { $("#home_div").html(x); });</script>
				<div id="home_div"></div>';
			}
			else {
				$replace = preg_replace("/\&(.*)/e","",$_SERVER['REQUEST_URI']);
				$content= str_replace("#URL#",$replace,$content);
				echo $content;
			}
			
		}
	}
?>
