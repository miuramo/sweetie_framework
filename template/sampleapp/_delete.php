<?php
require_once("_lib.php");
require_sweetielogin(); // require sweetie login for the user
if (isset($_GET['ak'])){
  $ak = filter_input(INPUT_GET, 'ak');
  $aa = serial2ary($ak);
  $dbfn = $aa['dbfn'];
  $table = $aa['table'];
} else {
  $dbfn = filter_input(INPUT_GET, 'dbfn');
  $table = filter_input(INPUT_GET, 'table'); 
}
$id = filter_input(INPUT_GET, 'id');
if (is_numeric($id)){
  $db = dbopen($dbfn);
  delete($db,$table,$id);
  header('Location: '.$_SERVER["HTTP_REFERER"]); //redirect 
  exit();
}
