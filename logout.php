<?php
require_once 'classes/User.php';

$user = new User();
$user->logout();
header('Location: login.php');
exit();
