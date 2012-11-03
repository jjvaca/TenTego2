<?php
	class mainContent {
		var $img = 'upload';
		
		function myCSS() {
			kernel::loadLib('shadowbox');
			echo shadowbox::loadCSS();
		return '
		table td:first-child {
			border-right:1px solid #E5E5E5;
		}
		table .media {
			overflow:hidden;
			height:140px;
			position:relative;
		}
		table .media *:not(.clicktozoom) {
			border-radius:3px 3px 0 0;
		}
		table .media .clicktozoom {
			position:absolute;
			bottom:0;
			left:0;
			padding:2px;
			width:216px;
			background:#000;
			color:#FFF;
			font-size:10px;
			text-align:right;
			display:block;
		}
		input[disabled] {
			color:#CCC;
		}
		input.search {
			width:130px !important;
			border:1px solid #CCC;
			padding:3px 7px !important;
			font-size:15px !important;
			font-weight:bold;
			float:right;
			margin:0 !important;
		}
		input.cat {
			width:130px !important;
			border:1px solid #CCC;
			padding:3px 7px !important;
			font-size:15px !important;
			font-weight:bold;
			margin:0 !important;
		}
		';
		}
		function subpages() {
			return array(
				'Główna' => 'glowna',
				'Poczekalnia' => 'poczekalnia',
				'Archiwum' => 'archiwum',
				'Kategorie' => 'kategorie'
			);
		}
		function init($get) {
			switch($get) {
				case 'glowna': $this->obrazki(0); break;
				case 'poczekalnia': $this->obrazki(1); break;
				case 'archiwum': $this->obrazki(2); break;
				case 'kategorie': $this->kategorie(); break;
				case 'catedit': $this->catedit(); break;
				case 'edit': $this->edytor(); break;
				case 'thumb': $this->thumbnail($_GET['img']);
				default: $this->obrazki(0);
			}
		}
		//Podstrona obiekty
		function obrazki($x = 0) {
			switch($x) {
				case 0: $action = 'glowna'; break;
				case 1: $action = 'poczekalnia'; break;
				case 2: $action = 'archiwum';
			}
			
			if(isset($_POST['mvmain'])) $this->moveto(0);
			else if(isset($_POST['mvwait'])) $this->moveto(1);
			else if(isset($_POST['mvarch'])) $this->moveto(2);
			else if(isset($_POST['search'])) $search = $_POST['search'];
					
				
			if(isset($_POST['delselected'])) $this->group_del();

			if(isset($_GET['del'])) $this->delete($_GET['del']);
			kernel::loadLib('apiconfirm');
			apiconfirm::load();
			kernel::loadLib('shadowbox');
			echo shadowbox::loadJS().'
				<div id="apigui">
					'.($x==2?'<div style="background:#f9ffb7; padding:10px; margin-top:-20px; color:#000;">Obiekty w tym miejscu są oznaczone jako <i>archiwalne</i>. Nie da się ich znaleźć na stronie głównej ani w poczekalni, jednak możliwe jest obejrzenie pojedynczego obiektu znając jego adres. Zaletą tego jest dostęp do obiektu z poziomu facebooka lub innych serwisów społecznościowych, w których dany element umieszczono.</div>':'').'
					<form method="post" action="?go=manager&amp;feature='.$action.'">
					<div id="options">
					<input type="submit" class="dbl" name="mvmain" value="Przenieś na główną" />
					<input type="submit" class="dbl" name="mvwait" value="Przenieś do poczekalni" />
					<input type="submit" class="dbl" name="mvarch" value="Przenieś do archiwum" />
					<input type="submit" class="dbl" name="delselected" value="Usuń zaznaczone" />
					<input type="text" class="search" name="search" placeholder="Szukaj..." />
					</div>
					<script type="text/javascript">
							function onchecked() {
								$("input:checkbox").change(function() {
									if($(this).is(":checked")) $(this).parent().parent().css("background","#ffffdd");
									else $(this).parent().parent().css("background","none");
									
									var n = $("input:checked").length;
									if(n > 0) $("#options input.dbl").prop("disabled", false);
									else $("#options input.dbl").prop("disabled", true);
								});
							}
							$(document).ready(function() {
								onchecked();
								$("#options input.dbl").prop("disabled", true);
							});
					</script>
					<table>
						<tr>
							<th width="220px">podgląd</th>
							<th>info</th>
							<th>opcje</th>
							<th></th>
						</tr>
						'.$this->pobierzObrazki('<tr><td><div class="media">#IMG#<div class="clicktozoom">kliknij aby powiększyć</div></div></td><td><b>Tytuł:</b> #TITLE#<br /><b>Typ:</b> #TYPE#<br /><b>Data dodania:</b> #DATE#<br /><b>Dodano na główną:</b> #REL_DATE#<br /><b>Głosy:</b> #VOTE#<br /><b>Kategoria:</b> #CATEGORY#<br /><b>Dodający:</b> #OWNER#<br /><b>Źródło:</b> #SOURCE#</td><td><a href="?go=manager&amp;feature=edit&amp;id=#ID#" rel="shadowbox;width=480">edytuj</a>, <a href="?go=manager&amp;feature='.$action.'&amp;del=#ID#" title="Czy na pewno chcesz usunąć <b>#TITLE#</b>?" class="apiconfirm">usuń</a></td><td><input type="checkbox" name="img[]" value="#ID#" onClick="onchecked()" /></td></tr>',$x, $_GET['page'], 10, @$search).'
					</table>
					<div style="text-align:center; margin-top:20px;">'.$this->pagination('<a href="?go=manager&amp;feature='.$action.'&amp;page=#">poprzednie</a>', ' <a href="?go=manager&amp;feature='.$action.'&amp;page=#">[ # ]</a> ', ' [ # ] ', '<a href="?go=manager&amp;feature='.$action.'&amp;page=#">następne</a>', $_GET['page'], 10, $x).'</div>
					</form>
				</div>
			';
		}
		//Podstrona kategorie
		function kategorie() {
			echo '<div id="apigui">';
			kernel::loadLib("shadowbox");
				echo shadowbox::loadJS();
			kernel::loadLib("apiconfirm");
				apiconfirm::load();
			if(isset($_POST['save'])) $this->dodajKategorie($_POST['name']);
			
			if(isset($_GET['del'])) $this->usunKategorie($_GET['del']);
			echo '
			<div id="options">
			<form method="post" action="?go=manager&amp;feature=kategorie">
			<input type="text" class="cat" name="name" placeholder="Nazwa kategorii..." /> <input type="submit" name="save" value="dodaj" />
			</form>
			</div>';
			
			echo '<table>';
			echo '<tr><th>Nazwa kategorii</th><th>liczba elementów</th><th>opcje</th></tr>';
			echo $this->pobierzKategorie("<tr><td>#NAME#</td><td>#COUNT#</td><td><a href=\"?go=manager&amp;feature=catedit&amp;id=#ID#\" rel=\"shadowbox;width=480;height=123\">edytuj</a>, <a href=\"?go=manager&amp;feature=kategorie&amp;del=#ID#\" title=\"Czy na pewno chcesz usunąć kategorię: <b>#NAME#</b>\" class=\"apiconfirm\">usuń</a></td></tr>");
			echo '</table>';
			
			echo '</div>';
		}
		//Edytor obiektów
		function edytor() {
			ob_end_clean();
			echo '
			<!DOCTYPE html>
			<html>
			<head>
			<title>Edytowanie</title>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<link rel="stylesheet" type="text/css" href="style.css" />
			<link rel="stylesheet" type="text/css" href="css/apigui.css" />
			<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
			<style type="text/css">
			* {
			margin:0;
			}
			html,body {
				width:auto;
				min-width:0;
				overflow:hidden;
			}
			body {
				background:#60d4ff;
			}
			#header {
				padding-top:5px;
				background:#FFF;
				box-shadow:0px 0px 5px #CCC;
			}
			#content {
				color:#FFF;
				margin:0;
			}
			#image {
				position:absolute;
				z-index:-2;
				opacity:0.05;
				width:100%;
			}
			td, th {
				padding:3px;
				font-size:16px;
				text-shadow:1px 1px 0px #4190ad;
				color:#FFF;
			}
			th { font-weight:bold; font-size:16px !important; font-family:Arial; }
			td input {
				font-size:16px !important;
			}
			#save {
				position:absolute;
				bottom:0;
				left:0;
				background:#fafafa;
				padding:20px;
				width:100%;
			}
			.notify_div, #notify {
				width:360px !important;
				text-align:center;
				color:#2c2c2c;
				text-shadow:#fffc7d;
			}
			#notify {
				background:#fffec4;
				border-color:#fffc80;
				margin-left:-180px;
			}
			</style>
			</head>
			<body>
			<div id="apigui">
			';
			
			$query = mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$_GET['id']);
			if(mysql_num_rows($query)) {
				if(isset($_POST['save'])) {
					if(mysql_query("UPDATE `tentego_img` SET `title`='".htmlspecialchars($_POST['title'],ENT_QUOTES)."', `cat`='".$_POST['cat']."', `date`='".$_POST['date']."', `src`='".$_POST['src']."', `type`='".$_POST['type']."', `source`='".htmlspecialchars($_POST['source'],ENT_QUOTES)."' WHERE `id`=".$_GET['id'])) { $query = mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$_GET['id']); kernel::make_notify("Zapisano."); }
					else kernel::make_notify("Bład podczas zapisu.");
				}
				$img = mysql_fetch_array($query);
				$date = new DateTime($img['date']);
				
				function type($pattern,$array) {
					$type = array(
						'Obrazek' => 'img',
						'YouTube.com' => 'youtube',
						'Vimeo' => 'vimeo'
					);
					$return = NULL;
					foreach($type as $name => $value) {
						$text = str_replace("#NAME#", $name, $pattern);
						if($array['type']==$value) $text = str_replace("#SELECTED#",' selected="selected"', $text);
						else $text = str_replace("#SELECTED#",NULL, $text);
						$text = str_replace("#TYPE#", $value, $text);
						$return .= $text;
					}
					return $return;
				}
				function category($pattern,$value) {
					$return = NULL;
					$query = mysql_query("SELECT * FROM `tentego_img_cat`");
					
					while($cat = mysql_fetch_array($query)) {
						$text = str_replace("#NAME#", $cat['name'], $pattern);
						if($cat['id']==$value) $text = str_replace("#SELECTED#",' selected="selected"', $text);
						else $text = str_replace("#SELECTED#",NULL, $text);
						$text = str_replace("#TYPE#", $cat['id'], $text);
						$return .= $text;
					}
					return $return;
				}
				
				switch($img['type']) {
					case 'img':
						$thumb = '?go=manager&feature=thumb&img=../'.$this->img.'/'.$img['src']; break;
					case 'youtube':
						parse_str(parse_url($img['src'], PHP_URL_QUERY), $src);
						$thumb = 'http://i1.ytimg.com/vi/'.$src['v'].'/hqdefault.jpg'; break;
					case 'vimeo':
						$url = parse_url($img['src'], PHP_URL_PATH);
						$xml = simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
						$thumb = $xml->video->thumbnail_large; break;
				}
				
				echo '<form method="post" action="?go=manager&amp;feature=edit&amp;id='.$img['id'].'">';
					
					
				echo '
					<div id="content">
						<div style="width:100%; height:170px; background:url('.$thumb.') no-repeat center"></div>
						<table>
						<tr>
							<th>Tytuł:</th>
							<td><input type="text" name="title" value="'.($img['title']).'" /></td>
						</tr>
						<tr>
							<th>Data:</th>
							<td><input type="text" name="date" value="'.$date->format('Y-m-d H:i:s').'" /></td>
						</tr>
						<tr>
							<th>Adres:</th>
							<td><input type="text" name="src" value="'.$img['src'].'" /></td>
						</tr>
						<tr>
							<th>Kategoria:</th>
							<td>
								<select name="cat">
								'.category('<option value="#TYPE#"#SELECTED#>#NAME#</option>',$img['cat']).'
								</select>
							</td>
						</tr>
						<tr>
							<th>Typ:</th>
							<td>
								<select name="type">
								'.type('<option value="#TYPE#"#SELECTED#>#NAME#</option>',$img).'
								</select>
							</td>
						</tr>
						<tr>
							<th>Źródło:</th>
							<td><input type="text" name="source" value="'.$img['source'].'" /></td>
						</tr>
						</table>
						<div id="save"><input type="submit" name="save" value="Zapisz zmiany" /></div>
					</div>
				';
																					   
				echo '</form>';
			}
			
			echo '
			</div>
			<div id="notify"></div>
			</body>
			</html>
			';
			if(kernel::get_notify(NULL, 1) > 0) {
				echo '<script type="text/javascript">
					$(document).ready(function(){
						$("#notify").show().html(\'';
				
				echo kernel::get_notify(NULL, 0,"<div class=\"notify_div\">#CONTENT#</div>");
					
				echo '\').animate({"top":"-15px"}, 500);
					setTimeout(function(){ $("#notify").fadeOut() }, 30000);
					$("#notify").click(function() { $(this).fadeOut(); });
					});
					</script>';
			}
			kernel::destroy_notify();
			exit();
		}
		//Edytor kategorii
		function catedit() {
			ob_end_clean();
			echo '
			<!DOCTYPE html>
			<html>
			<head>
			<title>Edytowanie</title>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<link rel="stylesheet" type="text/css" href="style.css" />
			<link rel="stylesheet" type="text/css" href="css/apigui.css" />
			<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
			<style type="text/css">
			* {
			margin:0;
			}
			html,body {
				width:auto;
				min-width:0;
				overflow:hidden;
			}
			body {
				background:#60d4ff;
			}
			#header {
				padding-top:5px;
				background:#FFF;
				box-shadow:0px 0px 5px #CCC;
			}
			#content {
				color:#FFF;
				margin:0;
			}
			td, th {
				padding:3px;
				font-size:16px;
				text-shadow:1px 1px 0px #4190ad;
				color:#FFF;
			}
			th { font-weight:bold; font-size:16px !important; font-family:Arial; }
			td input {
				font-size:16px !important;
			}
			#save {
				position:absolute;
				bottom:0;
				left:0;
				background:#fafafa;
				padding:20px;
				width:100%;
			}
			.notify_div, #notify {
				width:360px !important;
				text-align:center;
				color:#2c2c2c;
				text-shadow:#fffc7d;
			}
			#notify {
				background:#fffec4;
				border-color:#fffc80;
				margin-left:-180px;
			}
			</style>
			</head>
			<body>
			<div id="apigui">
			';
			if(isset($_GET['id']) && is_numeric($_GET['id'])) {
				if(isset($_POST['save'])) {
					(mysql_query("UPDATE `tentego_img_cat` SET `name`='".$_POST['name']."' WHERE `id`=".$_GET['id'])?kernel::make_notify("Zapisano."):kernel::make_notify("Wystąpił błąd podczas zapisywania."));
				}
				$cat = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img_cat` WHERE `id`=".$_GET['id']));
				echo '<form method="post" action="?go=manager&amp;feature=catedit&amp;id='.$cat['id'].'">';
					
				echo '
					<div id="content">
						<table>
						<tr>
							<th>Nazwa:</th>
							<td><input type="text" name="name" value="'.htmlspecialchars($cat['name'], ENT_QUOTES).'" /></td>
						</tr>
						</table>
						<div id="save"><input type="submit" name="save" value="Zapisz zmiany" /></div>
					</div>
				';
																					   
				echo '</form>';
			}
			
			echo '
			</div>
			<div id="notify"></div>
			</body>
			</html>
			';
			if(kernel::get_notify(NULL, 1) > 0) {
				echo '<script type="text/javascript">
					$(document).ready(function(){
						$("#notify").show().html(\'';
				
				echo kernel::get_notify(NULL, 0,"<div class=\"notify_div\">#CONTENT#</div>");
					
				echo '\').animate({"top":"-15px"}, 500);
					setTimeout(function(){ $("#notify").fadeOut() }, 30000);
					$("#notify").click(function() { $(this).fadeOut(); });
					});
					</script>';
			}
			kernel::destroy_notify();
			exit();
		}
		
		//------------------------------------\
		//--------Funkcje pomocnicze----------|
		//------------------------------------/
		
		function dodajKategorie($name) {
			$name = strip_tags(htmlspecialchars($name));
			if(mysql_query("INSERT INTO `tentego_img_cat` (`name`) VALUES ('".$name."')")) return true;
			else return false;
		}
		function usunKategorie($id) {
			if(!empty($id) && is_numeric($id)) {
				if(mysql_query("DELETE FROM `tentego_img_cat` WHERE `id`=".$id)) return true;
				else return false;
			}
		}
		function pobierzKategorie($pattern) {
			$query = mysql_query("SELECT * FROM `tentego_img_cat`");
			$return = NULL;
			$text = NULL;
			
			while($cat = mysql_fetch_array($query)) {
				
				$text = str_replace("#ID#", $cat['id'], $pattern);
				$text = str_replace("#NAME#", $cat['name'], $text);
				$count = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img` WHERE `cat`=".$cat['id']));
				$text = str_replace("#COUNT#", $count, $text);
				
				$return .= $text;
				
			}
			if(empty($return)) kernel::make_notify("Brak kategorii.");
			return $return;
		}
		
		function pobierzObrazki($pattern, $where, $current_page, $count, $search = NULL) {
			
			if($search != NULL) $search_like = " AND `title` LIKE '%".$search."%'";
			else $search_like = NULL;
			if(empty($current_page)) $current_page = 1;
			$x = ($current_page-1)*$count;
			
			if($where == 0) $query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=".$where.$search_like." ORDER BY `rel_date` DESC LIMIT $x, $count");
			else $query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=".$where.$search_like." ORDER BY `id` DESC LIMIT $x, $count");
			$i = 1;
			$return = NULL;
			$result = NULL;
			while($img = mysql_fetch_array($query)) {
				$return = str_replace("#ID#",$img['id'], $pattern);
				$return = str_replace("#TITLE#",htmlspecialchars($img['title'], ENT_QUOTES), $return);
				
				switch($img['type']) {
					case 'img':
						$img['src'] = '<a href="../'.$this->img.'/'.$img['src'].'" rel="shadowbox"><img src="?go=manager&feature=thumb&img=../'.$this->img.'/'.$img['src'].'" alt="" style="max-width:220px" /></a>'; break;
					case 'youtube':
						parse_str(parse_url($img['src'], PHP_URL_QUERY), $src);
						$img['src'] = '<a href="http://www.youtube.com/embed/'.$src['v'].'" rel="shadowbox;width=720;height=405"><img src="http://i1.ytimg.com/vi/'.$src['v'].'/hqdefault.jpg" alt="" style="max-width:220px" /></a>'; break;
					case 'vimeo':
						$url = parse_url($img['src'], PHP_URL_PATH);
						$xml = simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
						$thumb = $xml->video->thumbnail_large;
						$img['src'] = '<a href="http://player.vimeo.com/video'.$url.'?portrait=0" rel="shadowbox;width=720;height=405"><img src="'.$thumb.'" alt="" style="max-width:220px" /></a>'; break;
				}
				
				$return = str_replace("#IMG#",$img['src'], $return);
				$return = str_replace("#TYPE#",$img['type'], $return);
				
				$vote_query = mysql_query("SELECT SUM(vote) FROM `tentego_img_vote` WHERE `object_id`=".$img['id']);
				$all_votes = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_vote` WHERE `object_id`=".$img['id']));
				if($all_votes) {
					$vote = mysql_fetch_array($vote_query);
					$proc = ceil($vote['SUM(vote)']/$all_votes*100);
					if($proc > 50) $return = str_replace("#VOTE#",'<span style="color:#06f000;">'.$proc.'%</span>', $return);
					else if($proc >= 30 && $proc <= 50) $return = str_replace("#VOTE#",'<span style="color:#f3c81a;">'.$proc.'%</span>', $return);
					else $return = str_replace("#VOTE#",'<span style="color:#f35118;">'.$proc.'%</span>', $return);
				}
				else $return = str_replace("#VOTE#",'<span style="color:#000;">Nie głosowano.</span>',$return);
				
				if($img['rel_date'] != '0000-00-00 00:00:00') {
					$time = new DateTime($img['rel_date']);
					$rel_date = $time->format("Y-m-d H:i:s");
				}
				else $rel_date = '<i>nigdy nie dodano</i>';
				
				$return = str_replace("#REL_DATE#",$rel_date, $return);
				
				$time = new DateTime($img['date']);
				
				$return = str_replace("#DATE#",$time->format("Y-m-d H:i:s"), $return);
				
				$cat = mysql_fetch_array(mysql_query("SELECT `name` FROM `tentego_img_cat` WHERE `id`=".$img['cat']));
				$return = str_replace("#CATEGORY#",$cat['name'], $return);
				
				$return = str_replace("#SOURCE#",$img['source'], $return);
				
				if($img['owner'] != 0) {
					$owner = mysql_fetch_array(mysql_query("SELECT `user` FROM `tablicacms_users` WHERE `id`=".$img['owner']));
					$owner_name = $owner['user'];
				}
				else {
					$owner_name = "Gość";
				}
				$return = str_replace("#OWNER#",$owner_name, $return);
				$i++;
				$result .= $return;
			}
			if($return == NULL) kernel::make_notify("Brak rekordów");
			return $result;
		}
		function moveto($x) {
			$set = NULL;
			if($x == 0) $set = ", `rel_date`='".date("YmdHis")."'";
			foreach($_POST['img'] as $id) {
				mysql_query("UPDATE `tentego_img` SET `is_waiting`=".$x.$set." WHERE `id`=".$id);
			}
		}
		function delete($id) {
			$query = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$id));
			if(mysql_query("DELETE FROM `tentego_img` WHERE `id`=".$id)) {
				if($query['type']=='img') {
					if(unlink('../'.$this->img.'/'.$query['src'])) kernel::make_notify("Usunięto obrazek z serwera.");
					else kernel::make_notify("Wystąpił błąd podczas usuwania obrazka.");
					kernel::make_notify("Pomyślnie usunięto wpis.");
				}
				else kernel::make_notify("Pomyślnie usunięto wpis z obiektem."); 
			}
			else kernel::make_notify("Wystąpił błąd podczas usuwania wpisu.");
		}
		function group_del() {
			$x = 0; $y = 0;
			foreach($_POST['img'] as $id) {
				if(mysql_query("DELETE FROM `tentego_img` WHERE `id`=".$id)) $x++;
				$y++;
			}
			kernel::make_notify("Usunięto <b>".$x."</b> z ".$y." elementów.");
		}
		function pagination($back_pattern, $pattern, $current_pattern, $next_pattern, $current, $count, $where) {
		
			$query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=$where");
			if($current<1 || !isset($current) || !is_numeric($current)) $current = 1;
			$i = ceil(mysql_num_rows($query)/$count);
			$return = NULL;
			if($current > 1) $return .= str_replace("#",$current-1,$back_pattern);
			if($current > 6) $return .= str_replace("#",1,$pattern).' ... ';
			for(($current>5?$j=$current-5:$j=1);$j<$current;$j++) {
				$text = str_replace("#",$j,$pattern);
				$return .= $text;
			}
			$return .= str_replace("#", $current, $current_pattern);
			for($j=$current+1;($current+5<$i?$j<$current+6:$j<=$i);$j++) {
				$text = str_replace("#",$j,$pattern);
				$return .= $text;
			}
			if($current+5<$i) $return .= ' z '.str_replace("#", $i, $pattern);
			if($current < $i && $i > 1) $return .= str_replace("#",$current+1,$next_pattern);
			
			return $return;
		}
		//Miniaturki
		function thumbnail($img) {
			ob_end_clean();
			header('Content-Type: image/png');
			kernel::loadLib("simpleimage");
			$image = new SimpleImage();
			$image->load($img);
			$image->resizeToWidth(480);
			$image->output();
			exit();
		}
	}
?>
