<?php
	ob_start();
	require_once("sys/kernel.php");
	$kernel = new kernel();
	$kernel->session_start();
	$kernel->mysql();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Logowanie</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<link rel="Shortcut icon" href="img/favicon.png" />
		<style type="text/css">
			body {
				margin:0 auto;
				padding-top:20px;
				background:url(http://subtlepatterns.com/patterns/outlets.png);
				font-family:Arial;
				text-align:center;
				min-width:1000px;
			}
			#box {
				position:absolute;
				display: table;
				right:30px;
				top:50%;
				height:300px;
				width:40%;
				margin-top:-150px;
				text-align:center;
			}
			#box #in-box {
				display: table-cell;
				vertical-align: middle;
			}
			#box input {
				color:#FFF;
				margin:5px 0px;
				border:1px solid #404040;
				width:290px;
				border-radius:2px;
				padding:5px;
				font-size:18px;
				background:orange;
				text-shadow:1px 1px 5px #111;
			}
			#box input:focus {
				border-color:#FFA500;
			}
			#hello {
				width:60%;
				position:absolute;
				left:0;
				top:50%;
				margin-top:-115px;
			}
			#hello h1 {
				font-size:52px;
				font-weight:bold;
				color:#FFF;
				padding:0; margin:0;
				text-shadow:1px 1px 10px #000;
				font-family:Arial;
			}
			#notify {
				margin:2px 10px;
				padding:5px;
				text-align:center;
				border-radius:5px;
				color:#FFF;
				font-size:13px;
				font-weight:bold;
			}
			#background {
				position:absolute;
				top:0; left:0;
				z-index:-100;
			}
			#footer {
				position:absolute;
				width:200px;
				text-align:center;
				left:50%;
				margin-left:-100px;
				bottom:2px;
				font-size:9px;
				color:#ccc;
			}
			#footer a {
				color:orange;
				text-decoration:none;
			}
		</style>
	</head>
	<body>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#hello, #box").hide();
				$("#hello, #box").fadeIn(1200);
				$("#login input:first").focus();
			});
		</script>
		<div id="hello">
			<h1><img src="img/welcome.png" alt="Witaj w TenTego2 - TablicaCMS!" /></h1>
		</div>
		<div id="box">
			<div id="in-box">
				<div id="login">
				<?php
					echo $kernel->get_notify(NULL, 0, '<div id="notify">#CONTENT#</div>'); 
				?>
				<form method="post" action="index.php">
					<input type="text" name="post_user" style="padding-left:35px; background:url(img/administrator.png) center left no-repeat;" />
					<input type="password" name="post_pass" style="padding-left:35px; background:url(img/key.png) center left no-repeat;" /><br />
					<input type="submit" name="submit_login" value="Zaloguj" />
				</form>
				</div>
			</div>
		</div>
		<div id="footer">Powered by <a href="http://wojciechkrol.eu">Wojciech Król</a> [2012]</div>
	</body>
</html>
<?php
	$kernel->destroy_notify();
	ob_end_flush();
?>
