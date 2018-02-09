<?php
	session_start();

	header("Expires: Fri, 01 Jan 2016 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
    $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLNPQRSTUVXYZ-=';
    $captchaString = '';
     
    for ($i = 0; $i < 6; $i++) {
		$captchaString .= $chars[rand(0, strlen($chars)-1)];
    }	
	
    $im = imagecreatefrompng("../images/captcha.png");
    imagettftext($im, 30, 0, 10, 40, imagecolorallocate ($im, 255, 255, 255), '../css/fonts/fancy.ttf', $captchaString);
	
	$_SESSION['thecode'] = strtolower($captchaString);
     
    header ('Content-type: image/png');
    imagepng($im, NULL, 9);
    imagedestroy($im);
?>