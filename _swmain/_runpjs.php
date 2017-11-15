<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
  <title>Pjs </title>
	<link href="jq/jquery-ui.css" rel="stylesheet">
	<style>
   body{
		font: 80% "Trebuchet MS", sans-serif;
		margin: 10px;
	}
	</style>

<script src="pjs/processing.min.js"></script>

</head>
<body>

<script type="text/processing" data-processing-target="mycanvas">
<?php
   $fn = $_GET['fn'];
readfile($fn);
?>
</script>
<canvas id="mycanvas"></canvas>



