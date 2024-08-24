<!-- セッション管理を行うクラス -->

<?php
class SessionManager
{
    public function startSession()
    {
        // すでにセッションが開始されているか確認
        if (session_status() == PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 1800);
            ini_set('session.gc_divisor', 1);
            session_start();

            // セッションの有効期限をチェックし、期限切れならセッションを破棄
            if ($this->isSessionExpired()) {
                session_unset();
                session_destroy();
                session_start(); // 新しいセッションを開始
            }

            session_regenerate_id(true);
        }

        // 最終アクティビティ時間を更新
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    // セッションの有効期限を確認するメソッド
    private function isSessionExpired()
    {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            return true;
        }
        return false;
    }

    // CSRFトークンを生成するメソッド
    public function setToken()
    {
        $token = sha1(uniqid(mt_rand(), true));
        $_SESSION['board_token'] = $token;
        return $token;
    }

    // CSRFトークンを検証するメソッド
    public function validateToken($token)
    {
        if (empty($_SESSION['board_token']) || ($_SESSION['board_token'] !== $token)) {
            exit('不正な投稿です');
        }
        unset($_SESSION['board_token']);
        unset($_POST['board_token']);
    }

    // ユーザーがログインしているか確認するメソッド
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // // ログイン状態を設定するメソッド　削除予定
    // public function loginUser($user_id)
    // {
    //     $_SESSION['user_id'] = $user_id;
    // }

}
?>