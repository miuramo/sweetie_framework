<?php
require_once("_lib.php");
require_sweetielogin(); // require sweetie login for the user
if (isset($_GET['ak'])){
  $ak = filter_input(INPUT_GET, 'ak');
  $aa = serial2ary($ak);
  $dbfn = $aa['dbfn'];
  $table = $aa['table'];
} else {
  // ?table=TABLE
  $dbfn = filter_input(INPUT_GET, 'dbfn');
  $table = filter_input(INPUT_GET, 'table'); 
}
$db = dbopen($dbfn);
sanitize($_POST);
if (isset($_FILES) && count($_FILES)>0){
  $_POST = array_merge($_POST,$_FILES);
}
if (isset($_POST) && count($_POST)>0){
  foreach($_POST as $k=>&$v){
    if ($k=="id") {
      unset($_POST[$k]); continue;
    }
    if (preg_match("/pass/i", $k)){
      $_POST[$k] = password_hash( $_POST[$k] ,PASSWORD_BCRYPT);
    }
    if ($k=="dt" && strlen(trim($_POST[$k]))<1 ){
      $_POST[$k] = date("Y-m-d H:i:s");
    }
  }
  insert($db,$table,$_POST);
  //  echo "<script>alert('".$_SERVER["HTTP_REFERER"]."');</script>";
  header('Location: '.$_SERVER["HTTP_REFERER"]); //redirect 
  exit();
}
echo '<style> div.jqadd_'.$table.' {  border: 2px solid #fcc; background: #fee; padding: 7px 12px 0px;} </style>';
$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
//echo("This page's URL = ".$url);

$schema = sql($db,"pragma table_info({$table});"); //cid, name, type, notnull, dflt_value, pk
remove_blobinfo_schema($schema); // drop image_name, image_type, image_size
echo "Add to {$table}";
// echo "<form action=\"{$_SERVER['REQUEST_URI']}\" method=\"post\">";
form_start( ["enctype"=>"multipart/form-data", "action"=>$_SERVER['REQUEST_URI'] ] );
//echo $_SERVER['REQUEST_URI']."<br>";
foreach($schema as $k=>$v){
  if ($v['name']=="id") continue;
  if ($v['type']=="TEXT"){
    if ( preg_match('/pass/i', $v['name'])){
      echo $v['name']." :  <input type=\"password\" name=\"{$v['name']}\" size=30 value=\"{$v['dflt_value']}\"><br>";
    } else {
      echo $v['name']." : <input type=\"text\" name=\"{$v['name']}\" size=80 value=\"{$v['dflt_value']}\"><br>";
    }
  } else if ($v['type']=="DATETIME"){
    if ( $v['name']=="dt"){
      echo $v['name']." : <input type=\"datetime\" name=\"{$v['name']}\" size=40 value=\"\" placeholder=\"keep it blank to insert current datetime\"><br>";
    }
  } else if ($v['type']=="BLOB"){
    echo $v['name']." : <input type=\"file\" name=\"{$v['name']}\" size=40 value=\"\" ><br>";
  } else {
    echo $v['name']." : <input type=\"text\" name=\"{$v['name']}\" size=10 value=\"{$v['dflt_value']}\"><i style=\"color:gray;\">(integer)</i><br>";
  }
}
echo "<input type=\"submit\" value=\"Add to {$table}\">";
echo "</form>";