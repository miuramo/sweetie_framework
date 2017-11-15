<?php
$candidates = explode("/",$_SERVER["SCRIPT_NAME"]);
var_dump($candidates);
$basefolder = $candidates[count($candidates)-3];
echo $_SERVER["SCRIPT_NAME"];
  
$date = date(DATE_RSS);
echo strtoupper($date);
  
echo "<br>";

$num = rand(1, 100); // returns a 1~100 integer
echo $num;

echo "<br>";

echo "The square of {$num} is ". ($num*$num) ;

echo "<br>";
echo "<br>";

$capitals = array("Japan"=>"Tokyo", 
                  "Malaysia"=>"Kuala Lumpur",
                  "United States"=>"Washington, D.C.",
                  "United Kingdom"=>"London",
                  "France"=>"Paris",
                  "Germany"=>"Berlin" );
foreach($capitals as $key=>$value){
  echo "[{$key}] = {$value} <br>";
}
echo "<br>";
$city2country = array_flip($capitals);
foreach($city2country as $key=>$value){
  echo "[{$key}] = {$value} <br>";
}



?>