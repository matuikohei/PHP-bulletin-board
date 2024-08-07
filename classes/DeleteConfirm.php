<!--  投稿の削除確認機能を提供するクラス -->

<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Database.php';

class DeleteConfirm {
    private $sessionManager;
    private $db;

    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->db = new Database();
        $this->sessionManager->startSession();
    }

    public function handleRequest() {
        if (isset($_POST['delete_btn'])) {
            $this->getPostInfo();
        } elseif (isset($_POST['delete_submit_btn'])) {
            $this->deletePost();
        } elseif (isset($_POST['cancel_btn'])) {
            $this->cancelDelete();
        }
    }

    private function getPostInfo() {
        if (isset($_POST['post_id']) && $_POST['post_id'] != '') {
            $_SESSION['id'] = $_POST['post_id'];
            try {
                $pdo = $this->db->getPdo();
                $sql = 'SELECT id, title, comment, image_path FROM board_info WHERE id = :ID';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':ID', $_SESSION['id'], PDO::PARAM_INT);
                $stmt->execute();
                $post_info = $stmt->fetch();
                $_SESSION['title'] = $post_info['title'];
                $_SESSION['comment'] = $post_info['comment'];
                $_SESSION['image_path'] = $post_info['image_path'];
            } catch (PDOException $e) {
                echo '接続失敗' . $e->getMessage();
                exit();
            }
        }
    }

    private function deletePost() {
        if (empty($_SESSION['board_token']) || ($_SESSION['board_token'] !== $_POST['board_token'])) {
            exit('不正な投稿です');
        }
        unset($_SESSION['board_token']);

        try {
            $this->db->deletePost($_SESSION['id']);
            unset($_SESSION['id'], $_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
            header('Location: delete-success.php');
            exit();
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    private function cancelDelete() {
        unset($_SESSION['id'], $_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
        header('Location: board.php');
        exit();
    }

    public function generateToken() {
        $token = $this->sessionManager->setToken();
        $_SESSION['board_token'] = $token;
        return $token;
    }
}
?>