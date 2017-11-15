<?php
require_once("__lib.php");
@session_start(); 


$version = "v1";

$list = file_get_contents('http://sweetie.istlab.info/'.$version.'/list.txt');
echo $list;

$a = explode("\n", $list);
foreach($a as $n=>$f){
  if (strlen($f)<1) continue;
echo "<pre>";
echo $f;
echo "</pre>";
  $s = file_get_contents('http://sweetie.istlab.info/'.$version.'/'.$f.".txt");
  if (strlen($s)>7){
    $h = fopen($f,"w");
    if ($h !== null){
      fwrite($h, $s);
      echo "wrote ".$f."<br>";
    }
  } else {
    $ss = trim($s);
    if (startsWith($ss,"del") && file_exists($f)){
      unlink($f);
      echo "deleted ".$f."<br>";
    }
  }
}

?>
