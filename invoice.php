<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();

function get_db_connection() {
    $conn = mysqli_connect('localhost', 'root', '');
    if (!$conn) {
        die("Connection failed: " . mysqli_error($conn));
    }
    if (!mysqli_select_db($conn, 'shopnow')) {
        die("Database selection failed: " . mysqli_error($conn));
    }
    return $conn;
}

function get_user_data($conn, $user_id) {
    $user_name = '';
    $profile_image = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    
    if ($user_id) {
        $query = "SELECT name, profile_image FROM users WHERE id = " . intval($user_id);
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $user_name = $row['name'];
            if ($row['profile_image']) {
                $profile_image = $row['profile_image'];
            }
        }
    }
    
    return ['user_name' => $user_name, 'profile_image' => $profile_image];
}

// Redirect if no order exists
if (!isset($_SESSION['order'])) {
    header('Location: shop.php');
    exit;
}

$conn = get_db_connection();

// Fetch user data if logged in
$user_data = get_user_data($conn, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
$user_name = $user_data['user_name'];
$profile_image = $user_data['profile_image'];

$order = $_SESSION['order'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - SHOP NOW</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ffffff;
            --secondary-color: #f5f5f5;
            --accent-color: #333333;
            --text-color: #333333;
            --light-text: #666666;
            --border-color: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--primary-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: var(--primary-color);
            color: var(--text-color);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            height: 40px;
            width: auto;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-color);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links.active {
            display: flex;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-color);
            cursor: pointer;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav ul li a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: var(--light-text);
        }

        .nav-icons {
            display: flex;
            gap: 1rem;
            margin-left: 2rem;
        }

        .nav-icon {
            color: var(--text-color);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .nav-icon:hover {
            color: var(--accent-color);
        }

        .profile-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            color: var(--text-color);
            font-weight: 500;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            padding: 0.5rem;
        }

        .profile-container:hover .dropdown {
            display: block;
        }

        .dropdown a {
            color: var(--text-color);
            text-decoration: none;
            display: block;
            padding: 0.5rem;
        }

        .dropdown a:hover {
            background-color: var(--secondary-color);
        }

        main {
            margin-top: 80px;
            padding: 2rem 0;
        }

        .invoice-container {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .invoice-header h1 {
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .invoice-header p {
            color: var(--light-text);
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .invoice-section {
            background: var(--primary-color);
            padding: 1.5rem;
            border-radius: 5px;
        }

        .invoice-section h3 {
            color: var(--accent-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .invoice-section p {
            margin-bottom: 0.5rem;
        }

        .invoice-items {
            margin-bottom: 2rem;
        }

        .invoice-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .invoice-item:last-child {
            border-bottom: none;
        }

        .invoice-total {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .invoice-actions {
            text-align: center;
            margin-top: 2rem;
        }

        .print-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .print-btn:hover {
            background-color: var(--text-color);
        }

        @media print {
            header, .invoice-actions {
                display: none;
            }

            main {
                margin-top: 0;
            }

            .invoice-container {
                box-shadow: none;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--primary-color);
                padding: 1rem;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links ul {
                flex-direction: column;
                width: 100%;
            }

            .nav-links ul li {
                width: 100%;
                padding: 0.5rem 0;
            }

            .nav-icons {
                margin: 1rem 0;
                justify-content: center;
            }

            .invoice-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo-container">
                    <img src="https://cdn-icons-png.flaticon.com/512/1374/1374128.png" alt="Shop Now Logo" class="logo-img">
                    <a href="index.php" class="logo">SHOP NOW</a>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="nav-links" id="navLinks">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                    <div class="nav-icons">
                        <a href="cart.php" class="nav-icon"><i class="fas fa-shopping-cart"></i></a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="profile-container">
                                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="profile-image">
                                <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                <div class="dropdown">
                                    <a href="logout.php">Logout</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="nav-icon"><i class="fas fa-user"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="invoice-container">
                <div class="invoice-header">
                    <h1>Order Confirmation</h1>
                    <p>Thank you for your purchase!</p>
                </div>

                <div class="invoice-details">
                    <div class="invoice-section">
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                        <p><strong>Estimated Delivery Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'] . ' + 3 days')); ?></p>
                    </div>

                    <div class="invoice-section">
                        <h3>Shipping Details</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer']['name'] ?? 'N/A'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer']['email'] ?? 'N/A'); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer']['phone'] ?? 'N/A'); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['customer']['address'] ?? 'N/A'); ?></p>
                    </div>
                </div>

                <div class="invoice-items">
                    <h3>Order Items</h3>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="invoice-item">
                            <div><?php echo htmlspecialchars($item['name']); ?></div>
                            <div>$<?php echo number_format($item['price'], 2); ?></div>
                            <div><?php echo $item['quantity']; ?></div>
                            <div>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="invoice-total">
                        <p>Total: $<?php echo number_format($order['total'], 2); ?></p>
                    </div>
                </div>
                <h4><strong>* Cash On Delivery Option Only Available Right Now</strong></h4>

                <div class="invoice-actions">
                    <a href="javascript:window.print()" class="print-btn">Print Invoice</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navLinks = document.getElementById('navLinks');

            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            });

            document.addEventListener('click', function(event) {
                if (!navLinks.contains(event.target) && !menuToggle.contains(event.target)) {
                    navLinks.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    </script>
</body>
</html>