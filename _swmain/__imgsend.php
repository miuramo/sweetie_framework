<?php
require_once("__lib.php");
@session_start(); 
chdir("img");

$imgstr = $_POST['request'];

// dataURL を分割して，データコンテンツとmimeタイプに分割
// http://stackoverflow.com/questions/6417720/create-display-image-from-dataurl
if (!preg_match('/data:([^;]*);base64,(.*)/', $imgstr, $matches)) {
  die("error");
}

// Decode the data
$content = base64_decode($matches[2]);

$mime = $matches[1];
//echo $mime."\n";
//echo strlen($content)."\n";


// ファイルに保存してみる
//$filename = "postdata";
//if ($mime == "image/png") $filename .= ".png";
//if ($mime == "image/jpeg") $filename .= ".jpg";

date_default_timezone_set('Asia/Tokyo');
$name = date('y_md_His');

if ($mime == "image/png") {
  $impng = imagecreatefrompng($imgstr);
  imagepng($impng,"".$name.".png");
  echo $name.".png";
  imagedestroy($impng);
 }

if ($mime == "image/jpeg") {
  $imjpeg = imagecreatefromjpeg($imgstr);
  imagejpeg($imjpeg,"".$name.".jpg");
  echo $name.".jpg";
  imagedestroy($imjpeg);
 }

/*if (!$handle = fopen($filename,"w")){
  echo "Cannot open file ($filename)";
  exit;
}
if (fwrite($handle, $content) === FALSE) {
  echo "Cannot write to file ($filename)";
  exit;
  }*/
//echo $filename;
//fclose($handle);

//画像サイズ表示
//list($width, $height, $type, $attr) = getimagesize($filename);
//echo "<img src=\"$filename\" $attr alt=\"getimagesize($filename)\" />";

unset($_POST['request']);
//echo nl2br(print_r($_POST,true)); //変数$_POST の中身を表示

//自作start
//$fh = fopen("linenum.txt","rb");
//if ($fh !== null){
//   $d = fread($fh, filesize("linenum.txt"));
//   $ary = unserialize($d);
//}
//fclose($fh);

//$ary []= __LINE__;
//$ary [$line]= $matches[2]; 
//$fh = fopen("linenum.txt","w");
//if($fh!==null){
//  fwrite($fh,serialize($ary));
//  echo "行番号保存しました";
// }
//fclose($fh);

//自作end

// Output the correct HTTP headers (may add more if you require them)
//header('Content-Type: '.$matches[1]);
//header('Content-Length: '.strlen($content));

// Output the actual image data
//echo $content;
die;

?>
