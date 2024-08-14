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

// 現在のページ番号を取得
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // 1ページあたりの表示件数

// 検索キーワードを取得
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// 掲示板のメイン処理を開始
$board = new Board();

$board->handlePostRequest(); // ユーザーの投稿リクエストを処理m、

// 検索キーワードがある場合の処理と通常の投稿取得処理
if (!empty($keyword)) {
    // 検索キーワードがある場合の処理
    $post_list = $board->searchPosts($keyword, $page, $limit); // 検索結果を取得
    $total_posts = $board->countSearchResults($keyword); // 検索結果の総投稿数を取得
} else {
    // 通常の投稿取得処理
    $post_list = $board->getPosts($page, $limit); // 指定されたページの投稿一覧を取得
    $total_posts = $board->getTotalPostCount(); // 総投稿数を取得
}

$token = $board->generateToken(); // CSRFトークンを生成

$user_id = $_SESSION['user_id']; // ログインしたユーザーのIDを取得

// 総投稿数を取得し、総ページ数を計算
$total_pages = ceil($total_posts / $limit);
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
    <section class="header">
        <div class="header-title-box">
            <h1 class="header-title">掲示板アプリ</h1>
        </div>
        <!-- ログアウトリンク -->
        <div class="logout-link">
            <form action="logout.php" method="post">
                <button type="submit" name="logout_button">ログアウト</button>
            </form>
        </div>
    </section>

    <div class="container board-container">
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
                </div>
                <div class="upload-section">
                    <label>
                        <p>画像アップロード</p>
                        <input type="file" name="post_image">
                        <!-- 画像のエラーメッセージ表示 -->
                        <?php if (!empty($board->getErrMsgImage())) {
                                echo "<p class='err'>" . htmlspecialchars($board->getErrMsgImage(), ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </label>
                </div>
                <!-- CSRFトークンをフォームに追加 -->
                <?php echo '<input type="hidden" name="board_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />'; ?>
                <button class="btn--mg-c" type="submit" name="post_btn" value="post_btn">投稿</button>
            </form>
        </section>

        <!-- 検索フォーム -->
        <section class="search-form">
            <form action="board.php" method="get">
                <input type="text" name="keyword" placeholder="検索キーワードを入力">
                <button type="submit" name="search_button">検索</button>
                <?php if (!empty($keyword)): ?>
                    <a href="board.php">全て表示</a>
                <?php endif; ?>
            </form>
        </section>

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

        <!-- ページネーション -->
        <section class="pagination">
            <?php if (!empty($keyword)): ?>
                <a href="board.php">全て表示</a>
            <?php endif; ?>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&keyword=<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">&laquo; 前のページ</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&keyword=<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" <?php if ($i == $page) echo 'class="current-page"'; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&keyword=<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">次のページ &raquo;</a>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>