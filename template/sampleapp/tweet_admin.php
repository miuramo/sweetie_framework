<?php
$dbfn = "tweet.db";
require_once("_lib.php");
$db = dbopen($dbfn);
// prepare database if not exists
dropdb_nodata($db,"tweets"); // drop db if no data (to reset autoincrement id)
$db->exec("create table IF NOT EXISTS 'tweets' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'mes' TEXT NOT NULL, 'dt' DATETIME NOT NULL )");

require_sweetielogin(); // require sweetie login to prohibit anonymous access
css(); 
jquery(); //enable jquery Javascript library
title("Tweet Admin"); // window title

heading("Tweet Admin"); //<h2>Tweet Admin</h2>
div("Note: This page requires sweetie login", ["style"=>"background: #ffc; padding: 5px;"]);

//$row = getrow($db, "tweets", 3); // getrow(DBObject, tablename, ID)
//pr($row);
//insert($db, "tweets", $row);

//$ret = tbl($db, "tweets"); // get all rows , fields are (id, mes, dt)
//$id2dt = arytohash($ret, "id", "dt"); // arytohash(Array, keyField, valueField)
//prependstrtohash($id2dt, "Tweeted at: ");
//appendstrtohash($id2dt, " (in JST)");
//pr($id2dt); 


showtable_withdeledit($db,"tweets",$dbfn);
jqaddform("tweets",$dbfn);


br(4); // insert 4 <br>s

show_linkb("Reload this page",$fullurl);
show_link("Tweet Toppage","tweet_index.php",["target"=>"_blank"]);

