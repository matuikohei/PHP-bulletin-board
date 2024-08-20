<!-- 掲示板アプリのメイン機能を提供するクラス。 -->

<?php
// classes/Board.php

require_once 'SessionManager.php';
require_once 'Database.php';

class Board
{
    private $sessionManager;
    private $db;
    private $err_msg_title = '';
    private $err_msg_comment = '';
    private $err_msg_image = '';

    // コンストラクタ: セッションの初期化とログイン状態の確認を行う。
    public function __construct()
    {
        // セッション管理クラスとデータベースクラスのインスタンスを作成
        $this->sessionManager = new SessionManager();
        $this->db = new Database();

        // セッションを開始
        $this->sessionManager->startSession();

        // ユーザーがログインしているか確認し、ログインしていない場合はログインページにリダイレクト
        if (!$this->sessionManager->isLoggedIn()) {
            header('Location: login.php'); // ログインしていない場合はログインページにリダイレクト
            exit();
        }
    }

    // 投稿リクエストを処理するメソッド
    public function handlePostRequest()
    {
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
            // DeleteConfig.phpでuser_idを使用するので削除しない
            if ($_SESSION['title'] != '' && $_SESSION['comment'] != '' && $imagePath !== false) {
                $title = $_SESSION['title'];
                $comment = $_SESSION['comment'];
                $userId = $_SESSION['user_id'];  // ログインユーザーIDを取得
                $this->db->insertPost($title, $comment, $userId, $imagePath);
                unset($_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
            }
        }
    }


    // 画像アップロードを処理するメソッド。
    private function handleImageUpload()
    {
        // フォームで画像がアップロードされているか確認し、アップロードにエラーがないか確認
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

    //  指定されたページの投稿を取得するメソッド
    public function getPosts($page = 1, $limit = 10)
    {
        return $this->db->fetchPostsByPage($page, $limit);
    }

    // 投稿の総数を取得するメソッド
    public function getTotalPostCount()
    {
        return $this->db->countAllPosts();
    }

    // 検索キーワードに基づいて投稿を取得するメソッド
    public function searchPosts($keyword, $page = 1, $limit = 10)
    {
        return $this->db->searchPosts($keyword, $page, $limit);
    }

    // 検索結果の投稿総数を取得するメソッド
    public function countSearchResults($keyword)
    {
        return $this->db->countSearchResults($keyword);
    }

    // CSRFトークンを生成するメソッド。
    public function generateToken()
    {
        return $this->sessionManager->setToken();
    }

    // タイトルのエラーメッセージを取得するメソッド。
    public function getErrMsgTitle()
    {
        return $this->err_msg_title;
    }

    // コメントのエラーメッセージを取得するメソッド。
    public function getErrMsgComment()
    {
        return $this->err_msg_comment;
    }

    // 画像のエラーメッセージを取得するメソッド。
    public function getErrMsgImage()
    {
        return $this->err_msg_image;
    }
}
?>