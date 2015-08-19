<?php  // -*- coding: utf-8 -*-
define('TTF', '/tmp/shuowen.ttf'); //フォントファイル
define('OUTIMG', '/tmp/img'); //画像出力先dir

mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");

outPNG(OUTIMG, TTF, '幸', '496b-80');

/**
 * 文字画像出力
 **/
function outPNG($dir, $font, $text, $name) {
  $width = 80;
  $height = 90;
  $fontSize = 60;
  $fangle = 0;

  $img = imagecreate( $width, $height );

  $black = ImageColorAllocate( $img, 0x00, 0x00, 0x00 );
  $white = ImageColorAllocate( $img, 0xff, 0xff, 0xff );

  //imagealphablending($img, true);
  //imagesavealpha($img, true);

  ImageFilledRectangle( $img, 0, 0, $width, $height, $white );
  imagecolortransparent($img, $white);


  $box = imagettfbbox($fontSize, 0, $font, $text);
  $x = 0;
  $y = 70;
  imageTTFText($img, $fontSize, $fangle, $x, $y, $black, $font, $text);

  $filename = $dir . "/" . $name . ".png";
  imagepng($img, $filename);
  imagedestroy( $img );
}


?>
