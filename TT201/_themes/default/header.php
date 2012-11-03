<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
	<title><?php echo (isset($title)?strip_tags($title).' - ':'').$page->load('title').' - '.$page->load('slogan'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta content='<?php echo $page->load('description'); ?>' name='description'/>
	<meta content='<?php echo $page->load('tags'); ?>,tentego,wojciechkrol.eu, klocus.pl' name='keywords'/>
	<link rel="stylesheet" type="text/css" href="<?php echo $page->host().'/'; ?>_themes/<?php echo $theme; ?>/style.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_js/messages.js"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_themes/<?php echo $theme; ?>/scripts.js"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_js/tools.php?vote"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_js/tools.php?favorites"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_js/tools.php?mod"></script>
	<script type="text/javascript" src="<?php echo $page->host(); ?>_js/facebook.js"></script>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
	<?php
	if(isset($img_file)) {
		echo '
			<meta property="og:title" content="'.$img->getObject("#TITLE#",$_GET['id']).' - '.$page->load('title').'" />
			<meta property="og:url" content="'.$img->getObject($rewrite->img("#ID#","#REWRITE-TITLE#"),$_GET['id']).'" />
			<meta property="og:image" content="'.$img->getObject("#SCREENSHOT#",$_GET['id']).'" />
			<meta property="og:site_name" content="'.$page->load('description').'" />
			<meta property="og:type" content="object" />';
	}
	?>
</head>
<body>
<!-- Begin Wrapper -->
<div id="wrapper">
	<?php echo $ads->load('<div style="position:fixed;left:10px;top:200px;">#AD[left]#</div>'); ?>
	<?php echo $ads->load('<div style="position:fixed;right:10px;top:200px;">#AD[right]#</div>'); ?>
	<?php $user->userTemplateInfo(NULL,$inbox->notification($user->userInfo('id'))); ?>
  <!-- Begin Header -->
  <div id="header">
	<?php echo $page->load('logo'); ?>
	<!-- Begin Top Navigation -->
	<div id="nav_top">
		<ul>
			<?php $user->userTemplateInfo('
			<li>Witaj, Nieznajomy!</li>
			<li><a href="'.$rewrite->login.'">Logowanie</a></li>
			<li><a href="'.$rewrite->register.'">Rejestracja</a></li>',
									 
			'<li>Witaj, #LOGIN#!</li>
			<li><a href="'.$rewrite->profile.'">Profil</a></li>
			<li><a href="'.$rewrite->inbox.'">Wiadomości ('.$inbox->get_new_pms($user->userInfo('id')).')</a></li>
			<li><a href="'.$rewrite->favorites.'">Ulubione</a></li>
			<li><a href="'.$kernel->host().'/?logout=true">Wyloguj się</a></li>');
			?>
		</ul>
	</div>
	<!-- End Top Navigation -->
  </div>
  <!-- End Header -->
  <!-- Begin Naviagtion -->
  <div id="navigation">
	<ul>
		<li><a href="<?php echo $rewrite->index; ?>">Strona Główna</a></li>
		<li><a href="<?php echo $rewrite->waiting; ?>">Poczekalnia <span class="sup"><?php echo $img->count(1); ?></span></a></li>
		<li><a href="#">Kategorie</a>
			<ul class="submenu">
			<?php
				echo $img->getCategories('<li><a href="'.$rewrite->categories("#ID#","#REWRITE-NAME#").'">#NAME# (#COUNT#)</a></li>');
			?>
			</ul>
		</li>
		<li><a href="<?php echo $rewrite->random; ?>">Losuj</a></li>
		<li><a href="<?php echo $rewrite->add; ?>">Dodaj</a></li>
	</ul>
	<!-- Begin Search -->
	<div id="search">
		<form action="<?php echo $rewrite->search; ?>" method="get">
		<input type="text" name="s" placeholder="Szukaj..." />
		<button><img src="<?php echo $page->host().'/'; ?>_themes/<?php echo $theme; ?>/img/search_icon.png" alt="search" /></button>
		</form>
	</div>
	<!-- End Search -->
  </div>
  <!-- End Naviagtion -->