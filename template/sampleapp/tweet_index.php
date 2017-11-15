<?php
require_once("_lib.php");
$dbfn = "tweet.db";
$db = dbopen($dbfn);
// prepare database if not exists
$db->exec("create table IF NOT EXISTS 'tweets' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'mes' TEXT NOT NULL, 'dt' DATETIME NOT NULL )");

if (isset($_POST['mes'])){ 
  sanitize($_POST);
  insert($db, "tweets", $_POST);
}

css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
title("Tweet Index");

heading("Tweet Index (Guest can tweet!)");
br(4);

showtable($db,"tweets",$dbfn);

form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
form_input("mes", ["type"=>"text", "size"=>80, "placeholder"=>"input message", "value"=>""]);
form_submit( ["value"=>"Tweet"] );
form_end();

show_linkb("Reload this page",$fullurl);

br(4);
show_link("admin","tweet_admin.php");