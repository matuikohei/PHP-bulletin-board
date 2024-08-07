<!-- 掲示板アプリのメイン機能を提供するクラス。 -->

<?php
// classes/Board.php

require_once 'SessionManager.php';
require_once 'Database.php';

class Board {
    private $sessionManager;
    private $db;
    private $cont_id;
    private $err_msg_title = '';
    private $err_msg_comment = '';
    private $err_msg_image = ''; // 追加: 画像エラーメッセージ用のプロパティ

    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->db = new Database();
        $this->sessionManager->startSession(); // セッションの開始と再生成
        $this->cont_id = $this->sessionManager->generateContributorId();
    }

    public function handlePostRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRFトークンの検証
            $this->sessionManager->validateToken($_POST['board_token']);
            unset($_SESSION['id']);

            if (isset($_POST['post_title']) && $_POST['post_title'] != '') {
                $_SESSION['title'] = $_POST['post_title'];
            } else {
                unset($_SESSION['title']);
                $this->err_msg_title = '※タイトルを入力して下さい';
            }

            if (isset($_POST['post_comment']) && $_POST['post_comment'] != '') {
                $_SESSION['comment'] = $_POST['post_comment'];
            } else {
                unset($_SESSION['comment']);
                $this->err_msg_comment = '※投稿内容を入力して下さい';
            }

            $imagePath = $this->handleImageUpload(); // 追加: 画像アップロード処理

            if ($_SESSION['title'] != '' && $_SESSION['comment'] != '' && $imagePath !== false) {
                $title = $_SESSION['title'];
                $comment = $_SESSION['comment'];
                $this->db->insertPost($title, $comment, $this->cont_id, $imagePath); // 追加: 画像パスをデータベースに保存
                unset($_SESSION['title'], $_SESSION['comment']);
            }
        }
    }

    // 追加: 画像アップロード処理メソッド
    private function handleImageUpload() {
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['post_image']['name']);
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $uploadFile)) {
                return $uploadFile;
            } else {
                $this->err_msg_image = '画像のアップロードに失敗しました';
                return false;
            }
        }
        return '';
    }

    public function getPosts() {
        return $this->db->fetchAllPosts();
    }

    public function generateToken() {
        return $this->sessionManager->setToken();
    }

    public function getContributorId() {
        return $this->cont_id;
    }

    public function getErrMsgTitle() {
        return $this->err_msg_title;
    }

    public function getErrMsgComment() {
        return $this->err_msg_comment;
    }

    // 追加: 画像エラーメッセージを取得するメソッド
    public function getErrMsgImage() {
        return $this->err_msg_image;
    }

    // 追加: 投稿を削除するメソッド
    public function deletePost($post_id) {
        $this->db->deletePost($post_id);
    }

    // 追加: 投稿情報を取得するメソッド
    public function getPostById($post_id) {
        return $this->db->getPostById($post_id);
    }

    // 追加: 投稿を更新するメソッド
    public function updatePost($id, $title, $comment, $imagePath) {
        $this->db->updatePost($id, $title, $comment, $imagePath);
    }
}
?>