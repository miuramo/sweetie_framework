<?php
require_once("__lib.php");
@session_start(); 

$zipFN = "Archive.zip";
makezip($zipFN);

header('Content-Type: application/zip; name="' . $zipFN . '"');
header('Content-Disposition: attachment; filename="' . $zipFN . '"');
header('Content-Length: '.filesize($zipFN));
echo file_get_contents($zipFN);
unlink($zipFN);
exit(0);
