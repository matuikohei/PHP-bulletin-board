<!-- セッション管理を行うクラス -->

<?php
class SessionManager {
    public function startSession() {
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

        // 最終アクティビティ時間を更新
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    // セッションの有効期限を確認するメソッドを追加
    private function isSessionExpired() {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            return true;
        }
        return false;
    }

    public function generateContributorId() {
        if (isset($_SESSION['cont_id'])) {
            return $_SESSION['cont_id'];
        } else {
            $cont_id = '';
            for ($i = 0; $i < 20; $i++) {
                $cont_id .= chr(mt_rand(65, 90));
            }
            $_SESSION['cont_id'] = $cont_id;
            return $cont_id;
        }
    }

    public function setToken() {
        $token = sha1(uniqid(mt_rand(), true));
        $_SESSION['board_token'] = $token;
        return $token;
    }

    public function validateToken($token) {
        if (empty($_SESSION['board_token']) || ($_SESSION['board_token'] !== $token)) {
            exit('不正な投稿です');
        }
        unset($_SESSION['board_token']);
        unset($_POST['board_token']);
    }
}
?>