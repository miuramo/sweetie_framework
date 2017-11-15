<?php
require_once("_lib.php");
$dbfn = "photo.db";
$db = dbopen($dbfn);
$db->exec("create table IF NOT EXISTS 'photos' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'image' BLOB NOT NULL, 'image_name' TEXT NOT NULL, 'image_type' TEXT NOT NULL, 'image_size' INTEGER NOT NULL, 'dt' DATETIME NOT NULL)");

require_sweetielogin(); // require sweetie login to prohibit anonymous access

css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Photo Admin");

echo("This page's URL = ".$fullurl);
br();
showqrcode($fullurl); // showqrcode(url, size=100 ) 

show_sqlite_admin_link();
show_linkb("Reload this page",$fullurl);
show_link("Photo Toppage","photo_index.php",["target"=>"_blank"]);



// when BUY  is pressed  ----------------------
if (isset($_FILES['image'])){ 
  insert($db, "photos", $_FILES); 
}
// --------------------------------------------------
$ut = tbl($db,"photos");
aryinscol($ut, "<a href='_delete.php?table=photos&id={\$id}&dbfn={$dbfn}'>Delete</a>","delete");
aryinscol($ut, "<a href='#{\$id}' onclick='jqedit(\"photos\",{\$id},\"{$dbfn}\);'>Edit</button>","edit");
aryinscol($ut, "<a href=\"img.php?id={\$id}&table=photos&field=image&size={\$image_size}\" target=\"_blank\"><img src='img.php?id={\$id}&table=photos&field=image&size={\$image_size}' width='150'></a>","image", 1);
aryinscol($ut, "<a href='imgdownload.php?id={\$id}&table=photos&field=image&size={\$image_size}'>DL</a>","link", 2);
table($ut);

//showtable_withdeledit($db,"photos");
jqaddform("photos",$dbfn);

?>
<h1>Upload Photo</h1>
<?php
form_start( ["enctype"=>"multipart/form-data", "style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
form_input("image", ["type"=>"file", "size"=>60, "placeholder"=>"specify a photo" ]);
form_submit( ["value"=>"Upload"] );
form_end();


heading("Field name conventions (field name rule in Sweetie Framework)",3);
?>
<ul>
  <li>id is mandatory, primary key, autoincrement, integer</li>
  <li>field contains "pass" represents password.  The raw data is converted (hashed) when storing.</li>
  <li>dt (DATETIME) represents timestamp.</li>
  <li>field contains "img" (BLOB) represents image.  </li>
  <li>field contains "file" (BLOB) represents file.  </li>
  <li></li>
</ul>
