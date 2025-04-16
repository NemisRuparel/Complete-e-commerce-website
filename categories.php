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

$conn = get_db_connection();

// Fetch user data if logged in
$user_data = get_user_data($conn, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
$user_name = $user_data['user_name'];
$profile_image = $user_data['profile_image'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - SHOP NOW</title>
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

        .categories-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .categories-header h1 {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .category-card {
            position: relative;
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .category-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--primary-color);
            text-align: center;
            padding: 2rem;
        }

        .category-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .category-description {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .category-link {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: var(--accent-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .category-link:hover {
            background-color: var(--accent-color);
            color: var(--primary-color);
        }

        footer {
            background-color: var(--accent-color);
            color: var(--primary-color);
            padding: 3rem 0;
            margin-top: 3rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary-color);
        }

        .footer-section p {
            color: var(--light-text);
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: var(--light-text);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary-color);
        }

        .footer-section ul li i {
            margin-right: 0.5rem;
            width: 20px;
        }

        .social-links {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .social-links a {
            color: var(--primary-color);
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--light-text);
        }

        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light-text);
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

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .category-card {
                height: 250px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-section {
                text-align: center;
            }

            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
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
                            <a href="signin.php" class="nav-icon"><i class="fas fa-user"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="categories-header">
                <h1>Shop by Category</h1>
                <p>Browse our wide range of product categories</p>
            </div>

            <div class="categories-grid">
                <div class="category-card">
                    <img src="assets/images/kitchen.jpg" class="category-image">
                    <div class="category-overlay">
                        <h2 class="category-title">Kitchen</h2>
                        <p class="category-description">Latest gadgets and devices</p>
                        <a href="shop.php?category=kitchen" class="category-link">Shop Now</a>
                    </div>
                </div>

                <div class="category-card">
                    <img src="assets/images/office.jpg" class="category-image">
                    <div class="category-overlay">
                        <h2 class="category-title">Office</h2>
                        <p class="category-description">Trendy clothing and accessories</p>
                        <a href="shop.php?category=office" class="category-link">Shop Now</a>
                    </div>
                </div>

                <div class="category-card">
                    <img src="assets/images/bath_assentials.jpg" class="category-image">
                    <div class="category-overlay">
                        <h2 class="category-title">Bath Essentials</h2>
                        <p class="category-description">Furniture and home decor</p>
                        <a href="shop.php?category=bathroom" class="category-link">Shop Now</a>
                    </div>
                </div>

                <div class="category-card-hide" style="background-color:#fff;cursor:default;"></div>

                <div class="category-card">
                    <img src="assets/images/cleaning.jpg" class="category-image">
                    <div class="category-overlay">
                        <h2 class="category-title">Cleaning</h2>
                        <p class="category-description">Trendy clothing and accessories</p>
                        <a href="shop.php?category=cleaning" class="category-link">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p>Your trusted online shopping destination for quality products and exceptional service.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> support@shopnow.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Shopping St, Retail City</li>
                    </ul>
                </div>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <p class="copyright">© 2025 SHOP NOW. All rights reserved.</p>
        </div>
    </footer>

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