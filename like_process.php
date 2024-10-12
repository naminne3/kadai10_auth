<?php
// セッション開始
session_start();

// データベース接続
include("includes/db_connect.php");
include("includes/functions.php");

$response = ['success' => false, 'message' => '']; 

// article_id を取得
$article_id = $_POST['article_id'];

// ユーザーID (セッションIDを使用)
$user_id = isset($_SESSION["chk_ssid"]) ? session_id() : null;

try {
    // 既にいいね済みかどうか確認
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE article_id = :article_id AND user_id = :user_id");
    $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
        // 既にいいね済みの場合は削除
        $stmt = $pdo->prepare("DELETE FROM likes WHERE article_id = :article_id AND user_id = :user_id");
        $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $response['message'] = 'いいねを取り消しました。';
    } else {
        // いいねがまだの場合は追加
        $stmt = $pdo->prepare("INSERT INTO likes (article_id, user_id) VALUES (:article_id, :user_id)");
        $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $response['message'] = 'いいねしました！';
    }

    // いいね数の更新
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE article_id = :article_id");
    $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $likes_count = $stmt->fetchColumn();
    $response['likesCount'] = $likes_count;

    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = 'エラーが発生しました: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);