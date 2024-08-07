<!-- データベース接続とクエリ実行を行うクラス -->

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

    public function getPdo() {
        return $this->pdo;
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

    // 画像のパスを取得するメソッド
    public function getImagePath($post_id) {
        $stmt = $this->pdo->prepare('SELECT image_path FROM board_info WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 投稿を削除するメソッドに画像の削除処理を追加
    public function deletePost($post_id) {
        $imagePath = $this->getImagePath($post_id);
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $stmt = $this->pdo->prepare('DELETE FROM board_info WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // 投稿情報を取得するメソッド
    public function getPostById($post_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM board_info WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 投稿を更新するメソッド
    public function updatePost($id, $title, $comment, $imagePath) {
        $stmt = $this->pdo->prepare('UPDATE board_info SET title = :title, comment = :comment, image_path = :image_path WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':image_path', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }
}
?>