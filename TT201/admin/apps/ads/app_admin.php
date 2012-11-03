<?php
	class mainContent {
		
		//Zmienne pomocnicze
		var $table = 'tentego_ads';

		
		// Metoda CSS
		function myCSS() {
		return '	
		@import url(\'lib/shadowbox/shadowbox.css\');
		#apigui .block {
			background: white;
			margin: 5px 10px;
			box-shadow: -1px 1px 5px #CCC;
			border: 1px solid #CCC;
			border-radius: 5px;
			width: 35%;
			color: black;
			padding: 15px;
			display: inline-block;
			text-align: left;
			font-size: 12px;
			padding-right: 100px;
			position: relative;
		}
		#apigui .block .description {
			display: block;
			padding-left: 2px;
		}
		#apigui .block input[type~=text] {
			width: 100% !important;
		}
		#apigui .block input[type~=radio] {
			margin: 3px 5px;
		}
		#apigui .block select {
			margin: 5px 0px;
			width: 100%;
		}
		#apigui .block h2 {
			font-weight: normal !important;
		}
		';
		}
		
		//Podstrony
		function subpages() {
			return array(
				'Reklamy' => 'list',
				'Dodaj nową' => 'new',
			);
		}
		
		//Wywolanie podstron
		function init($get) {
			switch($get) {
				case 'list': $this->ads(); break;
				case 'new': $this->new_ad(); break;
				case 'edit': $this->edit_ad(); break;
				default: $this->ads();
			}
		}

		function ads() {	
			$this->validity();
			if(isset($_GET['del'])) $this->delete($_GET['del']);
			kernel::loadLib('apiconfirm');
			apiconfirm::load();
			echo '<script src="lib/shadowbox/shadowbox.js"></script>
				<script type="text/javascript">
					Shadowbox.init();
				</script>';
			echo '<div id="apigui">
					<table>
						<tr>
							<th>podgląd kodu</th>
							<th>info</th>
							<th>opcje</th>
						</tr>'.
				$this->pobierzReklamy('<tr><td>#CODE#</td> <td><b>Nazwa:</b> #TITLE#<br/><b>Położenie:</b> #PLACE#<br/><b>Wyświetlana do:</b> #DATE#<br/><b>Aktywna:</b> #ACTIVE#</td> <td><a href="?go=ads&amp;feature=edit&amp;id=#ID#" rel="shadowbox;width=580">edytuj</a>, <a href="?go=ads&amp;feature=list&amp;del=#ID#" title="Czy na pewno chcesz usunąć <b>#TITLE#</b>?" class="apiconfirm">usuń</a></td></tr>')	
					.'</table>
			</div>';
			
		}
		
		function new_ad() {
			if(isset($_POST['save'])) {
				if((!empty($_POST['title'])) && (!empty($_POST['code'])) && (!empty($_POST['place']))) { 
					$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
					$query = mysql_query("INSERT INTO `$this->table` (title, code, date, place, object_nr) 
					VALUES ('".$_POST['title']."', '".mysql_real_escape_string($_POST['code'])."', '$date', '".$_POST['place']."', '".$_POST['object_nr']."')") or die(mysql_error());
					if($query) kernel::make_notify("Reklama została dodana");
					else kernel::make_notify("Nie udało się dodać reklamy");
				}
				else { kernel::make_notify("Uzupełnij wszystkie pola"); }
			}
			
			echo '<script type="text/javascript">
				$(document).ready(function(){
				$(\'select[name="place"]\').change( 
					function() { 
						if ($(\'select[name="place"] option[value="2"]\').is(":selected")) {
							$(\'span[class$="number"]\').attr(\'style\', \'display:block;\');
						} else {
							$(\'span[class$="number"]\').attr(\'style\', \'display:none;\');
						}
					})
				});
				</script>';
			echo '<div id="apigui">
				<form method="post" action="?go=ads&feature=new">
					<div id="options"><input type="submit" value="Zapisz" name="save"></div>
					<div class="block">
					<h2>Nazwa</h2>
						<span class="description">Nazwa reklamy - widoczna tylko dla administratorów.</span>
						<input type="text" name="title" />
					</div>
					<div class="block">
					<h2>Położenie</h2>
						<span class="description">Umiejscowienie reklamy w szablonie.</span>
						<select name="place">
							'.(!$this->check_place(1)?'<option value="1">lewa strona</option>':'').'
							<option value="2">pod obiektem</option>
							'.(!$this->check_place(3)?'<option value="3">prawa strona</option>':'').'	
						</select>
					<span class="number" style="display: none;">
						<span class="description" style="padding-top: 4px;">Podaj numer obiektu, pod którym będzie wyświetlana.</span>
						<input type="text" name="object_nr" value="1" />
					</span>
					</div>
					<div class="block">
					<h2>Data</h2>
						<span class="description">Data, do której reklama będzie aktywna.</span>
						'.$this->years().$this->months().$this->days().'
					</div>
					<div class="block">
					<h2>Kod</h2>
						<span class="description">Dozwolony kod reklamy to HTML.</span>
						<textarea name="code"></textarea>
					</div>
				</form>
			</div>';
		}
		
		function edit_ad() {
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
			.description {
				padding-top:4px;
				padding-left:3px;
				font-size:12px;
			}
			textarea {
				width: 95% !important;
				height: 100px;
			}
			</style>
			</head>
			<body>
			<div id="apigui">';
			
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `id`=".$_GET['id']);
			if(mysql_num_rows($query)) {
				if(isset($_POST['save'])) {
					$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
					if(mysql_query("UPDATE `$this->table` SET `title`='".$_POST['title']."', `date`='".$date."', `code`='".mysql_real_escape_string($_POST['code'])."', `place`='".$_POST['place']."', `object_nr`='".$_POST['object_nr']."', `active`='".$_POST['active']."' WHERE `id`=".$_GET['id'])) { $query = mysql_query("SELECT * FROM `$this->table` WHERE `id`=".$_GET['id']); kernel::make_notify("Zapisano."); }
					else kernel::make_notify("Bład podczas zapisu.");
				}
				$ad = mysql_fetch_array($query);
				$date_from_db = strtotime($ad['date']);
				
			echo '<script type="text/javascript">
				$(document).ready(function(){
				$(\'select[name="place"]\').change( 
					function() { 
						if ($(\'select[name="place"] option[value="2"]\').is(":selected")) {
							$(\'span[class$="number"]\').attr(\'style\', \'display:block;\');
						} else {
							$(\'span[class$="number"]\').attr(\'style\', \'display:none;\');
						}
				})
					if ($(\'select[name="place"] option[value="2"]\').is(":selected")) {
						$(\'span[class$="number"]\').attr(\'style\', \'display:block;\');
					}
				});
				</script>';	
				echo '<form method="post" action="?go=ads&amp;feature=edit&amp;id='.$ad['id'].'">';						
				echo '
					<div id="content">
						<table>
						<tr>
							<th>Nazwa:</th>
							<td><input type="text" name="title" value="'.$ad['title'].'" /></td>
						</tr>
						<tr>
							<th>Aktywna:</th>
							<td>
								<select name="active">
									<option value="1" '.($ad["active"]==1?'selected="selected"':'').'>tak</option>
									<option value="0" '.($ad["active"]==0?'selected="selected"':'').'>nie</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Położenie:</th>
							<td>
								<select name="place">
								';
								if($this->check_place(1,$ad['id'])) {
									echo '<option value="1" '.($ad["place"]==1?'selected="selected"':'').'>lewa strona</option>';
								}
								elseif(!$this->check_place(1)) {
									echo '<option value="1" '.($ad["place"]==1?'selected="selected"':'').'>lewa strona</option>';
								}
				
								echo '<option value="2" '.($ad["place"]==2?'selected="selected"':'').'>pod obiektem</option>';
				
								if($this->check_place(3,$ad['id'])) {
									echo '<option value="3" '.($ad["place"]==3?'selected="selected"':'').'>prawa strona</option>';
								}
								elseif(!$this->check_place(3)) {
									echo '<option value="3" '.($ad["place"]==3?'selected="selected"':'').'>prawa strona</option>';
								}
									
								/* echo '<option value="1" '.($ad["place"]==1?'selected="selected"':'').'>lewa strona</option>
										<option value="2" '.($ad["place"]==2?'selected="selected"':'').'>pod obiektem</option>
								<option value="3" '.($ad["place"]==3?'selected="selected"':'').'>prawa strona</option>'; */
								echo '</select>
								<span class="number" style="display: none;">
									<span class="description">Numer obiektu, pod którym będzie wyświetlana:</span>
									<input type="text" name="object_nr" value="'.$ad["object_nr"].'" />
								</span>
							</td>
						</tr>
						<tr>
							<th>Wyświetlana do:</th>
							<td>'.$this->years(date("Y",$date_from_db)).$this->months(date("m",$date_from_db)).$this->days(date("d",$date_from_db)).'</td>
						</tr>
						<tr>
							<th>Kod:</th>
							<td>
								<textarea name="code">'.htmlspecialchars($ad['code']).'</textarea>
							</td>
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
					setTimeout(function(){ $("#notify").fadeOut() }, 3000);
					$("#notify").click(function() { $(this).fadeOut(); });
					});
					</script>';
			}
			kernel::destroy_notify();
			exit();
		}
	
		
		/*****************************************/
		/* DODATKOWE FUNKCJE
		/*****************************************/
		function pobierzReklamy($pattern) {
		    $query = mysql_query("SELECT * FROM `$this->table` ORDER BY `date` DESC") or die(mysql_error());
			$i = 1;
			$return = NULL;
			$result = NULL;
			while($ad = mysql_fetch_array($query)) {
				$return = str_replace("#ID#",$ad['id'], $pattern);
				$return = str_replace("#CODE#",htmlspecialchars(stripslashes($ad['code'])), $return);
				$return = str_replace("#TITLE#",$ad['title'], $return);			
				switch($ad['active']) {
					case 0: $active = '<span style="color:red;">nie</span>'; break;
					case 1: $active = '<span style="color:green;">tak</span>';
				}	
				$return = str_replace("#ACTIVE#",$active, $return);
				$return = str_replace("#DATE#",$ad['date'], $return);		
				switch($ad['place']) {
					case 1: $place = 'lewa strona'; break;
					case 2: $place = 'pod obiektem'; break;
					case 3: $place = 'prawa strona';
				}			
				$return = str_replace("#PLACE#",$place, $return);
			
				$i++;
				$result .= $return;
			}
			if($return == NULL) kernel::make_notify("Brak rekordów");
			return $result;
		}
		
		function delete($id) {
			if(mysql_query("DELETE FROM `$this->table` WHERE `id`=".$id)) {
				 kernel::make_notify("Pomyślnie usunięto reklamę");
			}
			else kernel::make_notify("Wystąpił błąd podczas usuwania reklamy");
		}
		
		function months($from_db = NULL) {
			$months = array (1=>"Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień");
			$select = '<select name="month">';
			foreach ($months as $key => $val) {
				$format = sprintf("%02d", $key);
				if($from_db == NULL) {
				$select .= '<option value="'.$format.'" '.(date('m')==$key?'selected="selected"':'').'>'.$val.'</option>';
				} else {
				$select .= '<option value="'.$format.'" '.($from_db==$key?'selected="selected"':'').'>'.$val.'</option>';
				}
			}
			$select .= '</select>';
			return $select;
		}
		
		function days($from_db = NULL) {
			$select =  '<select name="day">';
			for ($i=1; $i<=31; $i++) {
				$format = sprintf("%02d", $i);
				if($from_db == NULL) {
					$select .= '<option value="'.$format.'" '.(date('d')==$i?'selected="selected"':'').'>'.$format.'</option>';
				} else {
					$select .= '<option value="'.$format.'" '.($from_db==$i?'selected="selected"':'').'>'.$format.'</option>';
				}
			}
			$select .= '</select>';		
			return $select;
		}
		
		function years($from_db = NULL) {
			$curr_year = date('Y');
			$select =  '<select name="year">';	
			$future_year = $curr_year + 2;
			for($i=$curr_year; $i<=$future_year; $i++) {
				if($from_db == NULL) {
					$select .= '<option value="'.$i.'" '.($curr_year==$i?'selected="selected"':'').'>'.$i.'</option>';
				} else {
					$select .= '<option value="'.$i.'" '.($from_db==$i?'selected="selected"':'').'>'.$i.'</option>';
				}
			}
			$select .= '</select>';		
			return $select;
		}
		
		function validity() {
			$query = mysql_query("SELECT * FROM `$this->table`") or die(mysql_error());
			while($ad = mysql_fetch_array($query)) {
				if($ad['date'] < date('Y-m-d')) {
					mysql_query("UPDATE `$this->table` SET `active` = '0' WHERE `id` = '".$ad['id']."'") or die(mysql_error());
				}
			}
		}
		
		function check_place($place, $id=NULL) {
			if($id == NULL) {
				$db_query = mysql_query("SELECT `place` FROM `$this->table` WHERE `place`='$place'");
				if(mysql_num_rows($db_query)) {
					$result = true;
				}
				else {
					$result = false;
				}
				return $result;
			}
			else {
				$db_query = mysql_query("SELECT `place` FROM `$this->table` WHERE `place`='$place' AND `id`='$id'");
				if(mysql_num_rows($db_query)) {
					$result = true;
				}
				else {
					$result = false;
				}
				return $result;
			}
		}
	
	
	}
?>
