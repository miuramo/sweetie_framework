<?php
$dbfn = "photo.db";
require_once("_lib.php");
$db = dbopen($dbfn);
$db->exec("create table IF NOT EXISTS 'photos' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'image' BLOB NOT NULL, 'image_name' TEXT NOT NULL, 'image_type' TEXT NOT NULL, 'image_size' INTEGER NOT NULL, 'dt' DATETIME NOT NULL)");

//require_sweetielogin(); // require sweetie login to prohibit anonymous access
css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Photo Index");

heading("Guest user can only browse images");

show_linkb("Reload this page",$fullurl);


// when UPLOAD  is pressed  ----------------------
//if (isset($_FILES['image'])){ 
//  insert($db, "photos", $_FILES); 
//}
// --------------------------------------------------

$ut = tbl($db,"photos");
//aryinscol($ut, "<a href='delete.php?table=photos&id={\$id}'>Delete</a>","delete");
//aryinscol($ut, "<a href='#{\$id}' onclick='jqedit(\"photos\",{\$id});'>Edit</button>","edit");
aryinscol($ut, "<a href=\"img.php?id={\$id}&table=photos&field=image&size={\$image_size}\" target=\"_blank\"><img src='img.php?id={\$id}&table=photos&field=image&size={\$image_size}' width='200'></a>","image", 1);
aryinscol($ut, "<a href='imgdownload.php?id={\$id}&table=photos&field=image&size={\$image_size}'>DL</a>","link", 2);
table($ut);

br(10);
echo("This page's URL = ".$fullurl);
br();
showqrcode($fullurl); // showqrcode(url, size=100 ) 

//showtable_withdeledit($db,"photos");
showtable($db,"photos");
//jqaddform("photos");

//form_start( ["enctype"=>"multipart/form-data", "style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
//form_input("image", ["type"=>"file", "size"=>60, "placeholder"=>"specify a photo" ]);
//form_submit( ["value"=>"Upload"] );
//form_end();

