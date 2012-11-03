<?php

class ads {
	var $i = 0;
		
	var $table = 'tentego_ads';
	
	function load($pattern) {
		$result = NULL;
		
		if(strpos($pattern, 'left')) {
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `place`='1' AND `active`='1'");
			if(mysql_num_rows($query)) {
				$ad = mysql_fetch_array($query);
				//Sprawdzenie czy reklama nadal moze sie wyswietlac
				if($ad['date'] < date('Y-m-d')) {
					mysql_query("UPDATE `$this->table` SET `active` = '0' WHERE `id` = '".$ad['id']."'");
					$result = NULL;
				}
				else {
					$result = str_replace("#AD[left]#", stripslashes($ad['code']), $pattern);
				}
			}
			else {
				$result = NULL;
			}
		}
		if(strpos($pattern, 'right')) {	
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `place`='3' AND `active`='1'");
			if(mysql_num_rows($query)) {
				$ad = mysql_fetch_array($query);
				//Sprawdzenie czy reklama nadal moze sie wyswietlac
				if($ad['date'] < date('Y-m-d')) {
					mysql_query("UPDATE `$this->table` SET `active` = '0' WHERE `id` = '".$ad['id']."'");
					$result = NULL;
				}
				else {
					$result = str_replace("#AD[right]#", stripslashes($ad['code']), $pattern);	
				}
			}
			else {
				$result = NULL;
			}
		}
		
		if(strpos($pattern, 'object')) {
			$query = mysql_query("SELECT * FROM `$this->table` WHERE `place`='2' AND `active`='1'");
			if(mysql_num_rows($query)) {
					while($ad = mysql_fetch_array($query)) {
						//Sprawdzenie czy reklama nadal moze sie wyswietlac
						if($ad['date'] < date('Y-m-d')) {
							mysql_query("UPDATE `$this->table` SET `active` = '0' WHERE `id` = '".$ad['id']."'");
							$result = NULL;
						}
						else {
							if($this->i==$ad['object_nr']) {
								$result = str_replace("#AD[object]#", stripslashes($ad['code']), $pattern);
							}
						}
					}
				$this->i++;
			}
			else {
				$result = NULL;
			}
		}
		
		return $result;
	}
	
}

?>