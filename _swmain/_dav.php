<?php

// The autoloader
require 'vendor/autoload.php';

use Sabre\DAV;
use Sabre\DAV\Auth;


$candidates = explode("/",$_SERVER["SCRIPT_NAME"]);
$basefolder = $candidates[count($candidates)-3];

if (file_exists("../{$basefolder}/phpliteadmin/davuser.db")){
  $pdo = new \PDO("sqlite:../{$basefolder}/phpliteadmin/davuser.db");
  $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  $authBackend = new Auth\Backend\PDO($pdo);
} else if (file_exists("../{$basefolder}/davhtdigest")){
  $authBackend = new Auth\Backend\File("../{$basefolder}/davhtdigest");
}
$authBackend->setRealm("SabreDAV");
$authPlugin = new Auth\Plugin($authBackend);


// Now we're creating a whole bunch of objects
$rootDirectory = new DAV\FS\Directory("../{$basefolder}");

// The server object is responsible for making sense out of the WebDAV protocol
$server = new DAV\Server($rootDirectory);

// If your server is not on your webroot, make sure the following line has the
// correct information
//$server->setBaseUri("/16/{$basefolder}/_edit/_dav.php");
$server->setBaseUri( $_SERVER["SCRIPT_NAME"] );

// The lock manager is reponsible for making sure users don't overwrite
// each others changes.
$lockBackend = new DAV\Locks\Backend\File("../{$basefolder}/davlocks");
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// This ensures that we get a pretty index in the browser, but it is
// optional.
$server->addPlugin(new DAV\Browser\Plugin());

$server->addPlugin($authPlugin);

// All we need to do now, is to fire up the server
$server->exec();
?>
