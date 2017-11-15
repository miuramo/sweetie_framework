<?php
// NOTE: size should be provided by GET method
$dbfn = "photo.db";
require_once("_lib.php");
$db = dbopen($dbfn);
sanitize($_GET);

$row = getrow($db, $_GET['table'],  $_GET['id']);
if ($_GET['size'] == $row[$_GET['field'].'_size']){
  if ( isset($row[$_GET['field']."_type"]) ){
    $mime = $row[$_GET['field']."_type"];
  } else {
    $finfo = new finfo(FILEINFO_MIME);
    $mime = $finfo->buffer($row[$_GET['field']]);    
  }
  if (preg_match("/^image/", $mime)){
    header("Content-Type: ".$mime);
    echo $row[$_GET['field']];
  } else {
    //    header("Content-Type: ". $mime);
    $img = ImageCreate(100,60);
    $bg = ImageColorAllocate($img, 0xf0, 0xf0, 0xf0);
    ImageFilledRectangle($img, 0,0, 100,100, $bg);
    $textcolor = imagecolorallocate($img, 0, 0, 255);
    $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
    // 左上に文字列を描画します
    imagettftext($img, 12, 0, 5, 20, $textcolor, $font, "not image");
    $ary = explode("/",$mime);
    imagettftext($img, 10, 0, 5, 40, $textcolor, $font, $ary[0]."/");
    imagettftext($img, 10, 0, 5, 55, $textcolor, $font, $ary[1]);
    header('Content-Type: image/png');
    ImagePNG($img);
  }
}
//$fp = $db->openBlob($_GET['table'], $_GET['field'], $_GET['id'] );
//    header("Content-Type: image/jpeg");
//    while(!feof($fp)) echo fgets($fp);
//    fclose($fp);

