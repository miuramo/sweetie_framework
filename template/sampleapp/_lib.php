<?php
//date_default_timezone_set('Asia/Kuala_Lumpur'); //GMT+8
date_default_timezone_set('Asia/Tokyo'); //GMT+9

// show errors for debug
ini_set('display_errors',1);  error_reporting(E_ALL);

define("SERIALKEY","R@nd0m1Ch@nGeTHis"); // change this!

/**  DO NOT CHANGE BELOW **/
define("SWEETIE_USERNAME_POS_FROM_DOCROOT", 2);  //  /16/[your username]
$fullurl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
$baseurl = get_sweetie_baseurl(); // ends with your username


function sanitize(&$ary){ // prevent SQL injection
  foreach($ary as $k=>$v){
    //    $ary[$k] = filter_input(INPUT_POST, $k, FILTER_SANITIZE_MAGIC_QUOTES);
    //    $ary[$k] = filter_input(INPUT_POST, $k, FILTER_SANITIZE_STRING);
    $ary[$k] = addslashes($ary[$k]);
    $ary[$k] = htmlspecialchars($ary[$k]); 
  }
}

/**
  DB
*/
function dbopen($dbfn){
  try {
    $pdo = new PDO('sqlite:../phpliteadmin/'.$dbfn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}
// returns multiple rows 
function sql($pdo, $sql){
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ret = $stmt->fetchAll();
    return $ret;
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}
// returns single row
function getrow($pdo, $table, $id){
  if (is_numeric($id)){
    $item = sql($pdo, "select * from {$table} where id = {$id}");
    if (count($item)==1){
      return $item[0];
    } else {
      echo "<div style=\"color:red;\">getrow Error: multiple rows matched by id={$id}</div>\n";
    }
  } else {
    echo "<div style=\"color:red;\">getrow Error: ID is not numeric: {$id}</div>\n";
    if (strlen($id)<1) echo "<div style=\"color:red;\">Maybe ID is empty string</div>\n";
  }
}
// drop db if no data (to reset autoincrement id)
function dropdb_nodata($pdo, $table){
  $r = sql($pdo, "select id from {$table}");
  if (count($r)<1){
    $pdo->exec("drop table if exists {$table}");
  }
}

// special insert for register to users : 
function adduser($pdo,$ary, $table="users", $userfield="name", $passfield="hashpass"){
  if (isset($ary[$passfield])){
    $ary[$passfield] = password_hash($ary[$passfield] ,PASSWORD_BCRYPT);
  } else {
    $ary[$passfield] = password_hash($ary[$userfield] ,PASSWORD_BCRYPT);
  }
  return insert($pdo,$table,$ary);
}

// insert
function insert($pdo,$table,&$ary){
  try {
    $schema = sql($pdo,"pragma table_info({$table})"); //cid, name, type, notnull, dflt_value, pk
    $fields_on_DB = arytohash($schema, "name","type");
    $tobeadded = array();
    foreach($ary as $k=>$v){
      if ($k=="id") continue; //  if (isset($ary['id'])) unset($ary['id']); // drop ID before insert
      if (isset($fields_on_DB[$k])){
        $tobeadded[$k] = $v;
      } else {
        echo "insert error: field name {$k} does not exist in table {$table}<br>\n";
      }
    }
    // Special rule for dt (DATETIME)
    if (isset($fields_on_DB['dt']) && $fields_on_DB['dt']=="DATETIME"){
      if (!isset($tobeadded['dt'])) $tobeadded['dt'] = date("Y-m-d H:i:s"); //date_default_timezone_set('Asia/Tokyo');
    }
    // Special rule for File
    foreach($tobeadded as $k=>&$v){
      if (is_array($v)){
        if ($fields_on_DB[$k] == "BLOB"){
          $tobeadded[$k] = file_get_contents($ary[$k]["tmp_name"]);
          if (isset($fields_on_DB[$k."_name"])) $tobeadded[$k."_name"] = $ary[$k]["name"];
          if (isset($fields_on_DB[$k."_type"])) $tobeadded[$k."_type"] = $ary[$k]["type"];
          if (isset($fields_on_DB[$k."_size"])) $tobeadded[$k."_size"] = $ary[$k]["size"];
        }
      }
    }

    $insertfields = array_keys($tobeadded);
    $insertfieldswithcolon = array_map(function($str){ return ":".$str; } , $insertfields);
    $sql = "insert into {$table} (".implode(", ",$insertfields).") values (".implode(", ",$insertfieldswithcolon).")"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute($tobeadded);
    return $pdo->lastInsertId();
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}
// add is almost same as insert
/*function add($pdo,$table,$ary){
  try {
    $schema = sql($pdo,"pragma table_info({$table})"); //cid, name, type, notnull, dflt_value, pk
    $fields_on_DB = arytohash($schema, "name","type");
    //    pr($fields_on_DB);
    $tobeadded = array();
    foreach($ary as $k=>$v){
      if (isset($fields_on_DB[$k])){
        $tobeadded[$k] = $v;
      } else {
        echo "add error: field name {$k} does not exist in table {$table}<br>";
      }
    }
    //    pr($tobeadded);
    $insertfields = array_keys($tobeadded);
    $insertfieldswithcolon = array_map(function($str){ return ":".$str; } , $insertfields);
    //  pr($insertfields);
    //  pr($insertfieldswithcolon);
    $sql = "insert into {$table} (".implode(", ",$insertfields).") values (".implode(", ",$insertfieldswithcolon).")"; 
    //pr($sql);  
    $stmt = $pdo->prepare($sql);
    foreach($tobeadded as $k=>$v){
      if ($fields_on_DB[$k]=='INTEGER'){
        $stmt->bindValue(":".$k , $tobeadded[$k] , PDO::PARAM_INT);
      } else {
        $stmt->bindParam(":".$k , $tobeadded[$k]  , PDO::PARAM_STR);
      }
    }
    $stmt->execute();
    return $pdo->lastInsertId();
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}*/
function delete($pdo,$table,$id){
  if (is_numeric($id)){
    $sql = "delete from {$table} where id = :id limit 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
  }
}
// update one row by id
function update($pdo, $table, $ary){
  $id = $ary["id"];
  if (is_numeric($id)){
    unset($ary["id"]);
    try {
      $schema = sql($pdo,"pragma table_info({$table})"); //cid, name, type, notnull, dflt_value, pk
      $fields_on_DB = arytohash($schema, "name","type");
      $tobeadded = array();
      $fields = array();
      foreach($ary as $k=>$v){
        if (isset($fields_on_DB[$k])){
          $tobeadded[$k] = $v;
          $fields[$k] = $k." = :".$k;
        } else {
          echo "update error: field name {$k} does not exist in table {$table}<br>";
        }
      }
      // Special rule for File
      foreach($tobeadded as $k=>&$v){
        if (is_array($v)){
          if ($fields_on_DB[$k] == "BLOB"){
            if (strlen($ary[$k]["tmp_name"])>0){
              $fp[$k] = fopen($ary[$k]["tmp_name"], 'rb');
              $tobeadded[$k] = $ary[$k]["tmp_name"]; //no meaning...not used
              $fields[$k] = $k." = :".$k;
              $suffix = array("name","type","size");
              foreach($suffix as $sk=>$sv){
                if (isset($fields_on_DB[$k.'_'.$sv])) {
                  $tobeadded[$k.'_'.$sv] = $ary[$k][$sv];
                  $fields[$k.'_'.$sv] = $k.'_'.$sv." = :".$k.'_'.$sv;
                }
              }
            } else {
              // no length (file was not selected)
              unset($tobeadded[$k]);
              unset($fields[$k]);
            }
          }
        }
      }
      $sql = "update `{$table}` set ".implode(", ",$fields)." where `id` = :id"; 
      //       pr($tobeadded);
      $stmt = $pdo->prepare($sql);
      foreach($tobeadded as $k=>&$v){ //why &$v works, not $v?
        if ($fields_on_DB[$k]=='INTEGER'){
          $stmt->bindParam(":".$k , $v , PDO::PARAM_INT);
        } else if ($fields_on_DB[$k]=='BLOB') {
          $stmt->bindParam(":".$k , $fp[$k]  , PDO::PARAM_LOB);
        } else {
          $stmt->bindValue(":".$k , $v  , PDO::PARAM_STR);
        }
      }
      $stmt->bindValue(":id" , $id , PDO::PARAM_INT);
      $pdo->beginTransaction();
      $stmt->execute() or die(print_r($stmt->errorInfo(), true));
      $pdo->commit();
      //      echo "<pre>";
      //      $stmt->debugDumpParams();
      //      echo "</pre>";
      return $pdo->lastInsertId();
    } catch (Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
  }
}

// get all table data as array
function tbl($pdo, $table){
  $schema = sql($pdo,"pragma table_info({$table})"); //cid, name, type, notnull, dflt_value, pk
  $fields_on_DB = arytohash($schema, "name","type");
  foreach($fields_on_DB as $k=>$v){
    if ($v=="BLOB") {
      unset($fields_on_DB[$k]); //remove BLOB
    }
  }
  $fields = implode(",",array_keys($fields_on_DB));
  return sql($pdo, "select {$fields} from {$table}");
}
// drop image_name, image_type, image_size
function remove_blobinfo_schema(&$schema){
  $toberemoved = array();
  $fields_on_DB = arytohash($schema, "name","type");
  foreach($schema as $k=>$v){
    if ($v['type']=="BLOB") {
      $toberemoved []= $v['name']."_name"; 
      $toberemoved []= $v['name']."_type"; 
      $toberemoved []= $v['name']."_size"; 
    }
  }
  foreach($schema as $k=>$v){
    if (in_array($v['name'], $toberemoved)){
      unset($schema[$k]);
    }
  }  
}
/**
 HTML Table and debug output
 */
function showtable($pdo, $table){
  table( tbl($pdo,$table) );
}
function showtable_withdeledit($pdo, $table,$dbfn){
  $tb = tbl($pdo,$table);
  $ak = getaccesskey($dbfn,$table);
  aryinscol($tb, "<a href='_delete.php?ak={$ak}&id={\$id}'>Delete</a>","delete");
  aryinscol($tb, "<a href='#{$table}{\$id}' onclick='jqedit(\"{$ak}\",{\$id},\"{$table}\");'>Edit</a>","edit");
//  aryinscol($tb, "<a href='_delete.php?table={$table}&id={\$id}&dbfn={$dbfn}'>Delete</a>","delete");
//  aryinscol($tb, "<a href='#{$table}{\$id}' onclick='jqedit2(\"{$table}\",{\$id},\"{$dbfn}\");'>Edit</a>","edit");
  table( $tb );
}
function table($ret){
  if (count($ret)==0){
    echo "<div style=\"color:red;\">No table data</div>\n";
    return;
  }
  echo "<table border=1 style=\"margin: 4px 0px;\">\n";
  foreach($ret as $k=>$row){
    if ($k == 0){ //print table headers
      echo "<tr>";
      foreach($row as $kk => $vv){
        echo "<th style=\"background: #eee;\">".$kk."</th>";
      }
      echo "</tr>\n";
    }
    echo "<tr>";
    foreach($row as $kk => $vv){
      echo "<td>".$vv."</td>";   //print table data
    }
    echo "</tr>\n";
  }
  echo "</table>\n";
}

/** print recursive for debug */
function pr($ret){
  echo "<pre>";
  nl2br( print_r($ret)  );
  echo "</pre>";
}

/**
  Array
*/
// construct one pair of data from array
function arytohash($ary,$key,$val){
  $ret = array();
  foreach($ary as $k=>$v){
    $ret[ $v[$key] ] = $v[$val];
  }
  return $ret;
}
function prependstrtohash(&$hash1, $str){
  foreach($hash1 as $k=>&$v){
    $v = $str.$v;
  }
}
function addstrtohash(&$hash1, $str){ appendstrtohash($hash1, $str); }
function appendstrtohash(&$hash1, $str){
  foreach($hash1 as $k=>&$v){
    $v = $v.$str;
  }
}
function mergehash(&$hash1, $hash2){
  foreach($hash1 as $k=>&$v){
    $v = $v.$hash2[$k];
  }
}

// insert column into array, with specified message
// NOTE: ONLY REPLACE $id IN message
function aryinscol(&$ary, $mes, $th="action", $pos=-1){
  foreach($ary as $nn=>&$r){
    $m = $mes;
    foreach($r as $k=>$v){
      //    $m = preg_replace('/\{\$id\}/', $r['id'], $mes);
      $m = preg_replace('/\{\$'.$k.'\}/', $v, $m);
    }
    //    eval("\$r[\$th] = \"{$mes}\";");
    $tobeins = array() ; $tobeins[$th]=$m;
    $r = aryinsertpos($r,$tobeins,$pos);
  }
  return $ary; // it is not necessary because aryinscol replace argument array
}
function aryinsertpos(&$ary, $tobeins, $pos=-1){
  if ($pos == -1){
    foreach($tobeins as $k=>$v){
      $ary[$k] = $v;
    }
  } else {
    $beforekey = array_keys($ary);
    //    pr($beforekey);
    for($i=0;$i<count($ary)-$pos;$i++){
      array_pop($beforekey);
    }
    //    pr($beforekey);
    $afterkey = array_keys($ary);
    for($i=0;$i<$pos;$i++){
      array_shift($afterkey);
    }
    //    pr("afterkey"); pr($afterkey);
    $tmp = $ary;
    $ary = array();
    foreach($beforekey as $n=>$k){
      $ary[$k] = $tmp[$k];
    }
    foreach($tobeins as $k=>$v){
      $ary[$k] = $v;
    }
    foreach($afterkey as $n=>$k){
      $ary[$k] = $tmp[$k];
    }
  }
  return $ary;
}

// delete column 
function arydelcol(&$ary, $key){
  foreach($ary as $nn=>&$r){
    unset($r[$key]);
  }
  return $ary;// it is not necessary because arydelcol replace argument array
}
// apply function to modify particular data
function arymapcol(&$ary, $f, $func){
  foreach($ary as $nn=>&$r){
    $r[$f] = $func($r[$f]);
  }
  return $ary;// it is not necessary because arymapcol replace argument array
}

/**
  Sweetie
*/
function get_sweetie_username(){
  $candidates = explode("/",$_SERVER["SCRIPT_NAME"]);
  $username = $candidates[SWEETIE_USERNAME_POS_FROM_DOCROOT];
  return $username;
}
function get_sweetie_parentpath(){ // begins and ends with slash
  $username = get_sweetie_username();
  $url = $_SERVER['REQUEST_URI'];
  $pos = strpos($url, $username);
  $parent = substr($url,0,$pos); // (https://upm.kitaq.link)/16/ 
  return $parent;
}
function get_sweetie_baseurl(){ // ends with slash
  $parent = get_sweetie_parentpath();
  $username = get_sweetie_username();
  return $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$parent.$username."/";
}
function get_fullurl(){
  return $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
}
function require_sweetielogin(){
  $username = get_sweetie_username();
  session_name("__".$username);
  @session_start();
  if (!isset($_SESSION['username'])){
    //    $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
    $parent = get_sweetie_parentpath(); // (https://upm.kitaq.link)/16/ 
    header('Location: '.$parent.$username.'/_edit/login.php');
    exit;
  }
}
/**
  Login
  */

// user table,  username field and password field
function require_login($dbfn, $table="users", $userfield="name", $passfield="hashpass"){
  session_name("_shop_session");
  @session_start();
  if (!isset($_SESSION['user'])){
    show_login_form($dbfn, $table, $userfield,$passfield);
    exit();
  } else {
    show_login_info();
  }
}
function checkuser($pdo, $table, $userfield="name", $passfield="hashpass", $user, $pass){
  $item = sql($pdo, "select * from {$table} where {$userfield} = '{$user}'");
  if (count($item)==1){
    if (password_verify($pass, $item[0][$passfield])){
      return $item[0]; // success!
    }
  }
  return false;
}
function addtmpuser($pdo,$ary, $table="users", $userfield="email", $passfield="hashpass"){
  if (isset($ary[$passfield])){
    $ary[$passfield] = password_hash($ary[$passfield] ,PASSWORD_BCRYPT);
  } else {
    $ary[$passfield] = password_hash($ary[$userfield] ,PASSWORD_BCRYPT);
  }
  return insert($pdo,$table,$ary);
}
function show_login_form($dbfn, $table, $userfield="name", $passfield="hashpass", $emailfield = "email"){

  if (isset($_GET['ak'])){ // from activation link
    $aary = serial2ary($_GET['ak']);
    $email = $aary['email']; $tmphash = $aary['tmphash'];
    $db = dbopen($dbfn);
    $ur = sql($db, "select * from {$table} where {$userfield} = '{$email}' and {$emailfield} = '{$email}' and {$passfield} = '{$tmphash}' limit 1");
    if (count($ur)==1){
      sql($db, "delete from {$table} where {$userfield} = '{$email}' and {$emailfield} = '{$email}' and id <> {$ur[0]['id']}");
      if (isset($_POST['tmphash'])){ // from registration button 
        sanitize($_POST);
        unset($_POST['tmphash']);
        // duplicate name check
        $dup = sql($db, "select * from {$table} where {$userfield} = '{$_POST[$userfield]}'");
        if (count($dup)>0) {
          form_start(["style"=>"background: #cfc; border: 3px solid #9cc; padding: 10px;"] );
          heading("The name is already used. Please choose other name.");
          span("Register (3/4)",["style"=>"font-size: 30px;"]); 
          nbsp(3); // spaces
          echo "User : ";
          form_input($userfield, ["type"=>"text", "size"=>20, "placeholder"=>"input user", "value"=>""]);
          echo "Pass : ";
          form_input($passfield, ["type"=>"password", "size"=>20, "placeholder"=>"input pass", "value"=>""]);
          form_input("tmphash", ["type"=>"hidden", "value"=>$tmphash]);
          form_input("id", ["type"=>"hidden", "value"=>$ur[0]['id'] ]);
          form_submit( ["value"=>"Register"] );
          form_end();
          exit();
        }
        $rawpass = $_POST[$passfield];
        $_POST[$passfield] = password_hash( $rawpass ,PASSWORD_BCRYPT);
        update($db, $table, $_POST);
        $u = checkuser($db,$table,$userfield,$passfield,$_POST[$userfield],$rawpass );
        @session_regenerate_id(true);
        $_SESSION['user'] = $_POST[$userfield];
        $_SESSION['u'] = $u; // user info
        // strip get parameters
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        header('Location: '.$uri);
        exit();
      } else {  // from activation link
        form_start(["style"=>"background: #cfc; border: 3px solid #9cc; padding: 10px;"] );
        heading("Email accepted");
        span("Register (3/4)",["style"=>"font-size: 30px;"]); 
        nbsp(3); // spaces
        echo "User : ";
        form_input($userfield, ["type"=>"text", "size"=>20, "placeholder"=>"input user", "value"=>""]);
        echo "Pass : ";
        form_input($passfield, ["type"=>"password", "size"=>20, "placeholder"=>"input pass", "value"=>""]);
        form_input("tmphash", ["type"=>"hidden", "value"=>$tmphash]);
        form_input("id", ["type"=>"hidden", "value"=>$ur[0]['id'] ]);
        form_submit( ["value"=>"Register"] );
        form_end();
      }
    }
    exit();
  }
  if (isset($_POST['email'])){
    sanitize($_POST); // < > & ' " special characters cannot be used for raw password string 
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)){
      //      $ary = [ "dbfn"=>$dbfn, "table"=>$table, "userfield"=>$userfield, "passfield"=>$passfield, "emailfield"=>$emailfield ];
      $aary = array();
      $aary[ 'email' ] = $email;
      $tmppass = date(DATE_RSS).$email;
      $tmphash = password_hash( $tmppass ,PASSWORD_BCRYPT);
      $aary[ 'tmphash' ] = $tmphash;
      //      $aary[ 'dt' ] = date(DATE_RSS);
      $db = dbopen($dbfn);
      $uary = [ "name"=>$email, "email"=>$email, "hashpass"=>$tmphash ];
      insert($db, $table, $uary);
      //      pr($aary);
      $serial = ary2serial($aary);
      sendMail($email , // To: address
               "Press link below to proceed registration", // Subject
               "Dear Guest User,\n\nPlase click the link to proceed registration.\n\n".get_fullurl()."?ak=".$serial.
               "\n\n(Please ignore if you do not know why this email is arrived.)", // Body
               "miuramo@mns.kyutech.ac.jp", //From address
               "my Web App MailSystem"); //FromName
      heading("(2/4) Sent activation link to {$email}. <br>Please check email.");
    } else {
      heading("(2/4) Error: Your address {$email} is not valid.");
    }
  }
  if (isset($_POST['userfield']) && isset($_POST['passfield'])){
    sanitize($_POST); // < > & ' " special characters cannot be used for raw password string 
    $uf = $_POST['userfield'];
    $pf = $_POST['passfield'];
    $db = dbopen($dbfn);
    if ($u = checkuser($db,$table,$uf,$pf,$_POST[$uf],$_POST[$pf])){
      @session_regenerate_id(true);
      $_SESSION['user'] = $_POST[$uf];
      $_SESSION['u'] = $u; // user info
      header('Location: '.$_SERVER['REQUEST_URI']);
      exit();
    }
  }
  //  pr($_POST);
  form_start( ["style"=>"background: #cff; border: 3px solid #9cc; padding: 10px;"] );
  span("Login",["style"=>"font-size: 30px;"]); 
  nbsp(3); // spaces
  echo "User : ";
  form_input($userfield, ["type"=>"text", "size"=>20, "placeholder"=>"input user", "value"=>""]);
  echo "Pass : ";
  form_input($passfield, ["type"=>"password", "size"=>20, "placeholder"=>"input pass", "value"=>""]);
  form_input("passfield", ["type"=>"hidden", "value"=>$passfield]);
  form_input("userfield", ["type"=>"hidden", "value"=>$userfield]);
  form_submit( ["value"=>"Login"] );
  showqrcode();
  form_end();
  // register 1/3
  form_start(["style"=>"background: #ffc; border: 3px solid #9cc; padding: 10px;"] );
  span("Register (1/4)",["style"=>"font-size: 30px;"]); 
  nbsp(3); // spaces
  echo "Email : ";
  form_input("email", ["type"=>"email", "size"=>40, "placeholder"=>"input email", "value"=>""]);
  form_submit( ["value"=>"Send Register Mail"] );
  form_end();
}
function show_login_info(){
  if (isset($_POST['logout'])){
    if (isset($_COOKIE["PHPSESSID"])){
      setcookie("PHPSESSID","",time()-1800,"/");
    }
    if (isset($_COOKIE[session_name()])){
      setcookie(session_name(),"",time()-1800,"/");
    }
    @session_destroy();
    header('Location: '.$_SERVER['REQUEST_URI']);
    exit();
  }
  echo "<div style=\"font-size: 80%; background: #eff; border: 1px solid #9cc; padding: 4px 20px; margin: 10px;\">";
  if (isset($_SESSION['user'])) {
    echo "Hello, ".$_SESSION['user']." !";
    form_start(["style"=>"float: right;"]);
    form_input("logout", ["type"=>"hidden", "value"=>$_SESSION['user']]);
    form_submit( ["value"=>"Logout"] );
    form_end();
  }
  echo "</div>";
}

// set default serial key if not definded
if (!defined("SERIALKEY")){
  define("SERIALKEY","R@nd0m1Z3dK6ns");
}
function ary2serial($ary, $salt=SERIALKEY){
  $s = serialize($ary);
  $k = md5($s.$salt);
  $master = $s.substr($k,0,8);
  $gz = gzdeflate($master);
  $val = base64_encode($gz);
  return str_replace(array('+', '/', '='), array('_', '-', '.'), $val);
}
function serial2ary($serial, $salt=SERIALKEY){
  $val = str_replace(array('_','-', '.'), array('+', '/', '='), $serial);
  $gz = base64_decode($val);
  $master = gzinflate($gz);
  $k = substr($master,-8);
  $s = substr($master,0,-8);
  $m = md5($s.$salt);
  if (substr($m,0,8)!=$k) return false;
  return unserialize($s);
}
function getaccesskey($dbfn,$table){
  return ary2serial( ["dbfn"=>$dbfn, "table"=>$table]);
}

/**
 HTML
*/
function showqrcode($url=null, $size=100){
  if ($url==null)  $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
  echo "<img style=\"border: 1px solid #eee;\" src=\"https://chart.apis.google.com/chart?cht=qr&chs={$size}x{$size}&chl={$url}\">";
}
function css(){
  echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } h1 {margin: 10px;} h2 {margin: 8px;} h3 {margin: 6px;}</style>';
}
function jquery(){
  echo '<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>';
  echo '<script>function jqadd2(table,dbfn){
  $("#add_"+table).load("_jqadd.php?table="+table+"&dbfn="+dbfn);
  }
  function jqedit2(table,id,dbfn){
  $("#add_"+table).load("_jqedit.php?table="+table+"&id="+id+"&dbfn="+dbfn);
  }</script>';
  echo '<script>function jqadd(ak,table){
  $("#add_"+table).load("_jqadd.php?ak="+ak);
  }
  function jqedit(ak,id,table){
  $("#add_"+table).load("_jqedit.php?ak="+ak+"&id="+id);
  }</script>';
  $jquery_enabled = true;
}
// add button for insert new data using JQuery
function jqaddform($table,$dbfn){
  if (!isset($jquery_enabled) || !$jquery_enabled) jquery(); // load JQuery script if not loaded previously

  echo "<div class=\"jqadd_{$table}\" id=\"add_{$table}\">";
  $ak = getaccesskey($dbfn,$table);
  echo "<button onclick=\"jqadd('{$ak}','{$table}')\">Add {$table}</button>";
//  echo "<button onclick=\"jqadd2('{$table}','{$dbfn}')\">Add {$table}</button>";
  echo "</div>";
}
function br($num=1){
  for($i=0;$i<$num;$i++) echo "<br>\n";
}
function title($title){
  echo "<title>{$title}</title>\n";
}
function heading($txt, $level=1){
  echo "<h{$level}>{$txt}</h{$level}>\n";
}
function div($txt,$ary=[]){
  echo "<div ";
  foreach($ary as $k=>$v){
    echo $k."=\"{$v}\" ";
  }
  echo ">{$txt}</div>\n";
}
function span($txt,$ary=[]){
  echo "<span ";
  foreach($ary as $k=>$v){
    echo $k."=\"{$v}\" ";
  }
  echo ">{$txt}</span>\n";
}
function space($num){ nbsp($num); }
function nbsp($num){
  for($i=0;$i<$num;$i++) echo " ";
}
function show_sqlite_admin_link(){
  $baseurl = get_sweetie_baseurl();
  show_link("open phpLiteAdmin",$baseurl."phpliteadmin/phpliteadmin.php", ["target"=>"_blank"]);
}
function show_link($label, $url, $ary = []){
  echo "<a ";
  $ary["href"]=$url;
  foreach($ary as $k=>$v){
    echo $k."=\"{$v}\" ";
  }
  echo ">{$label}</a>\n";
}
function show_linkb($label, $url, $ary = []){
  $ary["onclick"]="location.href='{$url}'";
  $ary["type"]="button";
  echo "<button ";
  foreach($ary as $k=>$v){
    echo $k."=\"{$v}\" ";
  }
  echo ">{$label}</button>\n";
}

