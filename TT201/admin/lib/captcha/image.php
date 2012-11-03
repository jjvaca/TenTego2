<?php 

$font = './font.ttf'; 
$lineCount = 40; 
$fontSize = 21; 
$height = 32; 
$width = 120; 
$img_handle = imagecreate ($width, $height) or die ("Nie mozna utworzyc obrazka"); 
$backColor = imagecolorallocate($img_handle, 255, 255, 255); 
$lineColor = imagecolorallocate($img_handle, 175, 238, 238); 
$txtColor = imagecolorallocate($img_handle, 135, 206, 235); 

$string = "abcdefghijklmnopqrstuvwxyz0123456789"; 
$str = NULL;
for($i=0;$i<6;$i++){ 
    $pos = rand(0,36); 
    $str .= @$string{$pos}; 
} 
$textbox = imagettfbbox($fontSize, 0, $font, $str) or die('Błąd funkcji imagettfbbox'); 
$x = ($width - $textbox[4])/2; 
$y = ($height - $textbox[5])/2; 
imagettftext($img_handle, $fontSize, 0, $x, $y, $txtColor, $font , $str) or die('Błąd funkcji imagettftext'); 
for($i=0;$i<$lineCount;$i++){ 
    $x1 = rand(0,$width);$x2 = rand(0,$width); 
    $y1 = rand(0,$width);$y2 = rand(0,$width); 
    imageline($img_handle,$x1,$y1,$x2,$y2,$lineColor); 
} 
header('Content-Type: image/jpeg'); 
imagejpeg($img_handle,NULL,100); 
imagedestroy($img_handle); 

session_start(); 
$_SESSION['img_number'] = $str; 

?>