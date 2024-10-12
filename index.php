<!-- // ブログ記事一覧表示 -->

<?php
// セッション開始
session_start();

// データベース接続
include("includes/db_connect.php");
include("includes/functions.php");

// セッションチェック (ログインユーザーのみアクセス可能にする場合)
// sschk(); 

// カテゴリIDの取得 (初期値はnull)
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null; 

// ハッシュタグの取得
$hashtag = isset($_GET['hashtag']) ? $_GET['hashtag'] : null;

// 記事データを取得
try {
    // SQL文のベース
    $sql = "SELECT a.*, c.name AS category_name FROM gs_an_table a LEFT JOIN categories c ON a.category_id = c.id WHERE 1=1 "; // 1=1 は常に真となる条件

    // カテゴリ絞り込み
    if (!empty($category_id)) { 
        $sql .= " AND a.category_id = :category_id ";
    }

    // ハッシュタグ絞り込み
    if (!empty($hashtag)) {
        $hashtag = '%' . addcslashes($hashtag, '%_\\') . '%'; 
        $sql .= " AND a.hashtag LIKE :hashtag ";
    }

    $sql .= " ORDER BY a.id DESC"; 

    $stmt = $pdo->prepare($sql); // SQL文を1回だけ準備

    if (!empty($category_id)) {
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    }

    if (!empty($hashtag)) {
        $stmt->bindValue(':hashtag', $hashtag, PDO::PARAM_STR);
    }

    $status = $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    sql_error($e->getMessage());
}



// ヘッダーを読み込み
include("templates/header.php");
?>

<h2>記事一覧</h2>
<div class="search-area">
<span><div class="search-box">
    <form method="get" action="">
        <label for="category"></label>
        <select id="category" name="category_id"> 
            <option value="">すべて</option> 
            <?php
            try {
                $stmt = $pdo->prepare("SELECT * FROM categories");
                $status = $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($categories as $category) {
                    $selected = ($category['id'] == $category_id) ? ' selected' : ''; // 選択されているカテゴリを設定 
                    echo "<option value='" . h($category['id']) . "'" . $selected . ">" . h($category['name']) . "</option>"; 
                }

            } catch (PDOException $e) {
                sql_error($e->getMessage());
            }
            ?>
        </select><input type="submit" value="絞り込み"> 
    </form> </span></div>

<span> <div class="hash-search-box">
    <form method="get" action="">
        <label for="hashtag"></label>
        <input type="text" id="hashtag" name="hashtag" placeholder="ハッシュタグ検索"> 
        <input type="submit" value="検索">
    </form>
</span></span>

<?php if (isset($_SESSION["chk_ssid"])) : ?> 
    <p><a href="new.php">新規投稿</a></p>
<?php endif; ?>
 </div> 
<!-- <div class="article-list"> <div class="article-container"> -->
<?php if (count($articles) > 0) : ?>
    
    <?php foreach ($articles as $article) : ?>
        

        <article class="article-item"> <div class="article-box">
            <a href="article.php?id=<?php echo h($article['id']); ?>">
            <h3><?php echo h($article['title']); ?>  </h3>
            </a>

            <div class="article-meta"> <div class="article-info">

            <span class="date"><?php echo h($article['indate']); ?></span>
            <?php if (!empty($article['category_name'])) : ?> 
                <span class="category">(<?php echo h($article['category_name']); ?>)</span> 
            <?php endif; ?> 
           
        </div> </div> 
        </div></article>
    <?php endforeach; ?>
  
<?php else : ?>
    <p>記事がありません。</p>
<?php endif; ?>

<?php
// フッターを読み込み
include("templates/footer.php");
?>



