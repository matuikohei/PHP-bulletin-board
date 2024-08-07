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
    <meta name="viewport" content="width=device幅=device-width, initial-scale=1.0">
    <title>掲示板アプリ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>掲示板アプリ</h1>
    <section class="post-form">
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="post-form__flex">
                <div>
                    <label>
                        <p>タイトル（※最大30文字）</p>
                        <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (!empty($board->getErrMsgTitle())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgTitle(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
                <div>
                    <label>
                        <p>投稿内容（※最大1000文字）</p>
                        <textarea name="post_comment" cols="50" rows="10"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (!empty($board->getErrMsgComment())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgComment(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
                <div>
                    <label>
                        <p>画像アップロード</p>
                        <input type="file" name="post_image">
                        <?php if (!empty($board->getErrMsgImage())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgImage(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
            </div>
            <?php echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />'; ?>
            <button class="btn--mg-c" type="submit" name="post_btn" value="post_btn">投稿</button>
        </form>
    </section>

    <hr>
    <section class="post-list">
        <?php if (count($post_list) === 0) : ?>
            <p class="no-post-msg">現在、投稿はありません。</p>
        <?php else : ?>
            <ul>
                <?php foreach ($post_list as $post_item) : ?>
                    <li>
                        <span>ID：<?php echo htmlspecialchars($post_item['id'], ENT_QUOTES, 'UTF-8'); ?>　</span>
                        <span><?php echo htmlspecialchars($post_item['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span>／投稿者：<?php echo htmlspecialchars($post_item['contributor_id'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <p class="p-pre"><?php echo htmlspecialchars($post_item['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if (!empty($post_item['image_path'])) : ?>
                            <p><img src="<?php echo htmlspecialchars($post_item['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像"></p>
                        <?php endif; ?>
                        <span class="post-datetime">投稿日時：<?php echo htmlspecialchars($post_item['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($post_item['created_at'] < $post_item['updated_at']) : ?>
                            <span class="post-datetime post-datetime__updated">更新日時：<?php echo htmlspecialchars($post_item['updated_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
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
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</body>
</html>