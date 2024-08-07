<?php
require_once 'classes/DeleteConfirm.php';

// メイン処理
$deleteConfirm = new DeleteConfirm();
$deleteConfirm->handleRequest();
$token = $deleteConfirm->generateToken();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板アプリ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>削除確認</h1>
    <p class="delete-confirm-msg">以下の投稿を削除します。</p>
    <!-- 削除確認画面 -->
    <section class="post-form">
        <form action="#" method="post">
            <div class="post-form__flex">
                <div>
                    <p>タイトル</p>
                    <p><?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div>
                    <p>投稿内容</p>
                    <p class="p-pre"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <?php
            //★【CSRF】 不正リクエストチェック用のトークン生成
            echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />';
            ?>
            <div class="btn-flex">
                <button type="submit" name="delete_submit_btn" value="delete_submit_btn">削除</button>
                <button type="submit" name="cancel_btn" value="cancel_btn">キャンセル</button>
            </div>
        </form>
    </section>
</body>
</html>