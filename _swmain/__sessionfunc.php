<?php

/**
 * ログイン状態によってリダイレクトを行うsession_startのラッパー関数
 * 初回時または失敗時にはヘッダを送信してexitする
 */
function require_unlogined_session()
{
    // セッション開始
    session_name("__".BASEFOLDER);
    @session_start();
    // ログインしていれば / に遷移
    if (isset($_SESSION[BASEFOLDER])) {
        header('Location: index.php');
        exit;
    }
}
function require_logined_session()
{
    // セッション開始
    session_name("__".BASEFOLDER);
    @session_start();
    // ログインしていなければ /login.php に遷移
    if (!isset($_SESSION['username']) ) {
        header('Location: login.php');
        exit;
    }
    if ($_SESSION['username'] != BASEFOLDER){ 
        header('Location: login.php'); exit;
    }
}
function logout(){
  if (isset($_COOKIE["PHPSESSID"])){
    setcookie("PHPSESSID","",time()-1800,"/");
  }
  if (isset($_COOKIE[session_name()])){
    setcookie(session_name(),"",time()-1800,"/");
  }
  @session_destroy();
}

/**
 * CSRFトークンの生成
 *
 * @return string トークン
 */
function generate_token()
{
    // セッションIDからハッシュを生成
    return hash('sha256', session_id());
}

/**
 * CSRFトークンの検証
 *
 * @param string $token
 * @return bool 検証結果
 */
function validate_token($token)
{
    // 送信されてきた$tokenがこちらで生成したハッシュと一致するか検証
    return $token === generate_token();
}

/**
 * htmlspecialcharsのラッパー関数
 *
 * @param string $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
