<?php
require_once("__lib.php");
@session_start(); 

$fn = filter_input(INPUT_GET,"dir");
$fn = urldecode($fn);
$fn = trim($fn);

if (!file_exists($fn)) {
  if (strpos($fn,"/")!==false){
    $ary = explode("/", $fn);
    $ary = array_map("trim",$ary);
    foreach($ary as $n=>$f){
      if ($n == count($ary)-1) break; // last item = filename, not folder
      if ($f == "..") break;
      if (is_dir($f)){
	chdir($f);
      } else {
	if (mkdir($f)) chdir($f);
      }
    }
    $fn = end($ary);
  }
  if (strlen($fn)>0){
    $h = fopen($fn,"w");
    if ($h !== null){
      fwrite($h, "" );
    }
    fclose($h);
  }
}

