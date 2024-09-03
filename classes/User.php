<!-- ユーザ認証および管理を行うクラス -->


<?php
// classes/User.php

require_once 'Database.php';
require_once 'SessionManager.php';

class User
{
    // データベース接続を管理するDatabaseクラスのインスタンス
    private $db;
    // セッション管理を行うSessionManagerクラスのインスタンス
    private $sessionManager;

    // コンストラクタ：クラスがインスタンス化されたときに呼び出される
    public function __construct()
    {
        // Databaseクラスのインスタンスを生成
        $this->db = new Database();
        // SessionManagerクラスのインスタンスを生成
        $this->sessionManager = new SessionManager();
        // セッションを開始
        $this->sessionManager->startSession();
    }

    // ユーザーのログインを処理するメソッド
    public function login($username, $password)
    {
        // ユーザー名に基づいてデータベースからユーザー情報を取得
        $stmt = $this->db->getPdo()->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザーが入力したパスワードとデータベースのパスワードが一致するかを確認
        if ($user && password_verify($password, $user['password'])) {
            // セッションにユーザーIDとユーザー名を保存し、ログイン状態に設定
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true; // ログイン成功
        }
        return false; // ログイン失敗
    }

    // ユーザーがログインしているかを確認するメソッド 削除予定
    // public function isLoggedIn()
    // {
    //     return isset($_SESSION['user_id']); // セッションにuser_idが設定されていればログイン中と見なす
    // }

    // ユーザーのログアウトを処理するメソッド
    public function logout()
    {
        session_destroy(); // セッションを破棄し、ログアウト状態にする
    }

    // 新しいユーザーを登録するメソッド
    public function register($username, $password)
    {
        // パスワードを安全にハッシュ化してからデータベースに保存（SQLインジェクション対策）
        $stmt = $this->db->getPdo()->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->execute(); // クエリを実行し、新しいユーザーをデータベースに追加
    }
}
?>