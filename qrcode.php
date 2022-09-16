<?php

header("Content-type: image/png");

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

require_once('vendor/autoload.php');

$options = new QROptions(
  [
    'eccLevel' => QRCode::ECC_L,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'version' => 5,
	  'scale' => 20,
  ]
);

if (empty($_GET["url"])) {
	$qrcode = "images/info.png";
} else {
	$url = urldecode($_GET["url"]);
	$qrcode = (new QRCode($options))->render($url);
}

$src = imagecreatefrompng($qrcode);
$img = imagecreatefrompng('images/background.png');
imagecopy($img, $src, 350, 0, 0, 0, 900, 900);

imagepng($img);
imagedestroy($img);
imagedestroy($src);
?>