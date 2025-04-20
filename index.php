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
    <title>SHOP NOW - Premium Online Shopping</title>
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

        .search-bar {
            display: flex;
            align-items: center;
            background: var(--secondary-color);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            width: 300px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.5rem;
            outline: none;
        }

        .search-bar button {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-color);
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

        .hero-section {
            height: 80vh;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            margin-bottom: 3rem;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--light-text);
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-3px);
        }

        .featured-products {
            padding: 5rem 0;
            background-color: var(--primary-color);
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--accent-color);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--primary-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }

        .product-price {
            font-size: 1.1rem;
            color: var(--light-text);
            margin-bottom: 1rem;
        }

        .add-to-cart {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: var(--text-color);
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

            .search-bar {
                width: 100%;
                margin-top: 1rem;
            }

            .nav-icons {
                margin: 1rem 0;
                justify-content: center;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
                                <img src="<?php echo $profile_image ?>" alt="Profile" class="profile-image">
                                <span class="user-name"><?php echo $user_name ?></span>
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
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1>Welcome to SHOP NOW</h1>
                    <p>Discover amazing products at unbeatable prices. Shop with confidence and enjoy a seamless shopping experience.</p>
                    <a href="shop.php" class="cta-button">Start Shopping</a>
                </div>
            </div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2 class="section-title">Featured Products</h2>
                <div class="products-grid">
                    <div class="product-card">
                        <img src="assets/images/kitchen_knife_set.jpg" class="product-image" alt="Kitchen Knife Set">
                        <div class="product-info">
                            <h3 class="product-title">Kitchen Knife Set</h3>
                            <p class="product-price">$15.99</p>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="product_id" value="1">
                                <input type="hidden" name="product_name" value="Kitchen Knife Set">
                                <input type="hidden" name="product_price" value="15.99">
                                <input type="hidden" name="product_image" value="assets/images/kitchen_knife_set.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <div class="product-card">
                        <img src="assets/images/bath_towel_set.jpg" class="product-image" alt="Bath Towel Set">
                        <div class="product-info">
                            <h3 class="product-title">Bath Towel Set</h3>
                            <p class="product-price">$8.99</p>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="product_id" value="2">
                                <input type="hidden" name="product_name" value="Bath Towel Set">
                                <input type="hidden" name="product_price" value="8.99">
                                <input type="hidden" name="product_image" value="assets/images/bath_towel_set.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <div class="product-card">
                        <img src="assets/images/cleaning_supplies_kit.jpg" class="product-image" alt="Cleaning Supplies Kit">
                        <div class="product-info">
                            <h3 class="product-title">Cleaning Supplies Kit</h3>
                            <p class="product-price">$12.99</p>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="product_id" value="3">
                                <input type="hidden" name="product_name" value="Cleaning Supplies Kit">
                                <input type="hidden" name="product_price" value="12.99">
                                <input type="hidden" name="product_image" value="assets/images/cleaning_supplies_kit.jpg">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
                if (navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
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