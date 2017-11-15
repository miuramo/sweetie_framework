<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
.prettyprint {
  padding: 8px !important ;
  border: 2px solid #ccc !important ;
}
body {
  font-family: "Liberation Mono","Verdana", monospace;
}
</style>
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
</head>
<body>
<?php
require_once("config.php");

if (isset($_GET['file'])){
  $file = $_GET['file'];
  $key = $_GET['key'];
  if ($key == md5($file.$config_srcsalt)){
    echo "<h2>{$file}</h2>";
    $h = fopen($file, "r");
    $src = fread($h, filesize($file));
    echo "<pre class=\"prettyprint\">";
    echo htmlspecialchars($src);
    echo "</pre>";
  } else {
    echo "No file specified (or key is wrong)";
  }
}
?>
</body>
</html>