<?php
$dbfn = "shop.db";
require_once("_lib.php");

require_login($dbfn); // require user/pass login. Try u1/u1 or u2/u2 or u3/u3

css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Shop Top");

echo("This page's URL = ".$fullurl);
br();
showqrcode($fullurl); // showqrcode(url, size=100 ) 

show_linkb("Reload this page",$fullurl);
  
$db = dbopen($dbfn);


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
  br(2);
  div("You bought {$_POST["num"]} {$item["name"]}(s) !! ", 
      ["style"=>"background: #fcf; border: 2px solid #f9f; padding: 10px;"] );
}
// --------------------------------------------------


heading("Purchase");

form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
form_input("userid", ["type"=>"hidden", "value"=>$_SESSION['u']['id'] ]);
$it = tbl($db,"items");
$idname = arytohash($it,"id","name");
$idprice = arytohash($it,"id","price");
$idleft = arytohash($it,"id","amount");
appendstrtohash($idname,"  (price: ");
mergehash($idname,$idprice);
appendstrtohash($idname,") (left: ");
mergehash($idname,$idleft);
appendstrtohash($idname,")");


//form_select("itemid", $it, "Select Item : "); //  name, dataarray, label (,after)
form_radio("itemid", $idname, "Select Item : <br>", "<br>", ""); //  name, array, label, between, after (, key of checked data) 
echo "Num : ";
form_input("num", ["type"=>"text", "size"=>10, "placeholder"=>"input num", "value"=>1]);
form_submit( ["value"=>"Buy"] );
form_end();

// pr($_SESSION['u']);


heading("Items you bought");
$uid = $_SESSION['u']['id'];
$logt = sql($db, "select logs.id, users.name as uname, items.name as iname, num, total, dt from logs
left join users on users.id = logs.userid   left join items on items.id = logs.itemid where users.id = {$uid}");
table($logt);

