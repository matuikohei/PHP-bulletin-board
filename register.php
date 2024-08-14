<?php
require_once 'classes/User.php';

$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user->register($username, $password);
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="title">ユーザー登録</h1>
        <form action="register.php" method="post">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="register_button">登録</button>
        </form>
    </div>
</body>
</html>
