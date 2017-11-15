<?php
require_once("__lib.php");
@session_start(); 

chdir("img");

$imgstr = $_POST['request'];

if (!preg_match('/data:([^;]*);base64,(.*)/', $imgstr, $matches)) {
  die("error");
}
// Decode the data
$content = base64_decode($matches[2]);
$mime = $matches[1];

date_default_timezone_set('Asia/Tokyo');
$name = $_POST['fname']; // date('y_md_His');

if (!$handle = fopen($name,"w")){
  echo "Cannot open file ($name)";
  exit;
}
if (fwrite($handle, $content) === FALSE) {
  echo "Cannot write to file ($name)";
  exit;
}
echo $mime."\t".$name;
fclose($handle);

unset($_POST['request']);

