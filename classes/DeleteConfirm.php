<!--  投稿の削除確認機能を提供するクラス -->

<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Database.php';

class DeleteConfirm {
    private $sessionManager;
    private $db;

    // コンストラクタ: セッションの開始とデータベース接続の初期化
    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->db = new Database();
        $this->sessionManager->startSession(); // セッションを開始
    }

    // リクエストを処理するメソッド
    public function handleRequest() {
        if (isset($_POST['delete_btn'])) {
            $this->getPostInfo(); // 削除対象の投稿情報を取得
        } elseif (isset($_POST['delete_submit_btn'])) {
            $this->deletePost(); // 投稿を削除する
        } elseif (isset($_POST['cancel_btn'])) {
            $this->cancelDelete(); // 削除をキャンセルする
        }
    }

    // 削除対象の投稿情報を取得するメソッド
    private function getPostInfo() {
        if (isset($_POST['post_id']) && $_POST['post_id'] != '') {
            $_SESSION['id'] = $_POST['post_id']; // 削除する投稿のIDをセッションに保存
            try {
                $pdo = $this->db->getPdo(); // データベース接続を取得
                $sql = 'SELECT id, title, comment, image_path FROM board_info WHERE id = :ID';
                $stmt = $pdo->prepare($sql); // SQL文を準備
                $stmt->bindValue(':ID', $_SESSION['id'], PDO::PARAM_INT);
                $stmt->execute(); // クエリを実行
                $post_info = $stmt->fetch(); // 結果を取得
                $_SESSION['title'] = $post_info['title'];
                $_SESSION['comment'] = $post_info['comment'];
                $_SESSION['image_path'] = $post_info['image_path'];
            } catch (PDOException $e) {
                echo '接続失敗' . $e->getMessage(); // エラーメッセージを表示
                exit();
            }
        }
    }

    // 投稿を削除するメソッド
    private function deletePost() {
        // CSRFトークンの検証
        if (empty($_SESSION['board_token']) || ($_SESSION['board_token'] !== $_POST['board_token'])) {
            exit('不正な投稿です');
        }
        unset($_SESSION['board_token']); // トークンを破棄

        try {
            $this->db->deletePost($_SESSION['id']); // 投稿を削除
            unset($_SESSION['id'], $_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']); // セッションから削除した投稿の情報を削除
            header('Location: delete-success.php'); // 削除成功ページにリダイレクト
            exit();
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();  // エラーメッセージを表示
            exit();
        }
    }

    // 削除をキャンセルするメソッド
    private function cancelDelete() {
        // セッションから投稿情報を削除
        unset($_SESSION['id'], $_SESSION['title'], $_SESSION['comment'], $_SESSION['image_path']);
        header('Location: board.php'); // 掲示板ページにリダイレクト
        exit();
    }

    // CSRFトークンを生成するメソッド
    public function generateToken() {
        $token = $this->sessionManager->setToken(); // トークンを生成
        $_SESSION['board_token'] = $token; // トークンをセッションに保存
        return $token;
    }
}
?>