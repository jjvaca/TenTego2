<?php
class manager {
	var $randomQuery = NULL;
	//Wyświetlanie obiektów (lista)
	function getObjects($pattern, $where, $currentPage, $objPerPage, $categories = 0, $cat = NULL) {
		global $ads, $user, $rewrite;
		
		if($categories) {
			if(!empty($cat) && is_numeric($cat) && $cat > 0) $cat = ' AND `cat`='.mysql_real_escape_string($cat);
			else $cat = ' AND `cat`=-1';
		}
		
		if(isset($currentPage) && is_numeric($currentPage) && $currentPage > 0) {
			$page = mysql_real_escape_string($currentPage-1)*$objPerPage;
		}
		else $page = 0;
		
		if($where > 0) $query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting` LIKE ".$where.$cat." ORDER BY `date` DESC LIMIT ".$page.",".$objPerPage);
		else $query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting` LIKE ".$where.$cat." ORDER BY `rel_date` DESC, `date` DESC LIMIT ".$page.",".$objPerPage);
		
		$text = NULL;
		$return = NULL;
		while($img = mysql_fetch_array($query)) {
			
			$return .= $ads->load('<div class="block">#AD[object]#</div>');
			
			switch($img['type']) {
				case 'img': $object = '<a href="$1"><img src="'.kernel::host().'/upload/'.$img['src'].'" title="#TITLE#" alt="#TITLE#" /></a>'; break;
				case 'youtube':
					parse_str(parse_url($img['src'], PHP_URL_QUERY), $src); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://www.youtube.com/embed/'.$src['v'].'?wmode=transparent" frameborder="0" allowfullscreen></iframe>'; break;
				case 'vimeo':
					$vid = parse_url($img['src'], PHP_URL_PATH); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://player.vimeo.com/video'.$vid.'?portrait=0" frameborder="0" allowfullscreen></iframe>'; break;
			}
			
			$text = preg_replace("/\[object url=(.*)\]/", $object, $pattern);
			$text = str_replace("#TITLE#", $img['title'], $text);
			$text = str_replace("#REWRITE-TITLE#", $rewrite->changeSigns($img['title']), $text);
			$text = str_replace("#ID#", $img['id'], $text);
			
			$vote_query = mysql_query("SELECT SUM(vote) FROM `tentego_img_vote` WHERE `object_id`=".$img['id']);
			$all_votes = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_vote` WHERE `object_id`=".$img['id']));
			if($all_votes) {
				$vote = mysql_fetch_array($vote_query);
				$proc = ceil($vote['SUM(vote)']/$all_votes*100);
				if($proc > 50) $text = str_replace("#VOTE#",'<span style="color:#06f000;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
				else if($proc >= 30 && $proc <= 50) $text = str_replace("#VOTE#",'<span style="color:#f3c81a;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
				else $text = str_replace("#VOTE#",'<span style="color:#f35118;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
			}
			else $text = str_replace("#VOTE#",'<span style="color:#fff;" class="rate_'.$img['id'].'">?%</span>',$text);
			
			
			$date = new DateTime($img['date']);
			$text = str_replace("#DATE#", $date->format("Y.m.d H:i"), $text);
			
			if($img['owner'] != 0) {
				$owner = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$img['owner']));
				$owner_name = $owner['user'];
			}
			else {
				$owner_name = "Gość";
				$owner['id'] = 0;
			}
			
			$text = str_replace("#OWNER-ID#", $owner['id'], $text);
			$text = str_replace("#OWNER#", $owner_name, $text);
			$text = str_replace("#REWRITE-OWNER#", $rewrite->changeSigns($owner_name), $text);
			$text = str_replace("#SOURCE#", $img['source'], $text);
			
			# MODERATOR #
			
			$text = str_replace("#MOD_TOOLS#", $this->mod_tools($img), $text);
			
			# KONIEC MODERATORA #
			
			if($user->verifyLogin()) {
				$verify = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_fav` WHERE `object_id`=".$img['id']." AND `user_id`=".$user->userInfo('id')));
				if($verify) $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$2",$text);
				else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$1",$text);
			}
			else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"",$text);
			
