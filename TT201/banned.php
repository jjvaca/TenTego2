<?php
	ob_start();
	//Loading core of application
		require_once("_core/kernel.php");
		$kernel = new kernel();
	//Loading settings of application
		$kernel->load_module("settings");
		$kernel->load_module("users");
	//Loading main theme
		$page = $kernel->settings;
		$theme = $page->load('theme');
		$user = $kernel->users;
		$allowed = 'page,theme,user';
		
	if($user->userInfo('rank')<3) header('Location: index.php');

	//Including content of theme
		$kernel->load_content("_themes/".$theme."/banned.php", $allowed);
	ob_end_flush();
?>