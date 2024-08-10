<?php
require_once 'classes/Board.php';
require_once 'classes/SessionManager.php';

// セッション管理とログイン状態の確認
$sessionManager = new SessionManager();
$sessionManager->startSession();

// ログインしているか確認
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php'); // ログインしていない場合はログインページにリダイレクト
    exit();
}

// 掲示板のメイン処理を開始
$board = new Board();
$board->handlePostRequest(); // ユーザーの投稿リクエストを処理
$post_list = $board->getPosts(); // 投稿一覧を取得
$token = $board->generateToken(); // CSRFトークンを生成
$user_id = $_SESSION['user_id']; // ログインしたユーザーのIDを取得
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
    <!-- ログアウトリンク -->
    <a href="logout.php">ログアウト</a>

    <!-- 投稿フォーム -->
    <section class="post-form">
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="post-form__flex">
                <div>
                    <label>
                        <p>タイトル（※最大30文字）</p>
                        <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <!-- タイトルのエラーメッセージ表示 -->
                        <?php if (!empty($board->getErrMsgTitle())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgTitle(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
                <div>
                    <label>
                        <p>投稿内容（※最大1000文字）</p>
                        <textarea name="post_comment" cols="50" rows="10"><?php if (isset($_SESSION['comment'])) echo htmlspecialchars($_SESSION['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <!-- コメントのエラーメッセージ表示 -->
                        <?php if (!empty($board->getErrMsgComment())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgComment(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
                <div>
                    <label>
                        <p>画像アップロード</p>
                        <input type="file" name="post_image">
                        <!-- 画像のエラーメッセージ表示 -->
                        <?php if (!empty($board->getErrMsgImage())) {
                            echo "<p class='err'>" . htmlspecialchars($board->getErrMsgImage(), ENT_QUOTES, 'UTF-8') . "</p>";
                        } ?>
                    </label>
                </div>
            </div>
            <!-- CSRFトークンをフォームに追加 -->
            <?php echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />'; ?>
            <button class="btn--mg-c" type="submit" name="post_btn" value="post_btn">投稿</button>
        </form>
    </section>

    <hr>
    <!-- 投稿リストの表示 -->
    <section class="post-list">
        <?php if (count($post_list) === 0) : ?>
            <p class="no-post-msg">現在、投稿はありません。</p>
        <?php else : ?>
            <ul>
                <?php foreach ($post_list as $post_item) : ?>
                    <li>
                        <!-- 投稿の各情報を表示 -->
                        <span>ID：<?php echo htmlspecialchars($post_item['id'], ENT_QUOTES, 'UTF-8'); ?>　</span>
                        <span><?php echo htmlspecialchars($post_item['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span>／投稿者：<?php echo htmlspecialchars($post_item['user_id'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <p class="p-pre"><?php echo htmlspecialchars($post_item['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <!-- 画像がある場合、画像を表示 -->
                        <?php if (!empty($post_item['image_path'])) : ?>
                            <p><img src="<?php echo htmlspecialchars($post_item['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="投稿画像"></p>
                        <?php endif; ?>
                        <span class="post-datetime">投稿日時：<?php echo htmlspecialchars($post_item['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <!-- 投稿が更新されている場合、更新日時を表示 -->
                        <?php if ($post_item['created_at'] < $post_item['updated_at']) : ?>
                            <span class="post-datetime post-datetime__updated">更新日時：<?php echo htmlspecialchars($post_item['updated_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        <!-- 投稿者が自分の場合、編集・削除ボタンを表示 -->
                        <?php if ($post_item['user_id'] === $user_id) : ?>
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