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

$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    header("Location: signin.php?error=missing_fields");
    exit;
}

if ($password !== $confirm_password) {
    header("Location: signin.php?error=password_mismatch");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signin.php?error=invalid_email");
    exit;
}

// Check if email already exists
$email_escaped = mysqli_real_escape_string($conn, $email);
$query = "SELECT id FROM users WHERE email = '$email_escaped'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    header("Location: signin.php?error=email_exists");
    exit;
}

// Insert new user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$name_escaped = mysqli_real_escape_string($conn, $name);
$hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);
$query = "INSERT INTO users (name, email, password_hash) VALUES ('$name_escaped', '$email_escaped', '$hashed_password_escaped')";
if (mysqli_query($conn, $query)) {
    $_SESSION['user_id'] = mysqli_insert_id($conn);
    $_SESSION['user_name'] = $name;
    header("Location: index.php");
    exit;
} else {
    header("Location: signin.php?error=registration_failed");
    exit;
}

mysqli_close($conn);
?>