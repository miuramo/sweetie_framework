<?php
$dbfn = "davuser.db";
require_once("_lib.php");
$db = dbopen($dbfn);
$db->exec("CREATE TABLE if not exists users ( id integer primary key asc NOT NULL, username TEXT NOT NULL, digesta1 TEXT NOT NULL, UNIQUE(username))");

require_sweetielogin(); // require sweetie login to prohibit anonymous access
css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("WebDav User Admin");




// when BUY  is pressed  ----------------------
if (isset($_POST['username'])){ 
  sanitize($_POST);
  $digest = md5("{$_POST['username']}:SabreDAV:{$_POST['digesta1']}");
  $_POST['digesta1'] = $digest;
  insert($db, "users", $_POST);
}
// --------------------------------------------------


heading("WebDAV User Admin",1);
showtable_withdeledit($db,"users",$dbfn);
// jqaddform("users",$dbfn);

form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
echo "Username: "; form_input("username", ["type"=>"text", "size"=>30, "placeholder"=>"input username", "value"=>""]);
br();
echo "Password: "; form_input("digesta1", ["type"=>"password", "size"=>30, "placeholder"=>"input password", "value"=>""]);
br(2);
form_submit( ["value"=>"Add New WebDav User"] );
form_end();

show_sqlite_admin_link();
show_linkb("Reload this page",$fullurl);


heading("Your WebDAV URL");
$davurl = get_sweetie_baseurl()."_edit/_dav.php";
show_link($davurl, $davurl, ["target"=>"_blank"]);

?>
