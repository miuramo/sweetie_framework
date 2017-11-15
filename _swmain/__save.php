<?php
require_once("__lib.php");
@session_start(); 

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//echo getcwd();


if (strlen($_POST['code'])==0){
  unlink($_POST['file']);
} else {
  if (strpos($_POST['file'],"/")!==false){
    $ary = explode("/", $_POST['file']);
    foreach($ary as $n=>$f){
      if ($n == count($ary)-1) break; // last item = filename, not folder
      if (is_dir($f)){
	chdir($f);
      } else {
	if (mkdir($f)) chdir($f);
      }
    }
    $_POST['file'] = end($ary);
  }
  $h = fopen($_POST['file'],"w");
  if ($h !== null){
    fwrite($h, stripslashes($_POST['code']));
  }
  fclose($h);
}


