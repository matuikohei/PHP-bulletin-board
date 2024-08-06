<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Database.php';

class Board {
    private $sessionManager;
    private $db;
    private $cont_id;

    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->db = new Database();
        $this->sessionManager->startSession();
        $this->cont_id = $this->sessionManager->generateContributorId();
    }

    public function handlePostRequest() {
        if (isset($_POST['post_btn'])) {
            $this->sessionManager->validateToken($_POST['board_token']);
            unset($_SESSION['id']);

            if (isset($_POST['post_title']) && $_POST['post_title'] != '') {
                $_SESSION['title'] = $_POST['post_title'];
                unset($_SESSION['err_msg_title']);
            } else {
                unset($_SESSION['title']);
                $_SESSION['err_msg_title'] = '※タイトルを入力して下さい';
            }

            if (isset($_POST['post_comment']) && $_POST['post_comment'] != '') {
                $_SESSION['comment'] = $_POST['post_comment'];
                unset($_SESSION['err_msg_comment']);
            } else {
                unset($_SESSION['comment']);
                $_SESSION['err_msg_comment'] = '※投稿内容を入力して下さい';
            }

            if (isset($_POST['post_title']) && $_POST['post_title'] != '' &&
                isset($_POST['post_comment']) && $_POST['post_comment'] != '') {
                $title = $_POST['post_title'];
                $comment = $_POST['post_comment'];
                $this->db->insertPost($title, $comment, $this->cont_id);
                unset($_SESSION['title']);
                unset($_SESSION['comment']);
            }
        }
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
}