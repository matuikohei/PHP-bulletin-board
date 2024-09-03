<!-- 投稿の編集機能を提供するクラス -->
<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Database.php';

class UpdateEdit
{
    private $sessionManager;
    private $db;
    private $err_msg_title = '';
    private $err_msg_comment = '';
    private $err_msg_image = '';

    // コンストラクタ クラスのインスタンスが作成された時に実行される
    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->db = new Database();
        $this->sessionManager->startSession();
    }

    // リクエストを処理するメソッド
    public function handleRequest()
    {
        // board.phpで更新ボタンが押された時の処理
        if (isset($_POST['update_btn'])) {
            $this->getPostInfo();
        // 更新ボタンが押された場合の処理
        } elseif (isset($_POST['update_submit_btn'])) {
            $this->updatePost();
        // キャンセルボタンが押された時の処理
        } elseif (isset($_POST['cancel_btn'])) {
            $this->cancelUpdate();
        }
    }

    // 投稿情報を取得するメソッド
    private function getPostInfo()
    {
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

    // 投稿を更新するメソッド
    private function updatePost()
    {
        $this->validateToken();
        $this->validateInputs();
        if ($this->isValid()) {
            $this->executeUpdate();
        }
    }

    // CSRFトークンを検証するメソッド
    private function validateToken()
    {
        if (empty($_SESSION['board_token']) || ($_SESSION['board_token'] !== $_POST['board_token'])) {
            exit('不正な投稿です');
        }
        unset($_SESSION['board_token']);
    }

    // ユーザーの入力を検証するメソッド
    private function validateInputs()
    {
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

        $this->handleImageUpload(); // 画像アップロード処理
    }

    // 画像をアップロードするメソッド
    private function handleImageUpload()
    {
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['post_image']['name']);
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $uploadFile)) {
                $_SESSION['image_path'] = $uploadFile;
            } else {
                $this->err_msg_image = '画像のアップロードに失敗しました';
            }
        }
    }

    // 入力が有効か確認するメソッド
    private function isValid()
    {
        return empty($this->err_msg_title) && empty($this->err_msg_comment) && empty($this->err_msg_image);
    }

    // 更新を実行するメソッド
    private function executeUpdate()
    {
        try {
            $pdo = $this->db->getPdo();
            $sql = 'UPDATE board_info SET title = :TITLE, comment = :COMMENT, image_path = :IMAGE_PATH WHERE id = :ID';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':ID', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':TITLE', $_SESSION['title'], PDO::PARAM_STR);
            $stmt->bindValue(':COMMENT', $_SESSION['comment'], PDO::PARAM_STR);
            $stmt->bindValue(':IMAGE_PATH', $_SESSION['image_path'], PDO::PARAM_STR);
            $stmt->execute();
            unset($_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
            header('Location: board.php');
            exit();
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    // 更新をキャンセルするメソッド
    private function cancelUpdate()
    {
        unset($_SESSION['id'], $_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
        header('Location: board.php');
        exit();
    }

    // タイトルのエラーメッセージを獲得するメソッド
    public function getErrMsgTitle()
    {
        return $this->err_msg_title;
    }

    // コメントのエラーメッセージを獲得するメソッド
    public function getErrMsgComment()
    {
        return $this->err_msg_comment;
    }

    // 画像のエラーメッセージを取得するメソッド
    public function getErrMsgImage()
    {
        return $this->err_msg_image;
    }

    // CSRFトークンを生成するメソッド
    public function generateToken()
    {
        $token = $this->sessionManager->setToken();
        $_SESSION['board_token'] = $token;
        return $token;
    }
}
?>