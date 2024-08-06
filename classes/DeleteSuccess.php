<?php
require_once 'classes/SessionManager.php';

class DeleteSuccess {
    private $sessionManager;

    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->sessionManager->startSession();
    }

    public function redirectToBoard() {
        header('refresh: 3; url=board.php');
    }
}