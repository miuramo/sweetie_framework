<?php 
$candidates = explode("/",$_SERVER["SCRIPT_NAME"]);
$basefolder = $candidates[count($candidates)-3];
define("BASEFOLDER",$basefolder);
# echo getcwd();
# echo $basefolder;
require_once("__sessionfunc.php");
require_logined_session();
# basicauth($basefolder,md5($basefolder."%1YaAc"),$basefolder);
chdir("../".$basefolder);
# define("BASEFOLDER",$basefolder);

/*
if (isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['REMOTE_ADDR'])){
  if ($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']){
    $ach = file_get_contents("accepthost.txt");
    $accepthost = explode("\n", $ach);
    $flag = true;
    foreach($accepthost as $n=>$h){
      $h = trim($h);
      if (startsWith($h,"#")) continue;
      if ($h==$_SERVER['REMOTE_ADDR']) { $flag = false; break; }
    }
    if ($flag){ 
      echo "<h1>This Page Access is Limited for localhost.<br>Your address is {$_SERVER['REMOTE_ADDR']}<br><br>Edit [ accepthost.txt ] by NotePad.</h1>";
      die;
    }
  }
}
*/

/*function auth(){
  if (startsWith($_SERVER['SERVER_SOFTWARE'],"Apache")){
  basicauth("user","pass","Please login (user)");
  } else {

  }
  }*/
function startsWith($haystack, $needle) {
  // search backwards starting from haystack length characters from the end
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
  // search forward starting from end minus needle length characters
  return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function basicauth($u,$p,$h){
  if (!isset($_SERVER["PHP_AUTH_USER"])){
    header("WWW-Authenticate: Basic realm=\"".$h."\"");
    //    header("WWW-Authenticate: Basic realm=\"Please Enter Your Pass ".$h."\"");
    header("HTTP/1.0 401 Unauthorized");
    echo "Authorization Required (1)";
    exit;
  } else {
    if ($_SERVER["PHP_AUTH_USER"]==$u && $_SERVER["PHP_AUTH_PW"]==$p ){

    } else {
      header("WWW-Authenticate: Basic realm=\"Please Enter Your Pass 2\"");
      header("HTTP/1.0 401 Unauthorized");
      echo "Authorization Required (2)";
      exit;
    }
  }
}

function get_inner_path_of_directory( $dir_path ){
  $file_array = array();
  if( is_dir( $dir_path ) ){
    if( $dh = opendir( $dir_path ) ){
      while( ( $file = readdir( $dh ) ) !== false ){
	if( $file == "." || $file == ".." ){
	  continue;
	}
	$file_array[] = $file;
      }
      closedir( $dh );
    }
  }
  sort( $file_array );
  return $file_array;
}

function add_zip( $zip, $dir_path, $new_dir ){
  if( ! is_dir( $new_dir ) ){
    $zip->addEmptyDir( $new_dir );
  }
  
  foreach( get_inner_path_of_directory( $dir_path ) as $file ){
    if( is_dir( $dir_path . "/" . $file ) ){
      add_zip( $zip, $dir_path . "/" . $file, $new_dir . "/" . $file );
    }
    else{
      $zip->addFile( $dir_path . "/" . $file, $new_dir . "/" . $file );
    }
  }
}

function sugulog($mes,$fn="sugulog.txt"){
  file_put_contents($fn,
		    date("[Y-m-d H:i:s] ")." {$mes}\n", FILE_APPEND);

}

function sugulogary($ary,$fn="sugulog.txt"){
  ob_start();
  var_dump($ary);
  $ret = ob_get_contents();
  ob_end_clean();
  sugulog( $ret , $fn );
}



function makezip($zipFN){
  $zip = new ZipArchive();
  $result = $zip->open($zipFN, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
  if ($result !== true) return false;

  $dirlist = scandir(".");
  foreach($dirlist as $n=>$d){
    if (strlen($d)>1 && !startsWith($d,".") ){
      if (is_dir($d)){
	if ($d != "_edit") add_zip( $zip, $d, $d); // do not include _edit
      } else if (!startsWith($d,"x__") && !startsWith($d,"x_") && !startsWith($d,"img") 
		 && !startsWith($d,".") && !startsWith($d,"#") && !endsWith($d,".exe")
		 && !endsWith($d,".ini") && !endsWith($d,".dll") && !endsWith($d,".zip")
		 && !endsWith($d,"index.phpx") && !endsWith($d,"~")){
	$zip->addFile($d);
      }
    }
  }
  $zip->close();

}
