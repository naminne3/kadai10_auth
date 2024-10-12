
    window.addEventListener('DOMContentLoaded', (event) => { // DOMの読み込みが完了してから実行
        document.getElementById('like-button').addEventListener('click', function() {
            const button = this;
            const articleId = button.dataset.articleId;



    // AJAXリクエスト
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'like_process.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // いいね数の更新
                document.getElementById('likes-count').textContent = response.likesCount;

                // ボタンのクラスを切り替え
                button.classList.toggle('liked');


                 // 画面をリロード  // 
                 location.reload();

            } else {
                alert(response.message);
            }
        } else {
            alert('いいね処理に失敗しました。');
        }
    };
    xhr.send('article_id=' + articleId);
});
});