<?php
	class mainContent {
			
		// Metoda CSS
		function myCSS() {
		return '
		#notatki {
			margin: 10px;
			width: 97%; 
			height: 200px;
			background: #000;
			color: #c0c0c0;
			font-family: Lucida Console;
			font-weight: bold;
			font-size: 12px;
			overflow-y: scroll;
			border-style: double; 
			border-width: 3px;
		}
		
		#button_zapisz {
			margin-left: 6px;
			background:#0BC70B;
			padding:10px 20px;
			border:5px solid #EFFFEF;
			color: #fff;
			font-weight: bold;
		}
		#button_zapisz:hover {
			background:#0db80d;
		';
		}

		function init($get) {
			$this->notatki();
		}

		function notatki() {
			if(isset($_POST['zapisz'])) {
			$notatki = htmlspecialchars($_POST['notes']);
			$query = mysql_query("UPDATE `notes` SET `content`='$notatki'");
				if($query)  kernel::make_notify("Pomyślnie zapisano notatkę", "Notatki", 3); 
				else  kernel::make_notify("Nie udało sie zapisać notatki", "Notatki", 1); 
			}
			echo '<form action="" method="post">
			<textarea name="notes" id="notatki" rows="5" cols="45">';
			$result = mysql_fetch_row(mysql_query("SELECT `content` FROM `notes`"));
			echo $result['0'];
			echo '</textarea>
			<input type="submit" name="zapisz" value="Zapisz" id="button_zapisz"> 
			</form>';
		}

	}
?>
