<?php
require_once 'Config.php';

class Database {
    private $pdo;

    public function __construct() {
        // DSNの設定をConfigクラスから読み込む
        $dsn = Config::DB_HOST;
        $username = Config::DB_USER;
        $password = Config::DB_PASSWORD;

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // エラーメッセージを表示
            echo '接続失敗: ' . $e->getMessage();
            exit();
        }
    }

    public function insertPost($title, $comment, $contributor_id, $imagePath) {
        $stmt = $this->pdo->prepare('INSERT INTO board_info (title, comment, contributor_id, image_path) VALUES (:title, :comment, :contributor_id, :image_path)');
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':contributor_id', $contributor_id, PDO::PARAM_STR);
        $stmt->bindValue(':image_path', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function fetchAllPosts() {
        $stmt = $this->pdo->prepare('SELECT * FROM board_info ORDER BY id DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>