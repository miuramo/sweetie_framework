<?php
// $candidates = explode("/",$_SERVER["SCRIPT_NAME"]);
// $basefolder = $candidates[count($candidates)-3];

require_once '__sessionfunc.php';

logout();
?>
<!doctype html>
<html lang="en">
  <head>
	<meta charset="utf-8">
  <title>Sweetie Logout</title>
  <link href="jq/jquery-ui.css" rel="stylesheet">
  <link rel="stylesheet" href="cm/doc/docs.css">
  <style>
body{
  font: 120% "Trebuchet MS", sans-serif;
  margin: 10px;
}
  </style>
  </head>
  <body>
<h1>Sweetie Logout</h1>

<a href="login.php" style="margin-left: 100px;">login page</a>

