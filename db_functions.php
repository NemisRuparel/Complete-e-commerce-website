<?php
// session_start();

// Database connection details
$host = 'localhost';
$username = 'root';
$password = ''; // Update if your MySQL has a password
$database = 'shopnow';

// Connect to MySQL
function db_connect() {
    global $host, $username, $password, $database;
    $conn = mysqli_connect($host, $username, $password);
    if (!$conn) {
        die("Connection failed: " . mysqli_error($conn));
    }
    if (!mysqli_select_db($conn, $database)) {
        die("Database selection failed: " . mysqli_error($conn));
    }
    return $conn;
}

// Close database connection
function db_close($conn) {
    mysqli_close($conn);
}

// --- Users Table Functions ---

function register_user($name, $email, $password, $phone = '', $address = '') {
    $conn = db_connect();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (name, email, password_hash, phone, address) 
              VALUES ('$name', '$email', '$password_hash', '$phone', '$address')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Registration failed: " . mysqli_error($conn);
        db_close($conn);
        return $error;
    }
    db_close($conn);
    return true;
}

function login_user($email, $password) {
    $conn = db_connect();
    $query = "SELECT id, name, password_hash FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            db_close($conn);
            return true;
        }
    }
    db_close($conn);
    return "Invalid email or password";
}

// --- Products Table Functions ---

function get_product($product_id) {
    $conn = db_connect();
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_array($result);
    db_close($conn);
    return $product;
}

function get_all_products() {
    $conn = db_connect();
    $query = "SELECT * FROM products";
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_array($result)) {
        $products[] = $row;
    }
    db_close($conn);
    return $products;
}

// --- Cart Items Table Functions ---

function add_to_cart($user_id, $product_id, $quantity) {
    $conn = db_connect();
    $query = "INSERT INTO cart_items (user_id, product_id, quantity) 
              VALUES ($user_id, $product_id, $quantity)";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Failed to add to cart: " . mysqli_error($conn);
        db_close($conn);
        return $error;
    }
    db_close($conn);
    return true;
}

function get_cart_items($user_id) {
    $conn = db_connect();
    $query = "SELECT ci.*, p.name, p.price, p.image_url 
              FROM cart_items ci 
              JOIN products p ON ci.product_id = p.id 
              WHERE ci.user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $items = [];
    while ($row = mysqli_fetch_array($result)) {
        $items[] = $row;
    }
    db_close($conn);
    return $items;
}

// --- Orders Table Functions ---

function create_order($user_id, $total_amount) {
    $conn = db_connect();
    $query = "INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $total_amount)";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Order creation failed: " . mysqli_error($conn);
        db_close($conn);
        return $error;
    }
    $order_id = $conn->insert_id;
    db_close($conn);
    return $order_id;
}

// --- Order Items Table Functions ---

function add_order_item($order_id, $product_id, $quantity, $price) {
    $conn = db_connect();
    $query = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
              VALUES ($order_id, $product_id, $quantity, $price)";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Failed to add order item: " . mysqli_error($conn);
        db_close($conn);
        return $error;
    }
    db_close($conn);
    return true;
}

// --- Contact Messages Table Functions ---

function save_contact_message($name, $email, $message) {
    $conn = db_connect();
    $query = "INSERT INTO contact_messages (name, email, message) 
              VALUES ('$name', '$email', '$message')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Failed to save message: " . mysqli_error($conn);
        db_close($conn);
        return $error;
    }
    db_close($conn);
    return true;
}

function get_contact_messages() {
    $conn = db_connect();
    $query = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
    $result = mysqli_query($conn, $query);
    $messages = [];
    while ($row = mysqli_fetch_row($result)) {
        $messages[] = $row;
    }
    db_close($conn);
    return $messages;
}
?>