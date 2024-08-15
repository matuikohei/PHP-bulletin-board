<?php
require_once 'classes/User.php';

$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($user->login($username, $password)) {
        header('Location: board.php');
        exit();
    } else {
        $error = 'ログインに失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1 class="title">ログイン</h1>
        <?php if (isset($error)) echo "<p class='err'>$error</p>"; ?>

        <form action="login.php" method="post">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="login_button">ログイン</button>
        </form>

        <!-- 新規登録ボタンを追加 -->
        <div class="register-link-container">
            <p>新規登録をご希望の方は、こちらから</p>
            <a href="register.php">
                <button name="register_link_button">新規登録はこちら</button>
            </a>
        </div>
    </div>
</body>

</html>