/**
 making HTML Form
*/
function form_start($ary = [] ){
  if (!isset($ary["method"])) $ary["method"]="post";
  echo "<form ";
  foreach($ary as $k=>$v){
    echo $k."=\"{$v}\" ";
  }
  echo ">\n";
}

function form_input($name, $ary=["type"=>"text"]){
  if (!isset($ary["type"])) $ary["type"]="text";
  if ($ary["type"]=="textarea"){ // special treatment, value is surrounded with textarea tag
    echo "<"."textarea name=\"{$name}\" ";
    foreach($ary as $k=>$v){
      echo "{$k}=\"{$v}\" ";
    }
    echo ">\n";
    if (isset($ary["value"])) echo $ary["value"];
    echo "<"."/textarea>"; // for limitation of Sweetie Web Editor, close textarea tag cannot be written directly...
  } else {
    echo "<input name=\"{$name}\" ";
    foreach($ary as $k=>$v){
      echo "{$k}=\"{$v}\" ";
    }
    echo ">\n";
  }
}
function form_select($name, $ary=[], $label="", $after=""){
  echo "{$label} <select name=\"{$name}\">\n";
  foreach($ary as $k=>$v){
    echo "<option value=\"{$k}\">{$v}</option>\n";
  }
  echo "</select> \n";
  echo $after;
}
function form_radio($name, $ary=[], $label="", $between="", $after="", $keyofchecked=null){
  $count = 0;
  echo "{$label} \n";
  foreach($ary as $k=>$v){
    echo "<input type=\"radio\" ";
    if ($keyofchecked==null && $count==0){
      echo "checked=\"checked\" ";
    } else if ($k==$keyofchecked){
      echo "checked=\"checked\" ";
    }
    echo "name=\"{$name}\" value=\"{$k}\" id=\"{$name}__{$k}\"><label for=\"{$name}__{$k}\">{$v}</label>\n";
    echo $between;
    $count++;
  }
  echo $after;
  echo "\n";
}
function form_submit($ary = ["value"=>"Submit"] ){
  echo "<input type=\"submit\" ";
  foreach($ary as $k=>$v){
    echo "{$k}=\"{$v}\" ";
  }
  echo ">\n";
}
function form_end(){
  echo "</form>\n";
}

/**
  Mail
*/
function sendMail($to, $subject, $body, $from_email,$from_name)
{
  $headers  = "MIME-Version: 1.0 \n" ;
  $headers .= "From: " .
    "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
    "<".$from_email."> \n";
  $headers .= "Reply-To: " .
    "".mb_encode_mimeheader (mb_convert_encoding($from_name,"ISO-2022-JP","AUTO")) ."" .
    "<".$from_email."> \n";
  $headers .= "Content-Type: text/plain;charset=ISO-2022-JP \n";

  /* Convert body to same encoding as stated 
     in Content-Type header above */
  $body = mb_convert_encoding($body, "ISO-2022-JP","AUTO");

  /* Mail, optional paramiters. */
  $sendmail_params  = "-f$from_email";

  mb_language("ja");
  mb_internal_encoding("UTF-8");
  $subject = mb_convert_encoding($subject, "ISO-2022-JP","AUTO");
  $subject = mb_encode_mimeheader($subject);

  $result = mail($to, $subject, $body, $headers, $sendmail_params);

  return $result;
}

