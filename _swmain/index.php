<?php
require_once("__lib.php");
@session_start(); 

// GET で file=xxx.html とすると，xxx.html を開くが，URLに残るのがいやなので
// POSTで送りなおす
$file = filter_input(INPUT_GET, 'file');
if (strlen($file)>0 && is_file($file)){
  echo '<form method="post" action="index.php">';
  echo '<input type="hidden" name="file" value="'.$file.'"/>';
  echo '</form>';
  echo '<script>document.forms[0].submit();</script>';
  exit;
}

?>
<!doctype html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <title>Sweetie :: Simple Web Editor Utilities</title>
  <link href="jq/jquery-ui.css" rel="stylesheet">
  <style>
  body{
font: 80% "Trebuchet MS", sans-serif;
margin: 10px;
}
.tree { width:242px; height: 600px;
border: solid 1px #bbb;
background: #fff;
overflow: scroll;
padding: 5px;
 }
</style>

<link rel="stylesheet" href="cm/doc/docs.css">
  <link rel="stylesheet" href="cm/lib/codemirror.css">
  <link rel="stylesheet" href="cm/addon/dialog/dialog.css">
  <link rel="stylesheet" href="cm/addon/search/matchesonscrollbar.css">
  <script src="cm/lib/codemirror.js"></script>
  <script src="cm/addon/mode/loadmode.js"></script>
  <script src="cm/mode/meta.js"></script>
  <script src="cm/addon/dialog/dialog.js"></script>
  <script src="cm/addon/search/searchcursor.js"></script>
  <script src="cm/addon/search/search.js"></script>
  <script src="cm/addon/scroll/annotatescrollbar.js"></script>
  <script src="cm/addon/search/matchesonscrollbar.js"></script>
  <script src="cm/addon/selection/active-line.js"></script>
  <style type="text/css">
  .CodeMirror {font-family: "Liberation Mono","Verdana",monospace;
border: 2px solid #ccc; overflow-y: hidden;}
.activeline {background: #e8f2ff !important;}
</style>

<link rel="stylesheet" href="mm/fuwari.css">
<link rel="stylesheet" href="mm/slide.css">
  <script src="mm/lib2.js"></script>

  </head>
  <body>
  <div class="alert">fuwari hogehoge</div>

  <h1>&nbsp; Sweetie :: <u>S</u>imple <u>We</u>b <u>E</u>ditor Utili<u>tie</u>s
  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<a target="_blank" href="index.php" style="font-size:small;">[open new tab]</a>
           &nbsp; 
<a target="index" href="../index.html" style="font-size:small;">[MDWiki Top]</a>
           &nbsp; 
<a href="logout.php" style="font-size:small;">[logout]</a> </h1>

<div class="paneltab">								<div class="panel">								           <button id="refreshB"  title="Refresh the file list">
           <img src="gl/glyphicons-82-refresh.png" width=22>
           </button>
           <button id="newfileB" title="Create New File">
           <img src="gl/newfile3.png" width=21>
           </button>
           <button id="newfolderB" title="Create New Folder">
           <img src="gl/glyphicons-146-folder-plus.png" width=24>
           </button>
           <button id="zipB" title="Download Zip Archive">
           <img src="gl/glyphicons-411-compressed.png" width=24>
           </button>
           <div id="fs" class="tree"></div>
</div>
</div>
           <!-- Editor -->
           <div id="ed" style="margin-left: 13px;">

           <div style="margin: 10px 20px; padding: 10px 20px; background-color: #eee; ">
           <h1> <span class="ui-icon ui-icon-circle-arrow-w"></span> Click file button to Edit</h1>
           <br>
           <h2> Server IP Address [<?php echo (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR']: "") ;?>]</h2>
           <h2> Client IP Address [<?php echo $_SERVER['REMOTE_ADDR'];?>]</h2>

           <p>
           <a href="../index.html" target="_blank">MDWiki Toppage (index.html)</a>
           </p>

           </div>
           </div>

           <script src="jq/external/jquery/jquery.js"></script>
           <script src="jq/jquery-ui.js"></script>
           <script src="jq/jquery.ui-contextmenu.min.js"></script>

	   <script src="jq/tree/jqueryFileTree.js"></script>
           <link rel="stylesheet" href="jq/tree/jqueryFileTree.css">

           <script src="mm/pasteimage.js"></script>
           <script src="mm/pasteimage_add.js"></script>
           <script>
           CodeMirror.modeURL = "cm/mode/%N/%N.js";
function newfile(basedir){
  var dir = prompt("Input File Name. ファイル名のみを入力してください。\nDo not include the selected folder name ( "+basedir+" ) ","newfile.txt");
    if (dir === null) return;
    if (/^\//.test(dir)) {
      alert("do not include / on top"); return;
    }
    if (/^[A-Za-z0-9_\.\/ ]+$/.test(dir)) { // スペースは許可する
    } else {
      alert("File name with Special characters cannot be created."); return;
    }
    var dir_encode = encodeURIComponent(dir); // 半角スペースは%2fにする
    var urldir = basedir + dir_encode; //ここで、basedir と連結する。
    var filedir = basedir + dir; //ここで、basedir と連結する。
    $("#noshow").load("__mkfile.php?dir="+urldir,null,function(){
	alert("New File ["+filedir+"] created.");
	openfile(filedir);
	updatetree();
      });
}

$( "#refreshB" ).button().click( function(){
    updatetree();
  });
$( "#newfileB" ).button().click( function(){
    newfile("");
  });
$( "#newfolderB" ).button().click( function(){
    var dir = prompt("input folder name");
    if (dir === null) return;
    if (/^\//.test(dir)) {
      alert("do not include / on top"); return;
    }
    if (/^[A-Za-z0-9_\/]+$/.test(dir)) {
    } else {
      alert("Folder name with Special characters cannot be created."); return;
    }
    $("#noshow").load("__mkdir.php?dir="+dir,null,function(){
	alert("Folder ["+dir+"] created.");
	updatetree();
      });
  });
$( "#zipB" ).button().click( function(e){
    location.href = "__zip.php";
  });

buttonclickevent_assign();


$(document).ready(function(){
    $(document).keydown(
   function(e){
     // CTRL+T
     if ((e.which=='116'|| e.which=='84')&&(e.ctrlKey || e.metaKey)){
       e.preventDefault();
       cmcheck();
       return false;
     }
     // CTRL+S
     if ((e.which=='115'|| e.which=='83')&&(e.ctrlKey || e.metaKey)){
       e.preventDefault();
       save();
       confirm_on_save();
       return false;
     }
     // CTRL+R
     if ((e.which=='114'|| e.which=='82')&&(e.ctrlKey || e.metaKey)){
       e.preventDefault();
       confirm_always();
       return false;
     }
     // CTRL+I
     if ((e.which=='105'|| e.which=='73')&&(e.ctrlKey || e.metaKey)){
       e.preventDefault();
       indentAll();
       return false;
     }
     // CTRL+/
     if ((e.which=='191') &&(e.ctrlKey || e.metaKey)){
       e.preventDefault();
       commentCaretOrSel();
       return false;
     }
   });
    updatetree();
  });

function getext(fn){ // 拡張子をGET
  var ary = fn.split(".");
  if (ary.length===0) return "";
  else return ary[ary.length-1].toLowerCase();
}
function updatetree(){
  $("#fs").fileTree(
		    { root: '../<?=$basefolder?>/',script:'jqueryFileTree.php'},
		    function(file){
		      if (/.png$/i.test(file) || /.jpe*g$/i.test(file) || /.gif$/i.test(file) || /.pdf$/i.test(file)){ //画像やPDFの場合
			var win = window.open("../"+file,"_blank","left: 100, top:100, width=500, height=500");
		      } else {
			openfile(file);
		      }
		      $(".panel").addClass("left260");
		    }
		    );
}
$(".panel").hover(function(){
    $(".panel").removeClass("left260");
  });
$(".panel").contextmenu({
  delegate: ".button",
      menu : [
	      { title: "Create New File here", cmd: "newfile", uiIcon: "ui-icon-plusthick"},
	      { title: "Edit in New Window", cmd: "editnewwin", uiIcon: "ui-icon-newwin"},
	      { title: "Open in New Window", cmd: "opennewwin", uiIcon: "ui-icon-newwin"},
	      { title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
	      { title: "Rename", cmd: "rename", uiIcon: "ui-icon-pencil"},
	      { title: "Delete", cmd: "delete", uiIcon: "ui-icon-trash"},
	      { title: "Delete Folder", cmd: "delete_folder", uiIcon: "ui-icon-trash"}
	      ],
      beforeOpen: function(event,ui){
      var tgt = ui.target;
      
      //      console.log(ui.target.text());
      var rel = ui.target.attr("rel");
      var isfolder = (/^.+\/$/.test(rel));
      $(".panel").contextmenu("showEntry","newfile", isfolder); 
      $(".panel").contextmenu("showEntry","editnewwin", !isfolder); 
      $(".panel").contextmenu("showEntry","delete", !isfolder); 
      $(".panel").contextmenu("showEntry","delete_folder", isfolder);
      $(".panel").removeClass("left0").addClass("left0");

      var ext = getext(ui.target.text());
      var canopenexts = ["php","md","html","htm"];
      var canopen = !!~canopenexts.indexOf(ext);
      $(".panel").contextmenu("showEntry","opennewwin", canopen); 
      
    },
      close: function(event){
      $(".panel").removeClass("left0");
    },
      select: function(event,ui){
      if (/^newfile/.test(ui.cmd)){
	newfile(ui.target.text()+"/");
      }
      if (/^editnewwin/.test(ui.cmd)){ // edit in new window
	var rel = ui.target.attr("rel"); // rel = folder + file
	var win = window.open("index.php?file="+rel,"_blank");
      }
      if (/^opennewwin/.test(ui.cmd)){ // open in new window
	var rel = ui.target.attr("rel"); // rel = folder + file
	var ext = getext(ui.target.text());
	var candopenexts = ["php","html","htm"]; // direct open
	var candopen = !!~candopenexts.indexOf(ext);
	if (candopen){
	  var win = window.open("../"+rel,"_blank");
	} else {
	  var win = window.open("../index.html#"+rel,"_blank");
	}
      }
      if (/^rename/.test(ui.cmd) || /^copy/.test(ui.cmd)){ // rename / move / copy 
	var rel = ui.target.attr("rel");
	var isfolder = (/^.+\/$/.test(rel));
	var fileorfolder = (isfolder)? "folder": "file";
	// ここで，先頭の../basename と，最後の/ をとりのぞく
	var newname = prompt("["+ui.cmd+"] Input new "+fileorfolder+" name",rel);
	if (newname != null){
	  $.ajax({ type: "POST",
		url: "__renamecopy.php",
		data: { before : encodeURIComponent(rel),
		  after : encodeURIComponent(newname),
		  isfolder : isfolder,
		  cmd : ui.cmd,
		  },
		success: function(data,dataType){
		//		console.log(data);
		//		alert(fileorfolder+" "+ui.cmd+" has been succeeded.");
		updatetree();
	      },
		});
	}
      }
      
      if (/^delete/.test(ui.cmd)){
	if (confirm("Do you really want to "+ui.cmd+" "+ui.target.text()+" ??")){
	  var rel = ui.target.attr("rel");
	  $.ajax({ type: "POST",
		url: "__delete.php",
		data: { dir : encodeURIComponent(rel),
		  cmd : ui.cmd,
		  },
		success: function(data,dataType){
		//		console.log(data);
		//		alert(ui.target.text()+" has been deleted.");
		updatetree();
	      },
		});
	}
      }
    }
  });

var editor;
var cm_mode;// mode=php|css|
var cm_spec;// spec=text/css,application/x-httpd-php
var openingFileName;
var saveOrDelete = "Save";
var fontSize = 12;
var sessionID = "<?php echo session_id(); ?>";
var cmOps = {};
var lastSend = +new Date();

var openingWindow = null;
var confirmTabs = {};

<?php
    $file = filter_input(INPUT_POST, 'file');
    if (strlen($file)>0 && is_file($file)){
      echo "openfile(\"{$file}\");";
    }
?>
</script>

<span id="noshow" style="visibility: hidden;">not visible</span>
  <br style="clear:both;">
<div style="font-size:small; text-align:right; margin-right: 20px;">
  <a target="_blank" href="http://bit.ly/sweetieapp" style="font-size:small;">Sweetie</a> version 3.0 &nbsp; &copy; 2015 Motoki Miura (miuramo@gmail.com) </a> &nbsp;
</div>
</body>
</html>
