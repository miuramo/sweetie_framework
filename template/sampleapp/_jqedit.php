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
$id = filter_input(INPUT_GET, 'id');
$db = dbopen($dbfn);
sanitize($_POST);
if (isset($_FILES) && count($_FILES)>0){
  $_POST = array_merge($_POST,$_FILES);
}
if (isset($_POST) && count($_POST)>0){
  foreach($_POST as $k=>&$v){
    //    if ($k=="id") {
    //      unset($_POST[$k]); continue;
    //    }
    //    $_POST[$k] = filter_input(INPUT_POST, $k, FILTER_SANITIZE_MAGIC_QUOTES);
    if (preg_match("/pass/i", $k)){
      if (strlen(trim($_POST[$k]))<1) {
        unset($_POST[$k]); 
      } else{
        $_POST[$k] = password_hash( $_POST[$k] ,PASSWORD_BCRYPT);
      }
    }
    if ($k=="dt" && strlen(trim($_POST[$k]))<1 ){
      $_POST[$k] = date("Y-m-d H:i:s");
    }
  }
  update($db,$table,$_POST);
  header('Location: '.$_SERVER["HTTP_REFERER"]); //redirect 
  exit();
}
echo '<style> div.jqadd_'.$table.' {  border: 2px solid #cfc; background: #efe; padding: 7px 12px 0px;} </style>';
//$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
//echo("This page's URL = ".$url);

$schema = sql($db,"pragma table_info({$table});"); //cid, name, type, notnull, dflt_value, pk
remove_blobinfo_schema($schema); // drop image_name, image_type, image_size
$row = getrow($db,$table,$id);
echo "Edit {$table}";
//echo "<form action=\"{$_SERVER['REQUEST_URI']}\" method=\"post\">";
form_start( ["action"=>$_SERVER['REQUEST_URI'], "enctype"=>"multipart/form-data"] );
foreach($schema as $k=>$v){
  $kn = $v['name'];
  if ($kn=="id") {
    form_input("id", ["type"=>"hidden", "value"=>$row[$kn] ] );
  } else if ($v['type']=="TEXT"){
    if ( preg_match('/pass/i', $kn)){
      echo $kn." :  <input type=\"password\" name=\"{$kn}\" size=30 value=\"\" placeholder=\"keep it blank to unchange\"><br>\n";
    } else {
      echo $kn." : <input type=\"text\" name=\"{$kn}\" size=80 value=\"{$row[$kn]}\"><br>\n";
    }
  } else if ($v['type']=="DATETIME"){
    if ( $kn=="dt"){
      echo $v['name']." : <input type=\"datetime\" name=\"{$kn}\" size=40 value=\"{$row[$kn]}\" placeholder=\"keep it blank to update current datetime\"><br>\n";
    }
  } else if ($v['type']=="BLOB"){
    echo $v['name']." : <input type=\"file\" name=\"{$v['name']}\" size=40 value=\"\" ><br>\n";
  } else {
    echo $kn." : <input type=\"text\" name=\"{$v['name']}\" size=10 value=\"{$row[$kn]}\"><i style=\"color:gray;\">(integer)</i><br>\n";
  }
}
echo "<input type=\"submit\" value=\"Update\">";
echo "</form>";