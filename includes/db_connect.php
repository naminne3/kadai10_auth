<?php

// <!-- データベース接続情報（ローカル） -->
$db_name = "gs_db4";    // データベース名
$db_id   = "root";      // アカウント名
$db_pw   = "";      // パスワード (XAMPPの場合は空欄)
$db_host = "localhost"; // DBホスト

// データベース接続情報（さくら）
// $db_name =  'XXX';            //データベース名
// $db_host =  'XXX';  //DBホスト
// $db_id =    'XXX';      //アカウント名(登録しているドメイン)→データベース名
// $db_pw =    'XXX';           //さくらサーバのパスワード



try {
    // データベースに接続
    $pdo = new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    // エラーモードを設定 (例外をスローするように設定)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // エラーが発生した場合、エラーメッセージを表示して終了
    exit('DB Connection Error:'.$e->getMessage());
}
?>