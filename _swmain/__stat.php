<?php
require_once("__lib.php");
@session_start(); 

$dirlist = scandir(".");
$val = array();
//$keys = array("size","atime","mtime","ctime");
foreach($dirlist as $n=>$d){
  if (strlen($d)>2 ){
    // 除外ファイル条件
    if (!startsWith($d,"__") && !startsWith($d,"__") && !startsWith($d,"img")
    && !startsWith($d,".") && !endsWith($d,".exe")
    && !endsWith($d,".ini") && !endsWith($d,".dll")
    && !endsWith($d,".php") && !endsWith($d,"~")){
      $stat = stat($d);
      $val[$d] = $stat['mtime'];
    }
  }
}
echo json_encode($val);
?>
