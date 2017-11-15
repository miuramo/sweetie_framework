<?php
$dbfn = "enq.db";
require_once("_lib.php");
$db = dbopen($dbfn);
$db->exec("create table IF NOT EXISTS 'questions' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'question' TEXT NOT NULL, 'a1' TEXT NOT NULL, 'a2' TEXT NOT NULL, 'a3' TEXT NOT NULL, 'a4' TEXT NOT NULL )");
$db->exec("create table IF NOT EXISTS 'users' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'name' TEXT NOT NULL, 'email' TEXT NOT NULL, 'hashpass' TEXT NOT NULL)");
$db->exec("create table IF NOT EXISTS 'answers' ('id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'userid' INTEGER NOT NULL, 'a1' INTEGER NOT NULL, 'a2' INTEGER NOT NULL, 'a3' INTEGER NOT NULL, 'a4' INTEGER NOT NULL, 'dt' DATETIME NOT NULL)");

require_login($dbfn); // require sweetie login to prohibit anonymous access
css(); // echo '<style> body { font: 120% "Trebuchet MS", sans-serif; } </style>';
jquery(); //enable jquery Javascript library
title("Enquete Index");

//echo("This page's URL = ".$fullurl);
//br();
//showqrcode($fullurl); // showqrcode(url, size=100 ) 

//show_sqlite_admin_link();
show_linkb("Reload this page",$fullurl);
//show_link("Enquete Toppage","enq_index.php",["target"=>"_blank"]);



// when BUY  is pressed  ----------------------
if (isset($_POST['userid'])){ 
  sanitize($_POST);
  pr($_POST);
  insert($db, "answers", $_POST);
}
// --------------------------------------------------

$questions = tbl($db,"questions");

//pr($questions);
form_start( ["style"=>"background: #ffc; border: 3px solid #cc9; padding: 10px;"] );
form_input("userid", ["type"=>"hidden", "value"=>$_SESSION['u']['id'] ]);

foreach($questions as $n=>$q){
  $sel = array(1=>$q['a1'], 2=>$q['a2'], 3=>$q['a3'], 4=>$q['a4'] ); 
  form_radio("a".($n+1), $sel, "Question ".($n+1)." : <b>".$q['question']."</b><br>", "   ", "<br><br>");
}
form_submit();
form_end();

//結果のテーブル表示
//showtable($db,"answers",$dbfn);

//結果をグラフ表示
require_once("_gchart.php"); // 必要なライブラリを追加
// _gchart.php のソースコード：http://kiu.istlab.info/16/miura/_edit/src.php?file=sampleapp/_gchart.php&key=7b461e4ff1f8d27496d4ae0467492cf6

$answers = tbl($db,"answers"); // 結果テーブルデータを、配列として取得しておく
// Q1 に関して、
$ques = getrow($db, "questions", 1);
// pr($ques);
// question1 の選択肢における、keyのa1,a2,a3,a4を、回答の1,2,3,4にあわせる必要がある
$items = count_item($answers, "a1"); // a1の回答選択肢=>回答頻度 の配列を生成
// pr($items);
//a1の回答選択肢(数字) を、$quesの選択肢（文字列）に変換する。
// ただし、もともとの回答選択肢は数字で、$quesは"a"+数字 なので、あわせるために"a"を指定する
$items = replace_key($items, $ques, "a"); 
//pr($items);
// パイチャートを表示する（Q1にかんして）
pie( $items , $ques['question'] );

for($num=2; $num<5; $num++){  // Q2～4にかんして、パイチャート表示をくりかえして生成（やっていることは上でQ1に対するものとおなじ）
  $ques = getrow($db, "questions", $num);
  $items = count_item($answers, "a".$num); // a1の回答選択肢=>回答頻度 の配列を生成
  $items = replace_key($items, $ques, "a"); 
  pie( $items , $ques['question'] );
}

?>
