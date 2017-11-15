<?php
require_once("__lib.php");
@session_start(); 

$dir = filter_input(INPUT_POST,"dir");
$cmd = filter_input(INPUT_POST,"cmd");

$dir = urldecode($dir);

if ($cmd=="delete_folder"){
  $dir = str_replace("[","",$dir);
  $dir = str_replace("]","",$dir);
  // 最後に/ がついていたら，削除する
  if (endsWith($dir,"/")){
    $dir = substr($dir,0,-1);
  }
}
if (strpos($dir,"/")!==false){
  $ary = explode("/", $dir);
  $ary = array_map("trim",$ary);
  foreach($ary as $n=>$f){
    if ($n == count($ary)-1) break; // last item = filename, not folder
    if ($f == "..") break;
    if (is_dir($f)){
      chdir($f);
    } else {
    }
  }
  $fn = end($ary);
} else {
  $fn = $dir;
}
//sugulogary($fn);
if (strlen($fn)>0){
  if (is_dir($fn)){
    exec("rm -fr \"{$fn}\"");
  } else {
    if (is_file($fn)){
      exec("rm \"{$fn}\"");
    } else {
      "{$fn} is not dir and file";
    }
  }
}

