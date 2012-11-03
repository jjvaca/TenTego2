<?php
class colgen {	
	public static function generate($name) {
    $hash = md5('vector'.$name);
    $color = hexdec(substr($hash, 0, 2)).','.hexdec(substr($hash, 2, 2)).','.hexdec(substr($hash, 4, 2));
	return $color;
	}
}
?>