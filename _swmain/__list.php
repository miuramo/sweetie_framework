<?php
require_once("__lib.php");
@session_start(); 

if (isset($_GET['dir'])){
   $realdir = $_GET['dir']."/";
   $udir = $_GET['dir']."___";
   $realdir = str_replace("___","/",$realdir);
   $dirlist = scandir($realdir);
} else {
  $dir = "";
  $dirlist = scandir(".");
}
$laterdir = array();
$filenumcounter = 0;
foreach($dirlist as $n=>$d){
  if (is_dir($realdir.$d)){
    if (startsWith($d,".")) continue;
    if ($dir==""){
      //      if ($d=="cm" || $d=="gl" || $d=="img" || $d=="jq" || $d=="mm" || $d=="pjs" || $d=="_sw" || $d=="_edit") continue;
      if ( $d=="img" || $d=="_edit") continue;
    }
    $laterdir[] = $d;
  } else {
    if (!startsWith($d,"img") 
        && ($d != ".") && !startsWith($d,"#") && !endsWith($d,".exe")
        && !endsWith($d,".ini") && !endsWith($d,".dll")
        && !endsWith($d,"~")){
      echo "<button id=\"{$udir}{$d}\" class=\"button\">{$realdir}{$d}</button>";
      $filenumcounter++;
    }
  }
}
foreach($laterdir as $n=>$d){
    echo "<button id=\"{$udir}{$d}\" data-isdir=\"dir\" class=\"button dirbutton\">[{$d}]</button>";
    echo "<span id=\"{$udir}{$d}_span\"></span>";
}
if ($filenumcounter>0) echo "<br>";
?>
