<?php
require_once 'classes/UpdateEdit.php';

// メイン処理
$updateEdit = new UpdateEdit();
$updateEdit->handleRequest();
$token = $updateEdit->generateToken();
$err_msg_title = $updateEdit->getErrMsgTitle();
$err_msg_comment = $updateEdit->getErrMsgComment();
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
    <div class="container">
        <h1 class="title">投稿編集画面</h1>
        <!-- 投稿編集フォーム -->
        <section class="post-form">
            <form action="#" method="post">
                <div class="post-form__flex">
                    <div>
                        <label>
                            <p>タイトル</p>
                            <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <!-- エラーメッセージ -->
                            <?php if (!empty($err_msg_title)) {
                                echo "<p class='err'>" . htmlspecialchars($err_msg_title, ENT_QUOTES, 'UTF-8') . "</p>";
                            } ?>
                        </label>
                    </div>
                    <div>
                        <label>
                            <p>投稿内容</p>
                            <textarea name="post_comment" cols="50" rows="10"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <!-- エラーメッセージ -->
                            <?php if (!empty($err_msg_comment)) {
                                echo "<p class='err'>" . htmlspecialchars($err_msg_comment, ENT_QUOTES, 'UTF-8') . "</p>";
                            } ?>
                        </label>
                    </div>
                </div>
                <?php
                //★ 不正リクエストチェック用のトークン生成
                echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />';
                ?>
                <div class="btn-flex">
                    <button type="submit" name="update_submit_btn" value="update_submit_btn">更新</button>
                    <button type="submit" name="cancel_btn" value="cancel_btn">キャンセル</button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>