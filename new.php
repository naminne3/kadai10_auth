<!-- // 記事投稿画面 -->
<?php
// セッション開始
session_start();

// データベース接続
include("includes/db_connect.php");
include("includes/functions.php");

// セッションチェック (ログインユーザーのみアクセス可能にする場合)
sschk();

// ヘッダーを読み込み
include("templates/header.php");
?>

<h2>新規記事投稿</h2>

<form method="post" action="insert.php">
    <label for="title">タイトル:</label><br>
    <input type="text" id="title" name="title" required><br><br>

    <label for="naiyou">本文:</label><br>
    <textarea id="naiyou" name="naiyou" rows="10" required></textarea><br><br>

    <!-- SummernoteのCSSとJavaScriptを読み込み -->
    <link href="summernote/summernote-bs4.min.css" rel="stylesheet"> <div class="summernote">
    <script src="summernote/summernote-bs4.min.js"></script> <div class="summernote">

    <!-- Summernoteの初期化 -->
        <script>
        $(document).ready(function() {
        $('#naiyou').summernote({
            height: 300, // エディタの高さ
            toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        });
        </script>


    <label for="category">カテゴリ:</label><br>
    <select id="category" name="category_id">
        <?php
        try {
            $stmt = $pdo->prepare("SELECT * FROM categories");
            $status = $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($categories as $category) {
                echo "<option value='" . h($category['id']) . "'>" . h($category['name']) . "</option>";
            }

        } catch (PDOException $e) {
            sql_error($e->getMessage());
        }
        ?>
    </select><br><br>
    
    <label for="hashtag">ハッシュタグ:</label><br>
    <input type="text" id="hashtag" name="hashtag"><br><br>

    <label for="memo">メモ:</label><br>
    <textarea id="memo" name="memo" rows="4" cols="40"></textarea><br><br>
    

    <input type="submit" value="投稿">
</form>

<?php
// フッターを読み込み
include("templates/footer.php");
?>