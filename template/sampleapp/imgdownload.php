<?php
// NOTE: size should be provided by GET method
$dbfn = "photo.db";
require_once("_lib.php");
$db = dbopen($dbfn);
sanitize($_GET);

function mime2ext($mime){
  $mime_types = array(
    'txt' => 'text/plain',
    'htm' => 'text/html',
    'html' => 'text/html',
    'php' => 'text/html',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'json' => 'application/json',
    'xml' => 'application/xml',
    'swf' => 'application/x-shockwave-flash',
    'flv' => 'video/x-flv',

    // images
    'png' => 'image/png',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'ico' => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif' => 'image/tiff',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',

    // archives
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'exe' => 'application/x-msdownload',
    'msi' => 'application/x-msdownload',
    'cab' => 'application/vnd.ms-cab-compressed',

    // audio/video
    'mp3' => 'audio/mpeg',
    'qt' => 'video/quicktime',
    'mov' => 'video/quicktime',

    // adobe
    'pdf' => 'application/pdf',
    'psd' => 'image/vnd.adobe.photoshop',
    'ai' => 'application/postscript',
    'eps' => 'application/postscript',
    'ps' => 'application/postscript',

    // ms office
    'doc' => 'application/msword',
    'rtf' => 'application/rtf',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',

    // open office
    'odt' => 'application/vnd.oasis.opendocument.text',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
  );
  $m2ext = array_flip($mime_types);
  if (isset($m2ext[$mime])) return $m2ext[$mime];
  else return "dat";
}
//pr($_GET);
$row = getrow($db, $_GET['table'],  $_GET['id']);
if ($_GET['size'] == $row[$_GET['field'].'_size']){
  if ( isset($row[$_GET['field']."_type"]) ){
    $mime = $row[$_GET['field']."_type"];
  } else {
    $finfo = new finfo(FILEINFO_MIME);
    $mime = $finfo->buffer($row[$_GET['field']]);    
  }
  if ( isset($row[$_GET['field']."_name"]) ){
    $fn = $row[$_GET['field']."_name"];
  } else {
    $fn = "file.".mime2ext($mime);
  }
  header('Content-Description: File Transfer');
  header("Content-Type: ".$mime);
  //  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="'.basename($fn).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  if ( isset($row[$_GET['field']."_size"]) ){
    $size = $row[$_GET['field']."_size"];
    header('Content-Length: ' . $size);
  }
  echo $row[$_GET['field']];
  exit;
}
