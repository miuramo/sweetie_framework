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
void setup()
{
  size(200,200);
  background(125);
  fill(255);
  noLoop();
  PFont fontA = loadFont("courier");
  textFont(fontA, 14);  
}

void draw(){  
  text("Hello Web!",20,20);
  println("Hello ErrorLog!");
}
</script>
<canvas id="mycanvas"></canvas>



