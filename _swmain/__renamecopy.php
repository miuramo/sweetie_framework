<?php
require_once("__lib.php");
@session_start(); 

$before = filter_input(INPUT_POST,"before");
$after = filter_input(INPUT_POST,"after");
$isfolder = filter_input(INPUT_POST,"isfolder");
$cmd = filter_input(INPUT_POST,"cmd");

$before = urldecode($before);
$after = urldecode($after);

// check after
$ary = explode("/",$after);
$ary = array_map("trim",$ary);
$newary = array();
foreach($ary as $n=>$f){
  if ($f == "..") continue;
  $newary[] = $f;
}
$newafter = implode("/",$newary);


/*if ($cmd=="delete_folder"){
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
  }*/
if ($cmd=="rename"){
  exec("mv \"{$before}\" \"{$newafter}\"");
} else if ($cmd=="copy"){
  exec("cp -rp \"{$before}\" \"{$newafter}\"");
}
