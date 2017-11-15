<?php
require_once("__lib.php");

$url = 'http://me.istlab.info/swweb/down.php';
$data =array(
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

exit(0);



