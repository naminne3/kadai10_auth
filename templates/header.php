<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ブログシステム</title>
    <link rel="stylesheet" href="css/style.css">  
    <!-- jQueryとBootstrap 4の読み込みを追加 --> <div class="jq-boot">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Popper.js の読み込みを追加 -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<header>
    
    
    <div class="login-info">
    <?php if (isset($_SESSION["name"])) : ?>
        <div>ようこそ、<?php echo h($_SESSION["name"]); ?>さん！</div>
        <a href="logout.php">ログアウト</a>
    <?php else : ?>
        <a href="login.php">ログイン</a>
        <a href="signup.php">ユーザー登録</a>
    <?php endif; ?>
    </div>

    <a href="index.php"><div class="logo">FORMIES</a></div>

</header>

<img src="img/top.jpg" alt="ヘッダー画像"> <div class="header-image">

<main>