<?php
require_once 'Config.php';

class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    public function insertPost($title, $comment, $cont_id) {
        $sql = '
            INSERT INTO
            board_info (title, comment, contributor_id)
            VALUES
            (:TITLE, :COMMENT, :CONTRIBUTOR_ID)
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':TITLE', $title, PDO::PARAM_STR);
        $stmt->bindValue(':COMMENT', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':CONTRIBUTOR_ID', $cont_id, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function fetchAllPosts() {
        $sql = '
            SELECT *
            FROM board_info
            ORDER BY id DESC
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPdo() {
        return $this->pdo;
    }
}