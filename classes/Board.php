<!-- 掲示板アプリのメイン機能を提供するクラス。 -->

<?php
// classes/Board.php

require_once 'SessionManager.php';
require_once 'Database.php';

class Board {
    private $sessionManager;
    private $db;
    private $err_msg_title = '';
    private $err_msg_comment = '';
    private $err_msg_image = '';

    // コンストラクタ: セッションの初期化とログイン状態の確認を行う。
    public function __construct() {
        // セッション管理クラスとデータベースクラスのインスタンスを作成
        $this->sessionManager = new SessionManager();
        // セッションを開始
        $this->db = new Database();
        $this->sessionManager->startSession();

        // ユーザーがログインしているか確認し、ログインしていない場合はログインページにリダイレクト
        if (!$this->sessionManager->isLoggedIn()) {
            header('Location: login.php'); // ログインしていない場合はログインページにリダイレクト
            exit();
        }
    }

    // 投稿リクエストを処理するメソッド
    public function handlePostRequest() {
        // フォームが送信されたか確認
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRFトークンの検証
            $this->sessionManager->validateToken($_POST['board_token']);
            unset($_SESSION['id']);

            // タイトルが入力されているか確認
            if (isset($_POST['post_title']) && $_POST['post_title'] != '') {
                $_SESSION['title'] = $_POST['post_title'];
            } else {
                unset($_SESSION['title']);
                $this->err_msg_title = '※タイトルを入力して下さい';
            }

            // コメントが入力されているか確認
            if (isset($_POST['post_comment']) && $_POST['post_comment'] != '') {
                $_SESSION['comment'] = $_POST['post_comment'];
            } else {
                unset($_SESSION['comment']);
                $this->err_msg_comment = '※投稿内容を入力して下さい';
            }

            // 画像のアップロード処理
            $imagePath = $this->handleImageUpload();

            // タイトル、コメントが入力されており、画像アップロードに成功していれば投稿をデータベースに保存
            if ($_SESSION['title'] != '' && $_SESSION['comment'] != '' && $imagePath !== false) {
                $title = $_SESSION['title'];
                $comment = $_SESSION['comment'];
                $userId = $_SESSION['user_id'];  // ログインユーザーIDを取得
                $this->db->insertPost($title, $comment, $userId, $imagePath);
                unset($_SESSION['title'], $_SESSION['comment']);
            }
        }
    }

    /**
     * 画像アップロードを処理するメソッド。
     *
     * @return string|bool アップロードされた画像のパス、または失敗時はfalseを返す。
     */
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

    /**
     * 全ての投稿を取得するメソッド。
     *
     * @return array 投稿データの配列を返す。
     */
    public function getPosts() {
        return $this->db->fetchAllPosts();
    }

    /**
     * CSRFトークンを生成するメソッド。
     *
     * @return string CSRFトークンを返す。
     */
    public function generateToken() {
        return $this->sessionManager->setToken();
    }

    /**
     * タイトルのエラーメッセージを取得するメソッド。
     *
     * @return string エラーメッセージを返す。
     */
    public function getErrMsgTitle() {
        return $this->err_msg_title;
    }

    /**
     * コメントのエラーメッセージを取得するメソッド。
     *
     * @return string エラーメッセージを返す。
     */
    public function getErrMsgComment() {
        return $this->err_msg_comment;
    }

    /**
     * 画像のエラーメッセージを取得するメソッド。
     *
     * @return string エラーメッセージを返す。
     */
    public function getErrMsgImage() {
        return $this->err_msg_image;
    }

    /**
     * 投稿を削除するメソッド。
     *
     * @param int $post_id 削除する投稿のID。
     */
    public function deletePost($post_id) {
        $this->db->deletePost($post_id);
    }

    /**
     * 投稿IDに基づいて投稿を取得するメソッド。
     *
     * @param int $post_id 取得する投稿のID。
     * @return array 投稿データを返す。
     */
    public function getPostById($post_id) {
        return $this->db->getPostById($post_id);
    }

    /**
     * 投稿を更新するメソッド。
     *
     * @param int $id 更新する投稿のID。
     * @param string $title 更新後のタイトル。
     * @param string $comment 更新後のコメント。
     * @param string $imagePath 更新後の画像パス。
     */
    public function updatePost($id, $title, $comment, $imagePath) {
        $this->db->updatePost($id, $title, $comment, $imagePath);
    }
}
?>