<?php
require_once("__lib.php");
@session_start(); 
?>
<!doctype html>

<title>CodeMirror: Search/Replace Demo</title>
<meta charset="utf-8"/>
<link rel="stylesheet" href="cm/doc/docs.css">

<link rel="stylesheet" href="cm/lib/codemirror.css">
<link rel="stylesheet" href="cm/addon/dialog/dialog.css">
<link rel="stylesheet" href="cm/addon/search/matchesonscrollbar.css">
<script src="cm/lib/codemirror.js"></script>
<script src="cm/addon/mode/loadmode.js"></script>
<script src="cm/mode/meta.js"></script>
<script src="cm/mode/clike/clike.js"></script>
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
 /*      dt {font-family: monospace; color: #666;}*/
</style>

<?php
if (file_exists("config.php")) require_once("config.php");
if (!isset($_POST['dir'])) $_POST['dir']="";
if (strlen($_POST['dir'])>0) $_POST['dir'].="/";
?>
<article>
<form id="EditForm" action="__save.php" method="post">
<span id="currentfile" style="font-size: large;"><?php echo $_POST['dir'].$_POST['file'] ?></span> 
   &nbsp;  &nbsp;  &nbsp;
<input id="file" type="text" name="file" size="20" value="<?php echo $_POST['file'] ?>">
<button id="saveButton" class="tooltip" title="ファイルを保存(CTRL+S)">
<img src="gl/glyphicons-444-floppy-disk.png" width=16>
</button>
<button id="indentButton" class="tooltip" title="字下げをそろえる(CTRL+I)">
<img src="gl/glyphicons-109-left-indent.png" width=16>
</button>
<button id="exportButton" class="tooltip" title="出力を確認(CTRL+R)">
<img src="gl/glyphicons-390-new-window-alt.png" width=16>
</button>
&nbsp; <span id="openlink"></span> &nbsp; <span id="srclink">
<?php
   $key = md5($_POST['file'].$config_srcsalt);
   echo "<a target=\"_blank\" href=\"src.php?file={$_POST['file']}&key={$key}\">[view source]</a>";
?></span>
&nbsp; &nbsp; 
<input id="dummy" type="text" name="dummy" style="display:none;">
<input type="checkbox" id="viewmodechk" onclick="javascript:showAll();">
	 <label for="viewmodechk">Show ALL</label>
<input type="checkbox" id="updateconfirmonsave" >
	 <label for="updateconfirmonsave">Confirm on Save</label>
<div id="fontsizeslider" style="width: 200px; float: right;"></div>
<br style="clear:both;">
<textarea id="code" name="code" contenteditable="true">
<?php
$txt = file_get_contents($_POST['dir'].$_POST['file']);
$txt = preg_replace('/&lt;/',"&amp;lt;", $txt);
$txt = preg_replace('/&gt;/',"&amp;gt;", $txt);
$txt = preg_replace('/&quot;/',"&amp;quot;", $txt);
$txt = preg_replace('/&#39;/',"&amp;#39;", $txt);
echo $txt;
?></textarea></form>

<script>
var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
  mode: "text/x-csrc",
  styleActiveLine: true,
  lineNumbers: true,
  lineWrapping: true,
});
</script>

 <div class="noprint" style="color: gray; text-align: right; ">Current Mode: <span id="modeinfo">text/plain</span> </div>
    <dl class="noprint">
      <dt>Ctrl-S / Cmd-S</dt><dd>Save content (if text is empty, delete the file)</dd>
      <dt>Ctrl-F / Cmd-F</dt><dd>Start searching</dd>
      <dt>Ctrl-G / Cmd-G</dt><dd>Find next</dd>
      <dt>Shift-Ctrl-G / Shift-Cmd-G</dt><dd>Find previous</dd>
      <dt>Shift-Ctrl-F / Cmd-Option-F</dt><dd>Replace</dd>
      <dt>Shift-Ctrl-R / Shift-Cmd-Option-F</dt><dd>Replace all</dd>
    </dl>
<!-- <div id="test" contenteditable="true" onpaste="handlepaste(this,event)">paste test</div> -->
</article>

