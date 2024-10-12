<!-- // 記事詳細表示 -->
<?php
// セッション開始
session_start();

// データベース接続
include("includes/db_connect.php");
include("includes/functions.php");

// セッションチェック (ログインユーザーのみアクセス可能にする場合)
// sschk(); 

// GETパラメータから記事IDを取得
$id = $_GET["id"];

// 記事データを取得
try {
    // カテゴリテーブルを結合してカテゴリ名を取得
    $stmt = $pdo->prepare("SELECT a.*, c.name AS category_name FROM gs_an_table a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    sql_error($e->getMessage());
}


// 現在の記事IDを取得
$current_id = $article['id'];

// 前の記事を取得
try {
    $stmt = $pdo->prepare("SELECT id, title FROM gs_an_table WHERE id < :current_id ORDER BY id DESC LIMIT 1");
    $stmt->bindValue(':current_id', $current_id, PDO::PARAM_INT);
    $stmt->execute();
    $prev_article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    sql_error($e->getMessage());
}

// 次の記事を取得
try {
    $stmt = $pdo->prepare("SELECT id, title FROM gs_an_table WHERE id > :current_id ORDER BY id ASC LIMIT 1");
    $stmt->bindValue(':current_id', $current_id, PDO::PARAM_INT);
    $stmt->execute();
    $next_article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    sql_error($e->getMessage());
}


// ヘッダーを読み込み
include("templates/header.php");
?>

<!-- JSとCSSを読み込み -->

<script src="like.js"></script> <link rel="stylesheet" href="css/style.css">

<h2 class="article-title"><?php echo h($article['title']); ?></h2>

<p>投稿日: <?php echo h($article['indate']); ?></p>


<br><br><br>
<p><?php echo htmlspecialchars_decode($article['naiyou']); ?></p>
<br><br><br>


<?php if (!empty($article['hashtag'])) : ?>
    <p>ハッシュタグ: <?php echo h($article['hashtag']); ?></p>
<?php endif; ?>  

<?php if (!empty($article['category_name'])) : ?>
    <p>カテゴリ: <?php echo h($article['category_name']); ?></p> <div class="category">
<?php endif; ?>

<?php if (isset($_SESSION["kanri_flg"]) && $_SESSION["kanri_flg"] == 1) : ?>

    <a href="https://lifecareerdesign.sakura.ne.jp/kadai09_php/edit.php?id=<?php echo h($article['id']); ?>">編集</a>
    <a href="delete.php?id=<?php echo h($article['id']); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
<?php endif; ?>


<!-- 次へ前へボタン -->
<div class="navigation">
<?php if ($prev_article) : ?>
    <a href="article.php?id=<?php echo h($prev_article['id']); ?>">< 前の記事</a> 
<?php endif; ?>

<?php if ($next_article) : ?>
    <a href="article.php?id=<?php echo h($next_article['id']); ?>">次の記事 ></a>
<?php endif; ?>
</div>


<!-- いいねボタン -->
<?php
// いいね数の取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE article_id = :article_id");
$stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
$stmt->execute();
$likes_count = $stmt->fetchColumn(); 

// いいね済みかどうか判定
$liked = false;
if (isset($_SESSION["chk_ssid"])) {
    $user_id = session_id(); // 一意なIDとしてセッションIDを使用
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE article_id = :article_id AND user_id = :user_id");
    $stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetch()) {
        $liked = true;
    }
}
?>


<p>いいね: <span id="likes-count"><?php echo $likes_count; ?></span></p> <div class="likes-count">
<button id="like-button" data-article-id="<?php echo h($article['id']); ?>" class="<?php echo $liked ? 'liked' : ''; ?>">
    ♡ 
</button> 


<br><br><br><br>


<h3>コメント</h3>


<?php if (isset($_SESSION["chk_ssid"])) : ?> <div class="comment">
    <p>ようこそ、<?php echo h($_SESSION["name"]); ?>さん！</p> <div class="user-name">
    <form method="post" action="comment_insert.php">
        <input type="hidden" name="article_id" value="<?php echo h($article['id']); ?>">
        <input type="hidden" name="name" value="<?php echo h($_SESSION["name"]); ?>"> <div class="hidden-name">
        <label for="comment">コメント:</label><br>
        <textarea id="comment" name="comment" rows="5" required></textarea><br><br>
        <input type="submit" value="投稿">
    </form></div></div>
<?php else : ?>
    <p>コメントするには、<a href="login.php">ログイン</a>してください。</p> <div class="login-comment">
<?php endif; ?>



<?php
// コメントデータを取得
try {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE article_id = :article_id ORDER BY created_at ASC");
    $stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    sql_error($e->getMessage());
}
?>



<?php if (count($comments) > 0) : ?>
<ul>
    <?php foreach ($comments as $comment) : ?>
        <li>
            <p><strong><?php echo h($comment['name']); ?></strong> - <?php echo h($comment['created_at']); ?></p>
            <p><?php echo h($comment['comment']); ?></p>
            <?php if (isset($_SESSION["chk_ssid"]) && $_SESSION["name"] == $comment['name']) : ?> 
                <a href="comment_edit.php?id=<?php echo h($comment['id']); ?>">編集</a> |  
                <a href="comment_delete.php?id=<?php echo h($comment['id']); ?>&article_id=<?php echo h($article['id']); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else : ?>
    <p>コメントはまだありません。</p>
<?php endif; ?>



<?php
// 関連記事を取得
try {
    // 現在の記事のカテゴリIDを取得
    $current_category_id = $article['category_id'];

    // 関連記事を取得するSQL文
    $sql = "SELECT id, title FROM gs_an_table WHERE category_id = :category_id AND id != :current_id ORDER BY RAND() LIMIT 3"; // ランダムに1件取得

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':category_id', $current_category_id, PDO::PARAM_INT);
    $stmt->bindValue(':current_id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();
    $related_articles = $stmt->fetchAll(PDO::FETCH_ASSOC); // 複数件取得
} catch (PDOException $e) {
    sql_error($e->getMessage());
}

?>

<?php if (count($related_articles) > 0) : ?> <div class="related-area">
  <div class="related-article">
    <h3>関連記事</h3> <div class="related">
    <ul>
      <?php foreach ($related_articles as $related_article) : ?>
        <li>
          <a href="article.php?id=<?php echo h($related_article['id']); ?>"><?php echo h($related_article['title']); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php else : ?>
    <p>関連記事はありません。</p>
<?php endif; ?>


<?php
// フッターを読み込み
include("templates/footer.php");
?>