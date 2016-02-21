<?php  // -*- coding: utf-8 -*-
mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");
define('TTF', '/tmp/shuowen.ttf'); //フォントファイル
define('OUTIMG', '/tmp/img'); //画像出力先dir

function helpExit() {
  print <<<HD1

  -h : help
  -f <filename> : 説文解字注xmlファイルの指定。default=stdin
  -c : CSV形式で出力。default
  -s : SQL文で出力。
  -p : 文字画像PNG出力
  -o <directory> : 文字画像PNG出力先directory
  -t <filename> : ttfファイルの指定。default=./shuowen.ttf


HD1;
  exit;
}

/**
 * swjz.xmlを変更。処理しやすいように
 */
function modify(&$file, &$temp)
{
    $chap = 0;
    while ($line = fgets($file) )
    {
        if ($chap == 0) {
            $a = preg_match('/<chapter>/', $line, $matches);
            if ($a) { $chap=1; }
        }
        if ($chap != 0) {
            $line = mb_ereg_replace('<!--<', '（', $line); //parse errorを回避
            $line = mb_ereg_replace('>-->', '）', $line);
            $line = mb_ereg_replace('<!--', '（', $line); //文中のコメントを得られるように。全角まるカッコ
            $line = mb_ereg_replace('-->', '）', $line);
        }

        // <wordhead> 単位で取れるように
        $line = mb_ereg_replace('<\s*wordhead\s+id', "</one><one><wordhead id", $line);
        $line = mb_ereg_replace('<\s*shuowen\s*>', "<shuowen><one>", $line);
        $line = mb_ereg_replace('</\s*shuowen\s*>', "</one></shuowen>", $line);

        // echo $line;
        fwrite($temp, $line);
    }
}

/**
 chapter titleを出力
 */
function outChap($mode, $chapid, $title) {
  if ($mode == "s") {
    print <<<HD2
INSERT INTO kaiji_title (chapter, title) VALUES ('$chapid', '$title');

HD2;
  }
}

/**
 * 本文を出力
 */
function outBun($mode, $chap, $id, $img, $c, $parts, $bun) {
  if ($mode == "s") {
    print <<<HD3
INSERT INTO kaiji_moji (chapter, id, img, c, parts) VALUES ('$chap', '$id', '$img', '$c', '$parts');
INSERT INTO kaiji_chu (id, bun) VALUES ('$id', '$bun');

HD3;
  } else {
    print <<<HD4
"$chap","$id","$img","$c","$parts","$bun"

HD4;
  }
}

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

//*****************************************
//*****************************************
$options = getopt("f:schpt:o:");

if ( strlen($options['f']) > 0 ) {
  $stdin = fopen($options['f'], "r");
}
else {
  $stdin = fopen("php://stdin", "r");
}

$mode = "c";
if ( array_key_exists('s', $options) ) { $mode = "s"; }
if ( array_key_exists('c', $options) ) { $mode = "c"; }

$outPNG = 0; $fontfile = TTF; $pngOutDir = OUTIMG;
if ( array_key_exists('p', $options) ) { $outPNG = 1; }
if ( array_key_exists('t', $options) ) { $outPNG = 1;
  $fontfile = $options['t'];
}
if ( array_key_exists('o', $options) ) { $outPNG = 1;
  $pngOutDir = $options['o'];
}

if ( array_key_exists('h', $options) ) {
    helpExit();
}


$tmpfname = tempnam("/tmp", "FOO");
$temp = fopen($tmpfname, "w");
modify($stdin, $temp);
fclose($temp);


$xml = simplexml_load_file($tmpfname);
foreach ( $xml->volumes->chapter as $chapter ) {
  $chapid = $chapter->chaptertitle['id'];
  $title = $chapter->chaptertitle;
  if ($title->duan_note) { $title = trim($title) . " " . trim($title->duan_note); }
  if ($chapid == "v29") { break; }
  outChap($mode, $chapid, trim($title));

  foreach ($chapter->shuowen as $shuo) {
    foreach ($shuo->one as $one) {
      if ( count( $one->wordhead ) < 1 ) { continue; }
      $id = trim($one->wordhead['id']);
      $img= trim($one->wordhead['img']);

      $ch = mb_ereg_replace( '（.*）', '', trim($one->wordhead) ); // 文字のパーツは全角まるカッコでくくられている
      $parts = mb_ereg_replace( "$ch", '', trim($one->wordhead) );
      $parts = mb_ereg_replace( "[（）]", '', $parts);

      $bun="";
      for ($i=0; $i<count($one->explanation); $i++) {
          $bun = $bun . "【" . trim($one->explanation[$i]) . "】\n";
          $bun = $bun . trim($one->duan_note[$i]) . "\n";
      }

      outBun($mode, $chapid, $id, $img, $ch, $parts, trim($bun) );

      if ($outPNG > 0) { outPNG($pngOutDir, $fontfile, $ch, $img); }
//      echo "$pngOutDir, $fontfile, $ch, $img \n";
//exit;
    }
  }
}

?>
