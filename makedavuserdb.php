#!/usr/bin/php
<?php

function dbopen($dbfn){
  try {
      $pdo = new PDO('sqlite:'.$dbfn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}
function sql($pdo, $sql){
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ret = $stmt->fetchAll();
    return $ret;
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
}

if (!isset($argv[2])){
  echo "Usage: makedavuserdb.php swfoldername  password\n";
} else {

$dbfn = "davuser.db";
$db = dbopen($dbfn);
$db->exec("drop table if exists users"); //既存のテーブルを消す
$db->exec("CREATE TABLE if not exists users ( id integer primary key asc NOT NULL, username TEXT NOT NULL, digesta1 TEXT NOT NULL, UNIQUE(username))");

$user = $argv[1];
$rawpass = $argv[2];
$digest = md5("{$user}:SabreDAV:{$rawpass}");
$db->exec("INSERT INTO users (username,digesta1) VALUES ('{$user}',  '{$digest}')");

}

