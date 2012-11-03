<?php
class inbox {
	
	var $table = 'tentego_inbox';
	var $msg = NULL;
	
	//Wyswietlenie odpowiednich buttonow
	function buttons() {
		if(empty($_GET['go'])) {
			echo '<div class="inbox_buttons"><a href="?go=new" class="inbox_button">Napisz nową</a></div>';
		}
		else if($_GET['go']=='view' && is_numeric($_GET['id'])) {
			echo '<div class="inbox_buttons">';
			echo '<a href="?go=new&reply='.$_GET['id'].'" class="inbox_button">Odpowiedz</a>';
			echo '<a href="?delete='.$_GET['id'].'" class="inbox_button">Usuń</a>';
			echo '</div>';
		}
	}
	
	//Wyswietlenie calej funkcjonalnosci
	function pms($user_id) {
		global $rewrite;
		if($this->appConf('active') && $user_id!=NULL) {
			$this->delete($user_id);
			
			if(empty($_GET['go'])) {
				//Lista wadomosci
				if($this->get_total_pms($user_id)>0) {
					echo '<table width="100%">
						<tr>
							<th>Tytuł</th> <th width="20%">Nadawca</th> <th  align="right" width="20%">Data wysłania</th> <th align="right" width="3%"></th>
						</tr>
						'.$this->getPMS('
						<tr>
						<td><a href="?go=view&id=#ID#">#SUBJECT#</a></td> <td width="20%">#FROM#</td> <td align="right" width="20%">#DATE#</td> <td align="right" width="2%" valign="middle"><a href="?delete=#ID#" title="Usuń"><img src="'.kernel::host().'/admin/apps/inbox/img/delete.png" alt="delete"/></a></td>
						</tr>',$user_id,@$_GET['page'],10).'
					</table>';
					echo '<div style="margin-top:10px;text-align:center;">'.$this->pagination(' <a href="?page=#">&laquo;</a> ',' <a href="?page=#">#</a> ', ' [ # ] ', ' <a href="?page=#">&raquo;</a> ',$user_id,@$_GET['page'],10).'</div>';
				}
				else $this->msg('Brak wiadomości',2);
			}
			else {
				//Czytanie wiadomosci
				if($_GET['go'] == 'view'){
					if(is_numeric($_GET['id']))
					{
						$msg_id = $_GET['id'];
						mysql_query("UPDATE `$this->table` SET `read`='1' WHERE `id`='$msg_id'");
						echo $this->getPM('<table width="100%">
						<tr>
						<th>#SUBJECT#</th> <th align="right">Nadawca: #FROM#</th>
						</tr>
						</table><br/>#CONTENT#', $msg_id);
					}
					else $this->msg('Niepoprawny numer ID wiadomości', 1);
				}
				//Tworzenie nowej wiadomosci
				else if($_GET['go'] == 'new') {
					if(isset($_GET['reply']) && is_numeric($_GET['reply']))
					{
						$query_pm = mysql_query("SELECT * FROM `tentego_inbox` WHERE `id`='".$_GET['reply']."' AND `to`='$user_id'");
						$reply_pm = mysql_fetch_array($query_pm);
						$query_user = mysql_query("SELECT `user` FROM `tablicacms_users` WHERE `id`='".$reply_pm['from']."'");
						$reply_user = mysql_fetch_array($query_user);
						$_POST['to'] = $reply_user['user'];
						$_POST['subject'] = 'Re: '.$reply_pm['subject'];
						$_POST['content'] = '[quote='.$reply_user['user'].']'.$reply_pm['content'].'[/quote]';
					}
					if(isset($_GET['user']) && is_numeric($_GET['user']))
					{
						$query_user = mysql_query("SELECT `user` FROM `tablicacms_users` WHERE `id`='".$_GET['user']."'");
						$reply_user = mysql_fetch_array($query_user);
						$_POST['to'] = $reply_user['user'];
					}
					if(isset($_POST['submit'])) {
						if(!empty($_POST['to']) && !empty($_POST['subject']) && !empty($_POST['content']))
						{
							$to = htmlspecialchars(mysql_real_escape_string($_POST['to']));
							$subject = htmlspecialchars(mysql_real_escape_string($_POST['subject']));
							$content = htmlspecialchars(mysql_real_escape_string($_POST['content']));
							
							$query_user = mysql_query("SELECT `id` FROM `tablicacms_users` WHERE `user`='$to'");
							if(mysql_num_rows($query_user)>0) {
								$user = mysql_fetch_array($query_user);
								$to = $user['id'];
								$date = date('Y-m-d H:i:s');
								$from = $user_id;
								$query_send = mysql_query("INSERT INTO `$this->table` (`subject`,`content`,`to`,`from`,`date`,`read`) VALUES ('$subject','$content','$to','$from','$date',0)");
								if($query_send) {
									$this->msg('Wiadomość została poprawnie wysłana!', 3); 
									$_POST['to'] = NULL; $_POST['subject'] = NULL;  $_POST['content'] = NULL; 
								} else $this->msg('Niestety nie udało się wysłać wiadomości.', 1);
							}
							else $this->msg('Taki użytkownik nie istnieje!', 1);
						}
						else $this->msg('Wypełnij wszystkie pola!', 1);
					}
					echo '
					<script src="admin/apps/inbox/scripts.js"></script>
					<form action="'.$rewrite->inbox.'?go=new" method="post">
						<label>Odbiorca</label>
						<input type="text" name="to" value="'.(isset($_POST['to'])?htmlspecialchars($_POST['to']):'').'"/>
						<label>Tytuł</label>
						<input type="text" name="subject" maxlength="80" value="'.(isset($_POST['subject'])?htmlspecialchars($_POST['subject']):'').'">';
						if($this->appConf('bbcode')) {
						echo '<label></label>
							<div class="options">
								<input type="button" value="b" onClick="wstaw(\'[b]\',\'[/b]\')" />
								<input type="button" value="i" onClick="wstaw(\'[i]\',\'[/i]\')" />
								<input type="button" value="u" onClick="wstaw(\'[u]\',\'[/u]\')" />
								<input type="button" value="s" onClick="wstaw(\'[s]\',\'[/s]\')">
								<input type="button" value="link" onClick="wstaw(\'[url]\',\'[/url]\')" />
								<input type="button" value="obrazek" onClick="wstaw(\'[img]\',\'[/img]\')" />
								<input type="button" value="cytat" onClick="wstaw(\'[quote]\',\'[/quote]\')" />
								<input type="button" value="kod" onClick="wstaw(\'[code]\',\'[/code]\')" />
							</div>';
						}
						echo '<label>Treść</label>
						<textarea id="content_input" name="content">'.(isset($_POST['content'])?htmlspecialchars($_POST['content']):'').'</textarea>
						<br/>
						<input type="submit" name="submit" value="Wyślij" />
					</form>
					';
				}
			}
		}
		else $this->msg('Prywatne Wiadomości są wyłączone.', 1);
	}

	//Pobranie danych o wiadomosciach i zastapienie w szablonie
	function getPMS($pattern, $user_id, $currentPage, $objPerPage) {
		$i = 1;
		$return = NULL;
		$result = NULL;
		
		if(isset($currentPage) && is_numeric($currentPage) && $currentPage > 0) {
				$page = mysql_real_escape_string($currentPage-1)*$objPerPage;
		}
		else $page = 0;
		
		$query = mysql_query("SELECT * FROM `$this->table` WHERE `to`='$user_id' ORDER BY `id` DESC LIMIT $page,$objPerPage");
		
			while($msg = mysql_fetch_array($query)) {
				$return = str_replace("#ID#",$msg['id'], $pattern);
				$from = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$msg['from']));
				$return = str_replace("#FROM#",'<a href="user.php?id='.$from['id'].'">'.$from['user'].'</a>', $return);
				$to = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$msg['to']));
				$return = str_replace("#TO#",'<a href="user.php?id='.$to['id'].'">'.$to['user'].'</a>', $return);
				if($msg['read']) $subject = $msg['subject']; else $subject = '<b>'.$msg['subject'].'</b>';
				$return = str_replace("#SUBJECT#",$subject, $return);	
				$return = str_replace("#CONTENT#",$msg['content'], $return);
				$return = str_replace("#DATE#",$msg['date'], $return);			
				$i++;
				$result .= $return;
			}
			return $result;
	}
									   
	//Pobranie danych o JEDNEJ wiadomosci i zastapienie w szablonie
	function getPM($pattern, $pm_id) {
		$query = mysql_query("SELECT * FROM `$this->table`WHERE `id`='$pm_id'");
		if(mysql_num_rows($query)>0) {
			$msg = mysql_fetch_array($query);
			$return = NULL;
			$result = NULL;
			$return = str_replace("#ID#",$msg['id'], $pattern);
			$from = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$msg['from']));
			$return = str_replace("#FROM#",'<a href="user.php?id='.$from['id'].'">'.$from['user'].'</a>', $return);
			$to = mysql_fetch_array(mysql_query("SELECT * FROM `tablicacms_users` WHERE `id`=".$msg['to']));
			$return = str_replace("#TO#",'<a href="user.php?id='.$to['id'].'">'.$to['user'].'</a>', $return);
			$return = str_replace("#SUBJECT#",$msg['subject'], $return);
			$return = str_replace("#CONTENT#",($this->appConf('bbcode')?$this->bbcode(nl2br($msg['content'])):nl2br($msg['content'])), $return);
			$return = str_replace("#DATE#",$msg['date'], $return);			
			$result .= $return;
			return $result;
		} else $this->msg('Taka wiadomość nie istnieje.', 1);
	}
	
	//Zastopienie znakow BBCode HTMLem
	function bbcode($text) {
		$text = preg_replace("#\[b\](.*?)\[/b\]#si",'<b>\\1</b>',$text);
		$text = preg_replace("#\[i\](.*?)\[/i\]#si",'<i>\\1</i>',$text);
		$text = preg_replace("#\[u\](.*?)\[/u\]#si",'<u>\\1</u>',$text);
		$text = preg_replace("#\[s\](.*?)\[/s\]#si",'<s>\\1</s>',$text);
		$text = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="\\1" />',$text);
		$text = preg_replace("#\[url\](http.*?)\[/url\]#si", "<a href=\"\\1\">\\1</a>",$text);
		$text = preg_replace("#\[url=(http.*?)\](.*?)\[/url\]#si", "<a href=\"\\1\">\\2</a>",$text);
		$text = preg_replace("#\[url\](.*?)\[/url\]#si", "<A HREF=\"http://\\1\">\\1</A>", $text);
		$text = preg_replace("#\[url=(.*?)\](.*?)\[/url\]#si", "<A HREF=\"http://\\1\">\\2</A>", $text);
		$text = preg_replace("#\[code\](.*?)\[/code\]#si",'<pre>\\1</pre>',$text);
		
		//Dla cytatow w cytatach :D
		preg_match_all('/\[quote\]/i', $text, $matches);
        $opentags = count($matches['0']);

        preg_match_all('/\[\/quote\]/i', $text, $matches);
        $closetags = count($matches['0']);

        $unclosed = $opentags - $closetags;
        for ($i = 0; $i < $unclosed; $i++) {
            $text .= '</blockquote>';
        }
		
		$text = str_replace ('[quote]', '<blockquote>', $text);
		$text = preg_replace('/\[quote\=(.*?)\]/is','<blockquote><cite>\\1 napisał(a):</cite>', $text);
		$text = str_replace ('[/quote]', '</blockquote>', $text);
		
		return $text;
	}
	
	//Uusuwanie wiadomosci
	function delete($user_id) {
		global $rewrite;
		if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
				mysql_query("DELETE FROM `tentego_inbox` WHERE `id`='".$_GET['delete']."' AND `to`='$user_id'");
				header('Location: '.$rewrite->inbox);
		}
	}
	
	//Ilosc wiadomosci do szablonu
	function pms_count($pattern, $user_id) {
		$total = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `$this->table` WHERE `to` = '$user_id'"), 0);
		$return = str_replace("#TOTAL#",$total, $pattern);
		$new = mysql_result(mysql_query("SELECT COUNT(`id`) FROM `$this->table` WHERE `to` = '$user_id' AND `read` = '0'"), 0);
		$return = str_replace("#NEW#",$new, $return);
		$result = $return;
		echo $result;
	}
	
	//Ilosc wszystkich wiadomosci
	function get_total_pms($user_id) {
		return mysql_result(mysql_query("SELECT COUNT(`id`) FROM `tentego_inbox` WHERE `to` = '$user_id'"), 0);	
	}
	//Ilosc nowych wiadomosci
	function get_new_pms($user_id) {
		return mysql_result(mysql_query("SELECT COUNT(`id`) FROM `tentego_inbox` WHERE `to` = '$user_id' AND `read`='0'"), 0);	
	}
	
	//Informacje o wiadomosci
	function pmInfo($col, $msg_id) {
		if(is_numeric($msg_id)) {
			$query = mysql_fetch_array(mysql_query("SELECT `$col` FROM `$this->table` WHERE `id`='$msg_id'"));
			return $query[$col];
		}
	}	
	
	//Powiedomienie o nowej wiadomosci
	function notification($user_id) {
		global $rewrite;
		$page = explode("/",$_SERVER['SCRIPT_NAME']);
		if(($this->get_new_pms($user_id)>0) && (end($page) != 'inbox.php') && ($this->appConf('active')))
		{
			echo '
			<script language="javascript">			
			$(function() {
				$(\'a.close\').click(function() {
					$($(this).attr(\'href\')).hide("slow");
					return false;
				});
			});
			</script>
			<div id="pm_notification">
				<a href="'.$rewrite->inbox.'">Otrzymano nową wiadomość! Kliknij tutaj, aby przejść do skrzynki odbiorczej.</a>
				<a href="#pm_notification" class="close">x</a>
			</div>';
		}
	}
	
	//Link w profilu do napisania PW
	function profile_link($user_id) {
		global $rewrite;
		if($this->appConf('active')) {
			return '<div class="inbox_buttons"><a href="'.$rewrite->inbox.'?go=new&user='.$user_id.'" class="inbox_button">Wyślij Wiadomość</a></div>';
		}
	}
	
	//Ustawienia aplikacji
	function appConf($col) {
			$query = mysql_fetch_array(mysql_query("SELECT `$col` FROM `tentego_inbox_conf` WHERE `id`='1'"));
			return $query[$col];
	}
	
	//Komunikaty systemowe
	function msg($text, $type) {
		switch($type) {
			case 1: $this->msg = '<div class="msg error">'.$text.'</div>'; break;
			case 2: $this->msg = '<div class="msg alert">'.$text.'</div>'; break;
			case 3: $this->msg = '<div class="msg good">'.$text.'</div>';
		}
		echo $this->msg;
	}
	
	//Paginacja
	function pagination($back_pattern, $pattern, $current_pattern, $next_pattern, $to, $current, $count) {
		
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `to` = '$to'");
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
	
}

?>