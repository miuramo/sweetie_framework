<?php
// execute "managepass.phpcli master" to initialize, by replacing MASTER_ PASSWORD_ HASH
$masterpasshash = 'MASTER_PASSWORD_HASH';

// execute "managepass.phpcli swkey" to initialize, by replacing SW KEY
$sweetiekey = 'SWKEY';
// thesweetiekey must be set at _swmain/login.php

// require_once("setting.php"); // swkey 

$subfolder = 'subfolder'; 

$default_src = 'template';
$allow_from_ip = '111\.222\.';
$allowed_by_ip = preg_match("/^".$allow_from_ip."/", $_SERVER['REMOTE_ADDR']);

function sedreplace($file, $before, $after){
  exec("/bin/sed -i -e 's|{$before}|{$after}|' $file");
}
?>

<!doctype html>
<html lang="en">
   <head>
   <meta charset="utf-8">
   <title>Sweetie Manager</title>
   <link href="_swmain/jq/jquery-ui.css" rel="stylesheet">
   <link rel="stylesheet" href="_swmain/cm/doc/docs.css">
   <style>
   body{
 font: 150% "Trebuchet MS", sans-serif;
 margin: 30px;
 }
   </style>
   </head>
   <body>
<?php
if (!$allowed_by_ip) {
  echo "(remote_addr: {$_SERVER['REMOTE_ADDR']})";
  echo " (allowed_by_ip = {$allowed_by_ip})";
}
?>

   <h1>Sweetie Manager</h1>

 <?php

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $src = filter_input(INPUT_POST, 'src');
     $dst = filter_input(INPUT_POST, 'dst');
     $src = trim($src); $dst = trim($dst);
     $password = filter_input(INPUT_POST, 'password');
     $beforedelete = 0;

     if (password_verify($password, $masterpasshash) || $allowed_by_ip ){
       if (strlen($src)>2){
	 $willexec = 1;
	 if ($src == "delete" || $src == "remove"){
	   $willexec = 0;
	   if (is_dir($dst)){
	     exec("rm -fr {$dst}");
	   }
	   echo "<h1 style=\"color: blue;\">Folder [{$dst}] has been successfully deleted.</h1>";
	   echo "<br><a href=\"makefolder.php\">Top</a>";
	   exit();
	 }
	 if ($src == "list"){
	   $willexec = 0;
       $passhash = array();
	   $dirlist = scandir(".");
	   echo "<table border=1>";
	   foreach($dirlist as $n=>$d){
	     if (is_dir($d)){
             if (preg_match("/^\./",$d) || $d == "_swmain" ) continue;
	       echo "<tr><td>";
	       echo $d;
	       echo "</td><td>";
	       echo "<a href=\"$d\" target=\"_blank\">$d</a>";
	       echo "</td><td>";
	       echo str_replace($d,"",exec("du -sh {$d}"));
	       echo "</td><td>";
           echo "<a href=\"$d/_edit\" target=\"_blank\">Edit</a>";
	       echo "</td></tr>";
           $passhash[$d] = substr(hash("sha256",$d.$sweetiekey),strlen($d),12);
	     }
	   }
	   echo "</table>";
	   echo "<br><a href=\"makefolder.php\">Top</a>";
       echo "<!-- \n\n\n";
       var_dump($passhash);
       echo "\n\n\n-->";
	   exit();
	 }
	 if (strlen($dst)>2){
	   if ($beforedelete==1){
	     if (is_dir($dst)){
	       exec("rm -fr {$dst}");
	     }
	   } else {
	     if (is_dir($dst)){
	       echo "<h1 style=\"color: orange;\">Error: The folder name you specified already exists. Please choose other folder name.</h1>";
	       $willexec = 0;
	     }
	   }
	 } else {
	   $willexec = 0;
	 }
     if (!preg_match('/^[a-zA-Z0-9_]{4,24}$/', $dst)){
         $willexec = 0;
         echo "<h1 style=\"color: orange;\">Error: folder name should not include special characters.  You can only use numbers, alphabets, and underscore(_)</h1>";
     }
     if (strlen($dst)<4 || strlen($dst)>24){
         $willexec = 0;
         echo "<h1 style=\"color: orange;\">Error: Please check the name length should be 4 to 24</h1>";
     }
	 if (!is_dir($src)){
	   echo "<h1 style=\"color: orange;\">Error: Src folder does not exist.</h1>";
	   $willexec = 0;
	 }

	 if ($willexec){
	   exec("cp -r {$src} {$dst}");
	 
	   chdir($dst);
	   if (!is_dir("img")) mkdir("img");
	   exec("ln -s ../_swmain ./_edit");
	   $pass = substr(hash("sha256",$dst.$sweetiekey),strlen($dst),12);
       sedreplace("phpliteadmin/phpliteadmin.config.php","hogehoge",$pass); // change pass
       sedreplace("phpliteadmin/phpliteadmin.config.php","0950ed7f0555",$pass); // change pass
       sedreplace("phpliteadmin/phpliteadmin.config.php","108005d50330",$pass); // change pass
       sedreplace("phpliteadmin/phpliteadmin.config.php","1b7103b6d4df",$pass); // change pass
       exec("/bin/chmod 444 phpliteadmin/phpliteadmin.php");

       // make and move davuser.db
       chdir("..");
       exec("./makedavuserdb.php {$dst} {$pass}");
       exec("mv davuser.db {$dst}/phpliteadmin/davuser.db");
	   
	   echo "<h1>[{$dst}] successfully created from [{$src}]</h1>";
	   // echo "<h1><a target=\"_blank\" href=\"{$dst}/_edit/index.php\">{$dst}</a></h1>";

// EDIT Subfolder name  
       $url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/{$subfolder}/".$dst."/";
       $editurl = $url."_edit";
       $webdavurl = $url."_edit/_dav.php";
	   echo "<h2 style=\"border: 5px dotted #9f9; background: #cfc; padding: 20px;\">Username: <b style=\"color:blue;\">{$dst}</b> / Password: <b style=\"color: red;\">{$pass}</b>
<br><br>Your URL: <a href=\"{$url}\" target=\"_blank\">{$url}</a>
<br><br>Editor URL: <a href=\"{$editurl}\" target=\"_blank\">{$editurl}</a>
<br><br>WebDAV URL: <a href=\"{$webdavurl}\" target=\"_blank\">{$webdavurl}</a>
</h2><pre>
Please keep the username and password NOW! (ex. Take a picture)<br><br>Note: The password characters are consists of <b style=\"color:red;\">0(zero)-9(nine)</b> numbers and <b style=\"color:red;\">abcdef</b> alphabets.</pre>";
	  // echo "<br><a href=\"makefolder.php\">Top</a>";
       exit();
	 }
       } else {
	 echo "<h1 style=\"color: orange;\">Error: No src and dst info.</h1>";
       }
     } else {
         //       echo password_hash($password, PASSWORD_BCRYPT);
       echo "<h1 style=\"color: red;\">Password is wrong</h1>";
     }
   }

if (!isset($dst)){
    $dst = "";
}
   ?>

   <form method="post" action="">
<?php if (!$allowed_by_ip){    ?>
   Master Password: <input type="password" name="password" value="<?=$password?>"><br>
   src folder: <input type="text" name="src" value="<?=$default_src?>"><br>
   dst folder: <input type="text" name="dst" value="<?=$dst?>"><br>
<?php } else { ?>
               <div style="border: 3px dotted #f99; background: #fcc; padding: 10px;">
   Please input your favorite folder name: <input type="text" name="dst" style="font-size:x-large;" value="<?=$dst?>" placeholder="input folder name here"><br>
               and press the button! 
   <input type="submit" value="Create Sweetie Folder"> </div>
               <br><br><br><br><br>
               <br><br><br><br><br>
               <br><br><br><br><br>
               <br><br><br><br><br>
(src folder: <?=$default_src?> ) <input style="font-size:xx-small; float:right;" size=1 type="text" name="src" value="<?=$default_src?>">
<?php }  ?>
   </form>
   <?php if (http_response_code() === 403): ?>
   <p style="color: red;">Password is wrong.</p>
   <?php endif; ?>


