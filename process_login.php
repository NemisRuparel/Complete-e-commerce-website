<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();

function get_db_connection() {
    $conn = mysqli_connect('localhost', 'root', '', 'shopnow');
    if (!$conn) {
        die("Connection failed: " . mysqli_error($conn));
    }
    if (!mysqli_select_db($conn, 'shopnow')) {
        die("Database selection failed: " . mysqli_error($conn));
    }
    return $conn;
}

$conn = get_db_connection();

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=invalid");
    exit;
}

$email = mysqli_real_escape_string($conn, $email);
$query = "SELECT id, name, password_hash FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_array($result);
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
        header("Location: login.php?error=invalid");
        exit;
    }
} else {
    header("Location: login.php?error=invalid");
    exit;
}

mysqli_close($conn);
?>