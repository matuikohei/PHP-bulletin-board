<!-- データベース接続とクエリ実行を行うクラス -->

<?php
require_once 'Config.php';

class Database
{
    private $pdo;

    public function __construct()
    {
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

    // 他のクラスからもPDOを使える（データーベース接続をできる）ようにするためのメソッド
    public function getPdo()
    {
        return $this->pdo;
    }

    // データベースに新しい投稿を保存するためのメソッド
    public function insertPost($title, $comment, $user_id, $imagePath)
    {
        $stmt = $this->pdo->prepare('INSERT INTO board_info (title, comment, user_id, image_path) VALUES (:title, :comment, :user_id, :image_path)');
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':image_path', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }

    // 検索キーワードに基づいて投稿を検索するメソッド
    public function searchPosts($keyword, $page, $limit)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare(
            'SELECT * FROM board_info
             WHERE title LIKE :keyword1 OR comment LIKE :keyword2
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':keyword1', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':keyword2', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 検索結果の投稿数を取得するメソッド。
    public function countSearchResults($keyword)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM board_info WHERE title LIKE :keyword1 OR comment LIKE :keyword2');
        $stmt->bindValue(':keyword1', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':keyword2', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 指定されたページ番号に基づいて、そのページに表示する投稿をデータベースから取得するメソッド
    public function fetchPostsByPage($page, $limit)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare('SELECT * FROM board_info ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 投稿の総数を取得するメソッド
    public function countAllPosts()
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM board_info');
        return $stmt->fetchColumn();
    }

    // 画像のパスを取得するメソッド
    public function getImagePath($post_id)
    {
        $stmt = $this->pdo->prepare('SELECT image_path FROM board_info WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 投稿を削除するメソッド
    public function deletePost($post_id)
    {
        $imagePath = $this->getImagePath($post_id);
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $stmt = $this->pdo->prepare('DELETE FROM board_info WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
    }

}
?>