			$cat = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img_cat` WHERE `id`=".$img['cat']));
			$text = str_replace("#CATEGORY#", '<a href="'.$rewrite->categories($cat['id'],$rewrite->changeSigns($cat['name'])).'">'.$cat['name'].'</a>', $text);
			
			if($img['type'] == 'youtube') {
				$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
				parse_str(parse_url($img['src'], PHP_URL_QUERY), $src);
				$text = str_replace("#SCREENSHOT#", 'http://img.youtube.com/vi/'.$src['v'].'/hqdefault.jpg', $text);
			}
			else if($img['type'] == 'vimeo') {
				$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
				$url = parse_url($img['src'], PHP_URL_PATH);
				$xml = @simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
				$text = @str_replace("#SCREENSHOT#", $xml->video->thumbnail_large, $text);
			}
			else { 
				$text = preg_replace("/\[video\=(.*)\]/", '', $text);
				$text = str_replace("#SCREENSHOT#", 'http://'.$this->host().'/upload/'.$img['src'], $text);
			}
			
			$return .= $text;
		}
		if(empty($return)) $return = "<div id=\"display_error\">Brak obiektów.</div>";
		
		return $return;
	}
	//Wyświetlanie obiektu (pojedynczy)
	function getObject($pattern, $id) {
		global $user, $rewrite;
		if(empty($id) || !is_numeric($id)) header("Location: ".$rewrite->index);
		else {		
			$id = mysql_real_escape_string($id);
			$query = mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$id);
			
			if(!mysql_num_rows($query)) header("Location: ".$rewrite->index);
			$text = NULL;
			$return = NULL;
			$img = mysql_fetch_array($query);
				
				switch($img['type']) {
					case 'img': $object = '<a href="$1"><img src="'.kernel::host().'/upload/'.$img['src'].'" title="#TITLE#" alt="#TITLE#" /></a>'; break;
					case 'youtube':
						parse_str(parse_url($img['src'], PHP_URL_QUERY), $src); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://www.youtube.com/embed/'.$src['v'].'?wmode=transparent" frameborder="0" allowfullscreen></iframe>'; break;
					case 'vimeo':
						$vid = parse_url($img['src'], PHP_URL_PATH); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://player.vimeo.com/video'.$vid.'?portrait=0" frameborder="0" allowfullscreen></iframe>'; break;
				}
				
				$text = preg_replace("/\[object url=(.*)\]/", $object, $pattern);
				$text = str_replace("#TITLE#", $img['title'], $text);
				$text = str_replace("#REWRITE-TITLE#", $rewrite->changeSigns($img['title']), $text);
				$text = str_replace("#ID#", $img['id'], $text);
			
				$vote_query = mysql_query("SELECT SUM(vote) FROM `tentego_img_vote` WHERE `object_id`=".$img['id']);
				$all_votes = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_vote` WHERE `object_id`=".$img['id']));
				if($all_votes) {
					$vote = mysql_fetch_array($vote_query);
					$proc = ceil($vote['SUM(vote)']/$all_votes*100);
					if($proc > 50) $text = str_replace("#VOTE#",'<span style="color:#06f000;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
					else if($proc >= 30 && $proc <= 50) $text = str_replace("#VOTE#",'<span style="color:#f3c81a;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
					else $text = str_replace("#VOTE#",'<span style="color:#f35118;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
				}
				else $text = str_replace("#VOTE#",'<span style="color:#fff;" class="rate_'.$img['id'].'">?%</span>',$text);
				
				$date = new DateTime($img['date']);
				$text = str_replace("#DATE#", $date->format("Y.m.d H:i"), $text);
				
				$owner = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$img['owner']));
				$text = str_replace("#OWNER-ID#", $owner['id'], $text);
				$text = str_replace("#OWNER#", $owner['user'], $text);
				$text = str_replace("#REWRITE-OWNER#", $rewrite->changeSigns($owner['user']), $text);
				$text = str_replace("#SOURCE#", $img['source'], $text);
			
				# MODERATOR #
				
				$text = str_replace("#MOD_TOOLS#", $this->mod_tools($img), $text);
				
				# KONIEC MODERATORA #
			
				if($user->verifyLogin()) {
					$verify = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_fav` WHERE `object_id`=".$img['id']." AND `user_id`=".$user->userInfo('id')));
					if($verify) $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$2",$text);
					else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$1",$text);
				}
				else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"",$text);
				
				$cat = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img_cat` WHERE `id`=".$img['cat']));
				$text = str_replace("#CATEGORY#", '<a href="'.$rewrite->categories($cat['id'],$rewrite->changeSigns($cat['name'])).'">'.$cat['name'].'</a>', $text);
				
				if($img['type'] == 'youtube') {
					$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
					parse_str(parse_url($img['src'], PHP_URL_QUERY), $src);
					$text = str_replace("#SCREENSHOT#", 'http://img.youtube.com/vi/'.$src['v'].'/hqdefault.jpg', $text);
				}
				else if($img['type'] == 'vimeo') {
					$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
					$url = parse_url($img['src'], PHP_URL_PATH);
					$xml = @simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
					$text = @str_replace("#SCREENSHOT#", $xml->video->thumbnail_large, $text);
				}
				else { 
					$text = preg_replace("/\[video\=(.*)\]/", '', $text);
					$text = str_replace("#SCREENSHOT#", 'http://'.$this->host().'/upload/'.$img['src'], $text);
				}
				
				$return .= $text;
				
			return $return;
		}
	}
	//Paginacja
	function pagination($back_pattern, $pattern, $current_pattern, $next_pattern, $count, $is_waiting, $current, $categories = 0, $cat = NULL) {
		
		if($categories) {
			if(!empty($cat) && is_numeric($cat) && $cat > 0) $cat = ' AND `cat`='.mysql_real_escape_string($cat);
			else $cat = ' AND `cat`=-1';
		}
		
		$query = mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=".$is_waiting.$cat);
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
	//Pobieranie kategorii
	function getCategories($pattern) {
		global $rewrite;
		$query = mysql_query("SELECT * FROM `tentego_img_cat` ORDER BY `name` ASC");
		
		$return = NULL;
		
		while($cat = mysql_fetch_array($query)) {
			$text = str_replace("#NAME#", $cat['name'],$pattern);
			$text = str_replace("#REWRITE-NAME#", $rewrite->changeSigns($cat['name']),$text);
			$text = str_replace("#ID#", $cat['id'],$text);
			
			$count = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=0 AND `cat`=".$cat['id']));
			$text = str_replace("#COUNT#", $count, $text);
			
			$return .= $text;
		}
		
		return $return;
	}
	//Losowy obiekt
	function random() {
		global $rewrite;
		$obj = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=0 ORDER BY RAND() LIMIT 1"));
		header("Location: ".$rewrite->img($obj['id'],$rewrite->changeSigns($obj['title'])));
	}
	//Licznik obiektów (glowan=0/poczekalnia=1/archiwum=2)
	function count($is_waiting) {
		if(empty($is_waiting) && !is_numeric($is_waiting)) return 0;
		else return mysql_num_rows(mysql_query("SELECT * FROM `tentego_img` WHERE `is_waiting`=".$is_waiting));
	}
	function host() {
		return $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	}
	//Wyszukajka obiektów :D
	function searchObjects($pattern, $search, $currentPage, $objPerPage) {
		global $ads,$user,$rewrite;
		
		if(empty($search)) return false;
		
		$search = mysql_real_escape_string(htmlspecialchars($search));
		
		if(isset($currentPage) && is_numeric($currentPage) && $currentPage > 0) {
			$page = mysql_real_escape_string($currentPage-1)*$objPerPage;
		}
		else $page = 0;
		
		$query = mysql_query("SELECT * FROM `tentego_img` WHERE `title` LIKE '%".$search."%' ORDER BY `rel_date` DESC, `date` DESC LIMIT ".$page.",".$objPerPage);
		$text = NULL;
		$return = NULL;
		while($img = @mysql_fetch_array($query)) {
			
			$return .= $ads->load('<div class="block">#AD[object]#</div>');
			
			switch($img['type']) {
				case 'img': $object = '<a href="$1"><img src="'.kernel::host().'/upload/'.$img['src'].'" title="#TITLE#" alt="#TITLE#" /></a>'; break;
				case 'youtube':
					parse_str(parse_url($img['src'], PHP_URL_QUERY), $src); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://www.youtube.com/embed/'.$src['v'].'?wmode=transparent" frameborder="0" allowfullscreen></iframe>'; break;
				case 'vimeo':
					$vid = parse_url($img['src'], PHP_URL_PATH); $object = '<iframe width="608" height="364" style="margin-bottom:30px; z-index:-100;" src="http://player.vimeo.com/video'.$vid.'?portrait=0" frameborder="0" allowfullscreen></iframe>'; break;
			}
			
			$text = preg_replace("/\[object url=(.*)\]/", $object, $pattern);
			$text = str_replace("#TITLE#", $img['title'], $text);
			$text = str_replace("#REWRITE-TITLE#", $rewrite->changeSigns($img['title']), $text);
			$text = str_replace("#ID#", $img['id'], $text);
			
			$vote_query = mysql_query("SELECT SUM(vote) FROM `tentego_img_vote` WHERE `object_id`=".$img['id']);
			$all_votes = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_vote` WHERE `object_id`=".$img['id']));
			if($all_votes) {
				$vote = mysql_fetch_array($vote_query);
				$proc = ceil($vote['SUM(vote)']/$all_votes*100);
				if($proc > 50) $text = str_replace("#VOTE#",'<span style="color:#06f000;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
				else if($proc >= 30 && $proc <= 50) $text = str_replace("#VOTE#",'<span style="color:#f3c81a;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
				else $text = str_replace("#VOTE#",'<span style="color:#f35118;" class="rate_'.$img['id'].'">'.$proc.'%</span>', $text);
			}
			else $text = str_replace("#VOTE#",'<span style="color:#fff;" class="rate_'.$img['id'].'">?%</span>',$text);
			
			$date = new DateTime($img['date']);
			$text = str_replace("#DATE#", $date->format("Y.m.d H:i"), $text);
			
			$owner = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$img['owner']));
			$text = str_replace("#OWNER-ID#", $owner['id'], $text);
			$text = str_replace("#OWNER#", $owner['user'], $text);
			$text = str_replace("#REWRITE-OWNER#", $rewrite->changeSigns($owner['user']), $text);
			$text = str_replace("#SOURCE#", $img['source'], $text);
			
			# MODERATOR #
			
			$text = str_replace("#MOD_TOOLS#", $this->mod_tools($img), $text);
			
			# KONIEC MODERATORA #
			
			if($user->verifyLogin()) {
				$verify = mysql_num_rows(mysql_query("SELECT * FROM `tentego_img_fav` WHERE `object_id`=".$img['id']." AND `user_id`=".$user->userInfo('id')));
				if($verify) $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$2",$text);
				else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"$1",$text);
			}
			else $text = preg_replace('/\[FAV\=(.*)\|(.*)\]/',"",$text);
			
			$cat = mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img_cat` WHERE `id`=".$img['cat']));
			$text = str_replace("#CATEGORY#", '<a href="'.$rewrite->categories($cat['id'],$rewrite->changeSigns($cat['name'])).'">'.$cat['name'].'</a>', $text);
			
			if($img['type'] == 'youtube') {
				$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
				parse_str(parse_url($img['src'], PHP_URL_QUERY), $src);
				$text = str_replace("#SCREENSHOT#", 'http://img.youtube.com/vi/'.$src['v'].'/hqdefault.jpg', $text);
			}
			else if($img['type'] == 'vimeo') {
				$text = preg_replace("/\[video\=(.*)\]/", '$1', $text);
				$url = parse_url($img['src'], PHP_URL_PATH);
				$xml = @simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
				$text = @str_replace("#SCREENSHOT#", $xml->video->thumbnail_large, $text);
			}
			else {
				$text = preg_replace("/\[video\=(.*)\]/", '', $text);
				$text = str_replace("#SCREENSHOT#", 'http://'.$this->host().'/upload/'.$img['src'], $text);
			}
			
			$return .= $text;
		}
		if(empty($return)) $return = "<div id=\"display_error\">Nie znaleziono obiektów spełniających kryteria wyszukiwania.</div>";
		
		return $return;
	}
	//Paginacja do wyszukiwarki
	function searchPagination($back_pattern, $pattern, $current_pattern, $next_pattern, $count, $current, $search) {
		
		if(empty($search)) return false;
		
		$search = mysql_real_escape_string(htmlspecialchars($search));
		
		$query = mysql_query("SELECT * FROM `tentego_img` WHERE `title` LIKE '%".$search."%'");
		if($current<1 || !isset($current) || !is_numeric($current)) $current = 1;
		$i = ceil(@mysql_num_rows($query)/$count);
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
	//Obiekty użytkownika :)
	function getProfileObjects($pattern, $profileId) {
		$return = NULL;
		if(!empty($profileId) && is_numeric($profileId)) {
			$profileId = mysql_real_escape_string($profileId);
			$query = mysql_query("SELECT * FROM `tentego_img` WHERE `owner`=".$profileId." ORDER BY `id` DESC");
		
			//This module is loading a thumbs of user uploaded objects like images and video.
			if(mysql_num_rows($query)>0) {
				while($img = mysql_fetch_array($query)) {
					
					switch($img['type']) {
						case 'img': $image = kernel::host().'/upload/'.$img['src']; break;
						case 'youtube':
							parse_str(parse_url($img['src'], PHP_URL_QUERY), $src); $image = 'http://i1.ytimg.com/vi/'.$src['v'].'/hqdefault.jpg'; break;
						case 'vimeo':
							$url = parse_url($img['src'], PHP_URL_PATH);
							$xml = @simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
							$image = @$xml->video->thumbnail_large;
					}
					
					$text = str_replace("#IMG#", $image, $pattern);
					$text = str_replace("#ID#", $img['id'], $text);
					$text = str_replace("#TITLE#", $img['title'], $text);
					
					$return .= $text;
				}
				return $return;
			}
			else echo 'Brak obiektów.';
		}
	}
	//Ulubione
	function getFavorites($pattern, $profileId) {
		if(!empty($profileId) && is_numeric($profileId)) {
			$profileId = mysql_real_escape_string($profileId);
			$query = mysql_query("SELECT * FROM `tentego_img_fav` WHERE `user_id`=".$profileId." ORDER BY `id` DESC");
			$return = NULL;
			//Moduł wczytuje ulubione obiekty użytkownika.
			if(mysql_num_rows($query)>0) {
				while($fav = mysql_fetch_array($query)) {
					
					$img = @mysql_fetch_array(mysql_query("SELECT * FROM `tentego_img` WHERE `id`=".$fav['object_id']));
					switch($img['type']) {
						case 'img': $image = kernel::host().'/upload/'.$img['src']; break;
						case 'youtube':
							parse_str(parse_url($img['src'], PHP_URL_QUERY), $src); $image = 'http://i1.ytimg.com/vi/'.$src['v'].'/hqdefault.jpg'; break;
						case 'vimeo':
							$url = parse_url($img['src'], PHP_URL_PATH);
							$xml = simplexml_load_file("http://vimeo.com/api/v2/video".$url.".xml");
							$image = $xml->video->thumbnail_large;
					}
					
					$text = str_replace("#IMG#", $image, $pattern);
					$text = str_replace("#ID#", $img['id'], $text);
					$text = str_replace("#TITLE#", htmlspecialchars($img['title'],ENT_QUOTES), $text);
					
					$return .= $text;
				}
				return $return;
			}
			else echo 'Brak ulubionych.';
		}
	}
	
	////////////////////////////////////////////////////
	// CZESC ODPOWIEDZALNA ZA DODAWANIE ////////////////
	////////////////////////////////////////////////////
	
	//Upload obrazkow
	function uploadImage($max_width, $max_file_size, $owner, $watermark) {
		$overwrite = 0;
		$dir = 'upload/';
		$file_types = array(1=>'jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG');
		$file_mimes = array(1=>'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
		$file_name = date('YmdHis').'uid'.$owner;

		if(isset($_POST['send_img'])) {	
			if(empty($_POST['title']) || !$_FILES['image']['name']) { $this->msg('Uzupełnij wszystkie pola!',1); }
			else { 
				if($_SESSION['img_number'] != strtolower($_POST['question'])) $this->msg('Kod weryfikacyjny nie jest poprawny!',1);
				else {
					$title = mysql_real_escape_string(htmlspecialchars($_POST['title']));
					if(!empty($_POST['source'])) $source = mysql_real_escape_string(htmlspecialchars($_POST['source']));
					else $source = '-----';
					$cat = mysql_real_escape_string(htmlspecialchars($_POST['cat']));
					$date = date('Y-m-d H:i:s');
					$is_waiting = 1;
					$type = 'img';
					if(empty($_SESSION['login'])) $owner = 0;
					
					if(filesize($_FILES['image']['tmp_name']) <= $max_file_size*1024) {
						$file_ex = pathinfo($_FILES['image']['name']);
						$image_info = @getimagesize($_FILES['image']['tmp_name']);
							if(array_search($file_ex['extension'],$file_types) && array_search($image_info['mime'],$file_mimes)) {
								if($overwrite == 0 && file_exists($dir.$file_name.".".$file_ex['extension'])) $this->msg('Taki plik już istnieje.',1);
								if(!move_uploaded_file($_FILES['image']['tmp_name'],$dir.$file_name.".".$file_ex['extension'])) $this->msg('Wgrywanie pliku nie powiodło się.',1);
								else { 
									$upload_dir = $dir.$file_name.".".$file_ex['extension'];
									$file_src = $file_name.".".$file_ex['extension'];
									mysql_query("INSERT INTO `tentego_img` (`title`,`src`,`type`,`owner`,`cat`,`date`,`source`,`is_waiting`)
												VALUES ('$title','$file_src','$type','$owner','$cat','$date','$source','$is_waiting')");
									
									//Zmina rozmiaru obrazka
									list($width, $height, $type, $attr) = getimagesize($upload_dir);
									if($width > $max_width) {
										require_once('admin/lib/imageworkshop.lib.php');
										$imageLayer = new ImageWorkshop(array(
											"imageFromPath" => $upload_dir,
										));		 
										$imageLayer->resizeInPixel($max_width, null, true);
										$createFolders = false;
										$backgroundColor = null;
										$imageQuality = 95; 
										$imageLayer->save($dir, $file_name.".".$file_ex['extension'], $createFolders, $backgroundColor, $imageQuality);
									}
									
									if($watermark) $this->watermark($upload_dir, $watermark, $dir);
									$this->msg('Obiekt został pomyślnie załadowany.',3);
								}
							}
						else $this->msg('Niedozwolone rozszerzenie lub typ pliku!',1);
					}
					else $this->msg('Wybrany plik jest za wielki! Dozwolony rozmiar to '.$max_file_size.' kB.',1);
				}
			}
		}
    }
	
	//Dodawanie obiektow filmowych
	function addMovie($owner, $type_video) {
		if(isset($_POST['send_movie'])) {
			if(empty($_POST['title']) || empty($_POST['src'])) { $this->msg('Uzupełnij wszystkie pola!',1); }
			else {
				if($_SESSION['img_number'] != strtolower($_POST['question'])) $this->msg('Kod weryfikacyjny nie jest poprawny!',1);
				else {
					$title = mysql_real_escape_string(htmlspecialchars($_POST['title'],ENT_QUOTES));
					$src = mysql_real_escape_string(htmlspecialchars($_POST['src']));
					$cat = mysql_real_escape_string(htmlspecialchars($_POST['cat']));
					$date = date('Y-m-d H:i:s');
					switch($type_video) {
						case 'youtube':
							$source = 'YouTube.com';
							$type = 'youtube';
							if(!preg_match("/youtube.com\/watch\?v\=/", $src)) { $this->msg('Link jest niepoprawny.',1); return false; }
							// Sprawdzanie czy video istnieje - YouTube
							parse_str(parse_url($src, PHP_URL_QUERY), $parsedLink);
							$getHeadersToVerify = get_headers("http://gdata.youtube.com/feeds/api/videos/".$parsedLink['v']);
							if(!strpos($getHeadersToVerify[0],'200')) { $this->msg('Nie można dodać klipu wideo, ponieważ nie isnieje.',1); return false; }
							break;
						case 'vimeo':
							$source = 'Vimeo';
							$type = 'vimeo';
							if(!preg_match("/vimeo.com\//", $src)) { $this->msg('Link jest niepoprawny.',1); return false; }
							// Sprawdzanie czy video istnieje - Vimeo
							$parsedLink = parse_url($src, PHP_URL_PATH);
							$getHeadersToVerify = get_headers("http://vimeo.com/api/v2/video".$parsedLink.".xml");
							if(!strpos($getHeadersToVerify[0],'200')) { $this->msg('Nie można dodać klipu wideo, ponieważ nie isnieje.',1); return false; }
							break;
					}
					$is_waiting = 1;
					if(empty($_SESSION['login'])) $owner = 0;
					
					$query = mysql_query("INSERT INTO `tentego_img` (`title`,`src`,`type`,`owner`,`cat`,`date`,`source`,`is_waiting`)
										VALUES ('$title','$src','$type','$owner','$cat','$date','$source','$is_waiting')");
					
					if($query) $this->msg('Obiekt został poprawnie dodany!',3);
					else $this->msg('Nie udało się dodać obiektu.',1);
				}
			}
		}
	}
	
	//Lista kategorii w opcji rozwijanej
	function catList() {
		$query = mysql_query("SELECT * FROM `tentego_img_cat`");
		$text = NULL;
		while($cat = mysql_fetch_array($query)) {
			$text .= '<option value="'.$cat['id'].'">'.$cat['name'].'</option>'."\n";
		}
		return $text;
	}
	
	function uploadForm($rewrite, $type) {
		if($type == 'img') {
			echo '<form action="'.$rewrite->add.'" method="post" enctype="multipart/form-data">
						<label>Nazwa</label>
						<input type="text" name="title" maxlength="64" value="'.(isset($_POST['title'])?htmlspecialchars($_POST['title']):'').'" />
						<label>Plik</label>
						<input type="file" name="image" />
						<label>Kategoria</label>
						<select name="cat">
							'.$this->catList().'
						</select>
						<label>Źródło</label>
						<input type="text" name="source" maxlength="64" value="'.(isset($_POST['source'])?htmlspecialchars($_POST['source']):'').'" />
						<label><img src="'.kernel::host().'/admin/lib/captcha/image.php" alt="Captcha"></label>
						<input type="text" name="question" />
						<br/>
						<input type="submit" name="send_img" value="Dodaj" />
					</form>';
		}
		else if($type == 'youtube') {
			echo '<form action="'.$rewrite->add.'" method="post" enctype="multipart/form-data">
						<input type="hidden" name="type" value="youtube" />
						<label>Nazwa</label>
						<input type="text" name="title" maxlength="64" value="'.(isset($_POST['title'])?htmlspecialchars($_POST['title']):'').'" />
						<label>Adres klipu YouTube</label>
						<input type="text" name="src" value="'.(isset($_POST['src'])?htmlspecialchars($_POST['src']):'').'" />
						<label>Kategoria</label>
						<select name="cat">
							'.$this->catList().'
						</select>
						<label><img src="'.kernel::host().'/admin/lib/captcha/image.php" alt="Captcha"></label>
						<input type="text" name="question" />
						<br/>
						<input type="submit" name="send_movie" value="Dodaj" />
					</form>';
		}
		else if($type == 'vimeo') {
			echo '<form action="'.$rewrite->add.'" method="post" enctype="multipart/form-data">
						<input type="hidden" name="type" value="vimeo" />
						<label>Nazwa</label>
						<input type="text" name="title" maxlength="64" value="'.(isset($_POST['title'])?htmlspecialchars($_POST['title']):'').'" />
						<label>Adres klipu Vimeo</label>
						<input type="text" name="src" value="'.(isset($_POST['src'])?htmlspecialchars($_POST['src']):'').'" />
						<label>Kategoria</label>
						<select name="cat">
							'.$this->catList().'
						</select>
						<label><img src="'.kernel::host().'/admin/lib/captcha/image.php" alt="Captcha"></label>
						<input type="text" name="question">
						<br/>
						<input type="submit" name="send_movie" value="Dodaj" />
					</form>';
		}
	}
	
	//Znak wodny TOP SECRET!
	function watermark($file, $watermark, $dir) {
		require_once('admin/lib/imageworkshop.lib.php');
		$info = pathinfo($file);
		
		if($info['extension'] != 'gif') {
			$imageLayer = new ImageWorkshop(array(
				"imageFromPath" => $file,
			));	 
			$watermarkLayer = new ImageWorkshop(array(
				"imageFromPath" => $watermark,
			));		 
			$imageLayer->addLayer(1, $watermarkLayer, 5, 5, "RB");
			
			$createFolders = false;
			$backgroundColor = null;
			$imageQuality = 100;
			 
			$imageLayer->save($dir, $info['filename'].'.'.$info['extension'], $createFolders, $backgroundColor, $imageQuality);
		}
	}
	//mod_tools - zarządzanie obiektami przez moderatorów
	
	private function mod_tools($img) {
		global $user;
		
		if($user->verifyLogin()) {
				if($user->userInfo('rank') <= 1) {
					$mod_tools = '<div class="mod_tools">';
					if($img['is_waiting'])
						$mod_tools .= '<a href="#" onClick="mod_move('.$img['id'].'); return false;">Przenieś na główną</a>';
					else
						$mod_tools .= '<a href="#" onClick="mod_move('.$img['id'].'); return false;">Przenieś do poczekalni</a>';
					
					$mod_tools .= '<a href="#" onClick="mod_amove('.$img['id'].'); return false;">Przenieś do archiwum</a>';
					$mod_tools .= '<a href="#" onClick="mod_del('.$img['id'].'); return false;">Usuń</a>';
					
					
					$mod_tools .= '</div>';
					
					return $mod_tools;
				}
				else
					return NULL;
			}
			else
				return NULL;
	}
		
	
	//Wiadomosci szablonowe
	function msg($text, $type) {
		switch($type) {
			case 1: $this->msg = '<div class="msg error">'.$text.'</div>'; break;
			case 2: $this->msg = '<div class="msg alert">'.$text.'</div>'; break;
			case 3: $this->msg = '<div class="msg good">'.$text.'</div>';
		}
		echo $this->msg;
	}
	
	function comments($pattern, $page_settings) {
		if($page_settings) { 
			$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$pattern = str_replace("#URL#",'http://'.$url,$pattern);
			echo $pattern; 
		}
		else echo 'Komentarze są wyłączone.';
	}
	
	
}
?>