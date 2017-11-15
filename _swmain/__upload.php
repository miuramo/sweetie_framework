<?php
require_once("__lib.php");
@session_start(); 

$zipFN = "Archive.zip";
makezip($zipFN);
$zipFC = file_get_contents($zipFN);

// アップロード機能を有効にするには、サーバスクリプトの場所を以下に設定してください
// To enable the upload function, set the URL of the [acceptzip.php] server script file.
// $url = 'http://me.istlab.info/sweetiedocs/acceptzip.php';
require_once("__uploadurl.php");



$data =array(
    'zipFC' => $zipFC,
    'user' => $_GET['user'],
    'pass' => $_GET['pass'],
);
                     
$data = http_build_query($data, "", "&");
 
$header = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: ".strlen($data)
);
                     
$options =array(
    'http' =>array(
            'method' => 'POST',
            'header' => implode("\r\n", $header),
            'content' => $data
        )
    );
 
$contents = file_get_contents($url, false, stream_context_create($options));
echo $contents;

unlink($zipFN);
exit(0);


