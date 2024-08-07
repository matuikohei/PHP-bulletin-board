<?php
require_once 'classes/DeleteSuccess.php';

// メイン処理
$deleteSuccess = new DeleteSuccess();
$deleteSuccess->redirectToBoard();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device幅=device-width, initial-scale=1.0">
    <title>掲示板アプリ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>削除が完了しました。</h1>
    <p class="delete-success-msg">3秒後に自動で掲示板TOPへ戻ります。</p>
</body>
</html>
?>