<?php
class SessionManager {
    public function startSession() {
        ini_set('session.gc_maxlifetime', 1800);
        ini_set('session.gc_divisor', 1);
        session_start();
        session_regenerate_id();
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