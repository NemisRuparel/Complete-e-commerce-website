<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();

$conn = new mysqli('localhost', 'root', '', 'shopnow');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data if logged in
$user_name = '';
$profile_image = 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; // Default profile image
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($user_name, $db_profile_image);
    $stmt->fetch();
    $stmt->close();
    if ($db_profile_image) {
        $profile_image = $db_profile_image;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$senderEmail = 'nemisruparel07@gmail.com';
$senderName = 'SHOP NOW';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(strip_tags($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags($_POST['subject'] ?? ''));
    $message = htmlspecialchars(strip_tags($_POST['message'] ?? ''));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMessage = "<strong>Error:</strong> All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "<strong>Error:</strong> Invalid email address provided.";
    } else {
        $mailToAdmin = new PHPMailer(true);

        try {
            // Admin email setup
            $mailToAdmin->isSMTP();
            $mailToAdmin->Host = 'smtp.gmail.com';
            $mailToAdmin->SMTPAuth = true;
            $mailToAdmin->Username = $senderEmail;
            $mailToAdmin->Password = 'pcqvyjskgachbrjy';
            $mailToAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailToAdmin->Port = 587;
            $mailToAdmin->setFrom($senderEmail, $senderName);
            $mailToAdmin->addAddress($senderEmail, 'Recipient');
            $mailToAdmin->addReplyTo($email, $name);
            $mailToAdmin->isHTML(true);
            $mailToAdmin->Subject = $subject;
            $mailToAdmin->Body = '
                <div style="font-family: Arial, sans-serif; color: #333;">
                    <h2 style="color: #007BFF;">New Contact Form Submission</h2>
                    <p><strong>Name:</strong> ' . $name . '</p>
                    <p><strong>Email:</strong> ' . $email . '</p>
                    <p><strong>Subject:</strong> ' . $subject . '</p>
                    <p><strong>Message:</strong> ' . nl2br($message) . '</p>
                </div>
            ';
            $mailToAdmin->send();

            // User auto-response email setup
            $mailToUser = new PHPMailer(true);
            $mailToUser->isSMTP();
            $mailToUser->Host = 'smtp.gmail.com';
            $mailToUser->SMTPAuth = true;
            $mailToUser->Username = $senderEmail;
            $mailToUser->Password = 'pcqvyjskgachbrjy';
            $mailToUser->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailToUser->Port = 587;
            $mailToUser->setFrom($senderEmail, $senderName);
            $mailToUser->addAddress($email, $name);
            $mailToUser->isHTML(true);
            $mailToUser->Subject = 'Thank You for Contacting SHOP NOW';
            $mailToUser->Body = '
                <div style="font-family: Arial, sans-serif; color: #333;">
                    <h2 style="color: #007BFF;">Thank You, ' . $name . '!</h2>
                    <p>We have received your message and will get back to you soon. Here’s a copy of what you sent:</p>
                    <p><strong>Subject:</strong> ' . $subject . '</p>
                    <p><strong>Message:</strong> ' . nl2br($message) . '</p>
                    <p>If you have any urgent questions, feel free to reply to this email or call us at +1 (555) 123-4567.</p>
                    <p>Best regards,<br>The SHOP NOW Team</p>
                </div>
            ';
            $mailToUser->send();

            $successMessage = "Your message has been sent successfully. Check your inbox for a confirmation!";
        } catch (Exception $e) {
            $errorMessage = "<strong>Error:</strong> Message could not be sent.<br><em>Mailer Error:</em> " . htmlspecialchars($mailToAdmin->ErrorInfo ?: $mailToUser->ErrorInfo);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - SHOP NOW</title>
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

        .contact-container {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 10px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .contact-header p {
            color: var(--light-text);
            font-size: 1.1rem;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .contact-form {
            background: var(--primary-color);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            color: var(--text-color);
            background-color: var(--secondary-color);
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: var(--text-color);
        }

        .contact-info {
            padding: 1rem;
        }

        .contact-info h2 {
            color: var(--accent-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .contact-info ul {
            list-style: none;
        }

        .contact-info ul li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-color);
        }

        .contact-info ul li i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            .contact-content {
                grid-template-columns: 1fr;
            }

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
            <div class="contact-container">
                <div class="contact-header">
                    <h1>Contact Us</h1>
                    <p>We're here to help! Reach out with any questions or feedback.</p>
                </div>

                <?php if ($successMessage): ?>
                    <div class="message success">
                        <strong>Success!</strong><br>
                        <?php echo $successMessage; ?>
                    </div>
                <?php elseif ($errorMessage): ?>
                    <div class="message error">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <div class="contact-content">
                    <div class="contact-form">
                        <form method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea name="message" id="message" required></textarea>
                            </div>
                            <button type="submit" class="submit-btn">Send Message</button>
                        </form>
                    </div>

                    <div class="contact-info">
                        <h2>Get in Touch</h2>
                        <ul>
                            <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                            <li><i class="fas fa-envelope"></i> support@shopnow.com</li>
                            <li><i class="fas fa-map-marker-alt"></i> 123 Shopping St, Retail City</li>
                        </ul>
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