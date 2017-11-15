<?php
require_once("__lib.php");
@session_start(); 

$fn = filter_input(INPUT_GET,"dir");
$fn = trim($fn);
if (strpos($fn,"/")!==false){
  $ary = explode("/", $fn); // separate with slash
  $ary = array_map("trim",$ary);
  foreach($ary as $n=>$f){
    if ($f == "..") break;
    if (strlen($f)<1) break;
    if (is_dir($f)){
      chdir($f);
    } else {
      if (mkdir($f)) chdir($f);
    }
  }
} else {
  if ($fn != "..") 
    if (strlen($fn)>0)
      if (!is_dir($fn))
	mkdir($fn);
}

