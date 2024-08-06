<?php
require_once 'classes/Board.php';

// メイン処理
$board = new Board();
$board->handlePostRequest();
$post_list = $board->getPosts();
$token = $board->generateToken();
$cont_id = $board->getContributorId();
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
    <h1>掲示板アプリ</h1>
    <!-- 投稿フォーム -->
    <section class="post-form">
        <form action="#" method="post">
            <div class="post-form__flex">
                <div>
                    <label>
                        <p>タイトル（※最大30文字）</p>
                        <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <!-- エラーメッセージ -->
                        <?php if (isset($_SESSION['err_msg_title'])) {
                            echo "<p class='err'>" . htmlspecialchars($_SESSION['err_msg_title'], ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
                <div>
                    <label>
                        <p>投稿内容（※最大1000文字）</p>
                        <textarea name="post_comment" cols="50" rows="10"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <!-- エラーメッセージ -->
                        <?php if (isset($_SESSION['err_msg_comment'])) {
                            echo "<p class='err'>" . htmlspecialchars($_SESSION['err_msg_comment'], ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
            </div>
            <?php echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />'; ?>
            <button class="btn--mg-c" type="submit" name="post_btn" value="post_btn">投稿</button>
        </form>
    </section>

    <hr>
    <!-- 投稿一覧 -->
    <section class="post-list">
        <?php if (count($post_list) === 0) : ?>
            <!-- 投稿が無いときはメッセージを表示する -->
            <p class="no-post-msg">現在、投稿はありません。</p>
        <?php else : ?>
            <ul>
                <!-- 投稿情報の出力 -->
                <?php foreach ($post_list as $post_item) : ?>
                    <li>
                        <form action="" method="post">
                            <!-- 投稿ID -->
                            <span>ID：<?php echo htmlspecialchars($post_item['id'], ENT_QUOTES, 'UTF-8'); ?>　</span>
                            <!-- 投稿タイトル -->
                            <span><?php echo htmlspecialchars($post_item['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <!-- 投稿者ID -->
                            <span>／投稿者：<?php echo htmlspecialchars($post_item['contributor_id'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <!-- 投稿内容 -->
                            <p class="p-pre"><?php echo htmlspecialchars($post_item['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <!-- 投稿日時 -->
                            <span class="post-datetime">投稿日時：<?php echo htmlspecialchars($post_item['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <!-- 過去に更新されていたら更新日時も表示 -->
                            <?php if ($post_item['created_at'] < $post_item['updated_at']) : ?>
                                <span class="post-datetime post-datetime__updated">更新日時：<?php echo htmlspecialchars($post_item['updated_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php endif; ?>
                        </form>
                        <!-- 自分の投稿内容かつセッションが有効な間は編集・削除が可能 -->
                        <?php if ($post_item['contributor_id'] === $cont_id) : ?>
                            <div class="btn-flex">
                                <form action="update-edit.php" method="post">
                                    <button type="submit" name="update_btn">編集</button>
                                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                </form>
                                <form action="delete-confirm.php" method="post">
                                    <button type="submit" name="delete_btn">削除</button>
                                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                </form>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == $post_item['id'])) : ?>
                            <p class='updated-post'>更新しました</p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</body>

</html>