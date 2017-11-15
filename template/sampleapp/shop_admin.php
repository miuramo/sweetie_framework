<?php
$dbfn = "shop.db";
require_once("_lib.php");
$db = dbopen($dbfn);
$db->exec("create table IF NOT EXISTS 'items' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT NOT NULL, 'price' INTEGER NOT NULL DEFAULT 100 , 'amount' INTEGER NOT NULL DEFAULT 30 )");
$db->exec("create table IF NOT EXISTS 'users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT NOT NULL, 'email' TEXT NOT NULL, 'hashpass' TEXT NOT NULL)");
$db->exec("create table IF NOT EXISTS 'logs' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'userid' INTEGER NOT NULL, 'itemid' INTEGER NOT NULL, 'num' INTEGER NOT NULL, 'total' INTEGER NOT NULL, 'dt' DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)");

require_sweetielogin(); // require sweetie login to prohibit anonymous access
css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Shop Admin");

echo("This page's URL = ".$fullurl);
br();
showqrcode($fullurl); // showqrcode(url, size=100 ) 

show_sqlite_admin_link();
show_linkb("Reload this page",$fullurl);
show_link("Shop Toppage","shop_index.php",["target"=>"_blank"]);



// when BUY  is pressed  ----------------------
if (isset($_POST['userid'])){ 
  sanitize($_POST);
  //  pr($_POST);
  $item = getrow($db, "items", $_POST["itemid"]);
  //  pr($item);
  $data = [ "userid"=>$_POST["userid"],
           "itemid"=>$_POST["itemid"],
           "num"=>$_POST["num"],
           "total"=>$_POST["num"] * $item["price"]
          ];
  insert($db, "logs", $data);
  $item["amount"] = $item["amount"] - $_POST["num"]; // reduce
  update($db, "items", $item); // modify by ID
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
$table = "users";
$ak = getaccesskey($dbfn,$table);
aryinscol($ut, "<a href='_delete.php?ak={$ak}&id={\$id}'>Delete</a>","delete");
aryinscol($ut, "<a href='#{$table}{\$id}' onclick='jqedit(\"{$ak}\",{\$id},\"{$table}\");'>Edit</a>","edit");
//arydelcol($ut,"hashpass");
arymapcol($ut,"hashpass",function($a){ return substr($a, 0, 20)."..." ; }); // truncate long string
heading("Users",3);
table($ut);
jqaddform("users",$dbfn);

//showtable($db,"items");
heading("Items",3);
showtable_withdeledit($db,"items",$dbfn);
jqaddform("items",$dbfn);

heading("Logs",3);
showtable_withdeledit($db,"logs",$dbfn);
jqaddform("logs",$dbfn);

$ret = sql($db, "select logs.id, users.name as uname, items.name as iname, num, total, dt from logs
left join users on users.id = logs.userid   left join items on items.id = logs.itemid");
heading("Joined table (dynamic: Logs + user.name + item.name)",3);
table($ret);
//pr($ret);
//phpinfo();

?>
<h1>Purchase</h1>
<?php
form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
$ut = tbl($db,"users"); 
$ut = arytohash($ut,"id","name");
form_select("userid", $ut, "Select User : ","<br>"); // name, dataarray, label (,after)
$it = tbl($db,"items");
$it = arytohash($it,"id","name");
//form_select("itemid", $it, "Select Item : "); //  name, dataarray, label (,after)
form_radio("itemid", $it, "Select Item : ", "   ", "<br>"); //  name, array, label, between, after (, key of checked data) 
echo "Num : ";
form_input("num", ["type"=>"text", "size"=>10, "placeholder"=>"input num", "value"=>1]);
form_submit( ["value"=>"Buy"] );
form_end();


