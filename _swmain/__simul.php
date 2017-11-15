<?php
require_once("__lib.php");
@session_start(); 

define("FILE","_simul.dat");
date_default_timezone_set("Asia/Tokyo");

function &init($file){
  $ary = array(); save($ary,$file); return $ary;
}
function save(&$ary, $file){
  $fh = fopen($file,"ab+");
  if (flock($fh, LOCK_EX)){
    ftruncate($fh,0);
    fwrite($fh, serialize($ary));
  }
  fclose($fh);
  //  flock($fh, LOCK_UN);
}
function &load($file){
  if (file_exists($file)){ $fh = fopen($file,"rb");
    if ($fh !== null) { 
       $fs = filesize($file);
       if ($fs > 0){
         $d = fread($fh,$fs);
         $ary = unserialize($d);
       } else { $ary =& init($file); }
    }
    fclose($fh);
  } else {
    $ary =& init($file);
  }
  return $ary;
}

$sid = $_GET['sid'];
$fn = $_GET['fn'];
$ary =& load("_simul_".$fn);
$tm = microtime(true)*1000;
//if (!isset($ary[$fn])){
//  $ary[$fn] = array();
//}
$ary[$tm] = array();
$ary[$tm][$sid] = array();
$ary[$tm][$sid]['op'] = $_POST['op'];
$ary[$tm][$sid]['cursor'] = $_POST['cursor'];

foreach($ary as $atm=>$aa){
  if ($tm - $atm > 8000){ unset($ary[$atm]); }
}
save($ary,"_simul_".$fn);

//echo $sid." ".$fn." ".$tm;
echo json_encode($ary);





