<?php
class mainContent {
	function myCSS() {
		return '
		span { color:red; }
		';
	}
	function init() {
			
		echo '
			<div id="apigui">
				<div id="options">
					Zaktualizuj swój TenTego już teraz! Jedyne co będziesz potrzebował to wygenerowany plik zawierający wszystkie dane z Twojej porzedniej bazy skryptu.
					Umieść go w poniższym formularzu i zaakceptuj. Programy wykona wszystko za Ciebie. Po pomyślnym imporcie wszystkie Twoje dane powinny być na swoim miejscu.
				</div>
				<div style="margin-top:50px; text-align:center;">
				<form method="post" action="" enctype="multipart/form-data">
					<input type="file" name="xmlfile" /><br />
					<input type="submit" name="import" />
				</form>
				</div>
			</div>
		';
		
		
		if(isset($_POST['import'])) {
			echo '<div style="padding:50px; font-size:16px; line-height:24px; font-family:monospace;">';
			
			if($xml = simplexml_load_file($_FILES['xmlfile']['tmp_name'])) {
				echo '>> Wczytano plik<br />';
				
				echo '>> Baza obiektów: powinno:'.$xml->objects->count.', jest:'.count($xml->objects->img).'<br />';
				if($xml->objects->count == count($xml->objects->img)) {
					echo '>> Wgrywanie bazy...<br />';
					foreach($xml->objects->img as $img) {
						if(mysql_query("INSERT INTO `tentego_img` (`id`,`title`,`src`,`type`,`owner`,`cat`,`date`,`source`,`is_waiting`) VALUES (".$img['id'].",'".$img['title']."','".$img['img']."','".$img['type']."','".$img['owner']."',1,".$img['date'].",'".$img['source']."',".$img['is_waiting'].")")) echo '+';
						else echo '<span>+</span>';
					}
					echo '<br />';
				}
				else echo '<span>>> Dane się nie zgadzają. Pomijam.</span><br />';
				
				echo '>> Baza użytkowników: powinno: '.$xml->users->count.', jest:'.count($xml->users->user).'<br />';
				if($xml->users->count == count($xml->users->user)) {
					echo '>> Wgrywanie bazy...<br />';
					foreach($xml->users->user as $user) {
						if(mysql_query("INSERT INTO `tablicacms_users` (`id`,`user`,`pass`,`email`,`rank`,`active`) VALUES (".$user['id'].",'".$user['user']."','".$user['pass']."','".$user['email']."',".$user['rank'].",".$user['active'].")")) echo '+';
						else echo '<span>+</span>';
					}
					echo '<br />';
				}
				else echo '<span>>> Dane się nie zgadzają. Pomijam.</span><br />';
				
				echo '>> Baza reklam: powinno:'.$xml->ads->count.', jest:'.count($xml->ads->ad).'<br />';
				if($xml->ads->count == count($xml->ads->ad)) {
					echo '>> Wgrywanie bazy...<br />';
					foreach($xml->ads->ad as $ad) {
						if(mysql_query("INSERT INTO `tentego_ads` (`title`,`code`,`date`,`place`,`object_nr`,`active`) VALUES ('".$ad['title']."','".$ad['code']."',".$ad['date'].",".$ad['place'].",".$ad['object_nr'].",".$ad['active'].")")) echo '+';
						else echo '<span>+</span>';
					}
					echo '<br />';
				}
				else echo '<span>>> Dane się nie zgadzają. Pomijam.</span><br />';
				
				echo '>> Ustawienia:<br />';
				if($set = $xml->conf) {
					echo '>> Wgrywanie bazy...<br />';
					if(mysql_query("UPDATE `tentego_settings` SET `title`='".$set['title']."', `slogan`='".$set['slogan']."', `logo`='".htmlspecialchars_decode($set['logo'])."', `description`='".$set['description']."', `tags`='".$set['tags']."', `objects_per_page`='".$set['objects_per_page']."', `object_title`='".$set['object_title']."', `regulations`='".htmlspecialchars_decode($set['regulations'])."', `req_code`='".$set['req_code']."', `comments`='".$set['comments']."', `max_file_size`='".$set['max_file_size']."', `ads`='".$set['ads']."' WHERE `id`=1")) echo '+';
					else echo '<span>+</span>';
					
					echo '<br />';
				}
				
				echo '<b>Importowanie zakończone. Gratuluję!</b>';
			}
			else echo '<span>>> Plik jest niepoprawny</span>';
			
		echo '</div>';
		}
		
	}
}
?>