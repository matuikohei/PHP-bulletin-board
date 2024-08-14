<?php
require_once 'classes/DeleteConfirm.php';

$deleteConfirm = new DeleteConfirm(); // DeleteConfirmクラスのインスタンスを生成し、削除処理を実行
$deleteConfirm->handleRequest(); // リクエストのハンドリングを行う
$token = $deleteConfirm->generateToken(); // CSRFトークンを生成
?>

<!DOCTYPE html
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板アプリ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>削除確認</h1>
        <p class="delete-confirm-msg">以下の投稿を削除します。</p>
        <!-- 投稿削除の確認画面 -->
        <section class="post-form delete-confirm_post-form">
            <form action="#" method="post">
                <div class="post-form__flex">
                    <div>
                        <p>タイトル</p>
                        <!-- セッションから投稿タイトルを取得して表示 -->
                        <p class="delete-confirm-input"><?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div>
                        <p>投稿内容</p>
                        <!-- セッションから投稿内容を取得して表示 -->
                        <p class="p-pre delete-confirm-input"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
                <?php
                //★【CSRF】 不正リクエストチェック用のトークン生成
                echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />';
                ?>
                <div class="btn-flex">
                    <!-- 投稿削除ボタン -->
                    <button type="submit" name="delete_submit_btn" value="delete_submit_btn">削除</button>
                    <!-- キャンセルボタン -->
                    <button type="submit" name="cancel_btn" value="cancel_btn">キャンセル</button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>