<?php
$dbfn = "enq.db";
require_once("_lib.php");
$db = dbopen($dbfn);
$db->exec("create table IF NOT EXISTS 'questions' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'question' TEXT NOT NULL, 'a1' TEXT NOT NULL, 'a2' TEXT NOT NULL, 'a3' TEXT NOT NULL, 'a4' TEXT NOT NULL )");
$db->exec("create table IF NOT EXISTS 'users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT NOT NULL, 'email' TEXT NOT NULL, 'hashpass' TEXT NOT NULL)");
$db->exec("create table IF NOT EXISTS 'answers' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'userid' INTEGER NOT NULL, 'a1' INTEGER NOT NULL, 'a2' INTEGER NOT NULL, 'a3' INTEGER NOT NULL, 'a4' INTEGER NOT NULL, 'dt' DATETIME NOT NULL)");

$questions = [ "INSERT INTO 'questions' ('id','question','a1','a2','a3','a4') VALUES ('1','住んでいるのはどこ？','北九州市八幡西区','北九州市八幡東区','北九州市のその他の区','北九州市以外');" ,
              "INSERT INTO 'questions' ('id','question','a1','a2','a3','a4') VALUES ('2','図書館にはどれくらいの頻度で行く？','毎日','毎週３～４日','毎週１～２日','それ以下');" ,
              "INSERT INTO 'questions' ('id','question','a1','a2','a3','a4') VALUES ('3','大学にはどれくらいの頻度で行く？','毎日','毎週３～４日','毎週１～２日','それ以下');" ,
              "INSERT INTO 'questions' ('id','question','a1','a2','a3','a4') VALUES ('4','博多駅にはどれくらいの頻度で行く？','毎週1回以上','隔週で1回','毎月1回','それ以下');"  ];
$uq = tbl($db,"questions"); // 質問テーブルを取得
if (count($uq)<1){ // まだ質問がつくられていなければ
  foreach($questions as $n=>$q){
    $db->exec($q); // 追加する
  }
}

require_sweetielogin(); // require sweetie login to prohibit anonymous access
css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Enquete Admin");

echo("This page's URL = ".$fullurl);
br();
showqrcode($fullurl); // showqrcode(url, size=100 ) 

show_sqlite_admin_link();
show_linkb("Reload this page",$fullurl);
show_link("Enquete Toppage","enq_index.php",["target"=>"_blank"]);



// when BUY  is pressed  ----------------------
if (isset($_POST['userid'])){ 
  //  sanitize($_POST);
  //  pr($_POST);
  //  $item = getrow($db, "items", $_POST["itemid"]);
  //  pr($item);
  //  $data = [ "userid"=>$_POST["userid"],
  //           "itemid"=>$_POST["itemid"],
  //           "num"=>$_POST["num"],
  //           "total"=>$_POST["num"] * $item["price"]
  //          ];
  //  insert($db, "logs", $data);
  //  $item["amount"] = $item["amount"] - $_POST["num"]; // reduce
  //  update($db, "items", $item); // modify by ID
}
// --------------------------------------------------


//$u1 = [ "name"=>"User1",
//       "email"=>"user1@example.com"  ];
// $lastinsertid = adduser($db, $u1); // adduser uses 'name' as hashpass if hasspass is not set
// pr($lastinsertid);
//$u2 = [ "name"=>"User2",
//       "email"=>"user2@example.com" ,
//       "hashpass"=>"this_is_rawpass" ] ;
//insert($db,"users",$u2); // insert to users table

// insert($db,"items", ["name"=>"Pen","price"=>200,"amount"=>50]);
//insert($db,"logs", ["userid"=>7,"itemid"=>1,"num"=>1,"total"=>100,"dt"=>date(DATE_ATOM)]);

$ut = tbl($db,"users");
aryinscol($ut, "<a href='_delete.php?table=users&id={\$id}&dbfn={$dbfn}'>Delete</a>","delete");
aryinscol($ut, "<a href='#{\$id}' onclick='jqedit(\"users\",{\$id},\"{$dbfn}\");'>Edit</button>","edit");
//arydelcol($ut,"hashpass");
arymapcol($ut,"hashpass",function($a){ return substr($a, 0, 20)."..." ; }); // truncate long string
heading("Users",3);
table($ut);
jqaddform("users",$dbfn);

//showtable($db,"items");
heading("Questions",3);
showtable_withdeledit($db,"questions",$dbfn);
jqaddform("questions",$dbfn);

heading("Answers",3);
showtable_withdeledit($db,"answers",$dbfn);
jqaddform("answers",$dbfn);

?>
