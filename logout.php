<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>