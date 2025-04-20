<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();
require 'db_functions.php'; 


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (isset($_POST['add_to_cart'])) {

    if (!isset($_POST['product_id']) || !isset($_POST['product_name']) || 
        !isset($_POST['product_price']) || !isset($_POST['product_image']) ||
        !isset($_POST['quantity'])) {
        $_SESSION['error'] = "Missing product information";
        header('Location: cart.php');
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    $product_name = htmlspecialchars(strip_tags($_POST['product_name']));
    $product_price = floatval($_POST['product_price']);
    $product_image = filter_var($_POST['product_image'], FILTER_SANITIZE_URL);
    $quantity = (int)$_POST['quantity'];

    if ($product_id <= 0 || $product_price < 0 || $quantity < 1 || $quantity > 10) {
        $_SESSION['error'] = "Invalid product data or quantity (1-10 allowed)";
        header('Location: cart.php');
        exit;
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $new_quantity = $item['quantity'] + $quantity;
            if ($new_quantity > 10) {
                $item['quantity'] = 10;
                $_SESSION['warning'] = "Maximum quantity per item is 10";
            } else {
                $item['quantity'] = $new_quantity;
            }
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => $quantity
        ];
    }

    $_SESSION['success'] = "Product added to cart successfully!";
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}


if (isset($_POST['update_quantity'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        $quantity = (int)$qty;
        if ($quantity < 1) $quantity = 1;
        if ($quantity > 10) $quantity = 10;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
    $_SESSION['success'] = "Cart updated successfully!";
    header('Location: cart.php');
    exit;
}


$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}


$showCheckoutPrompt = false;
if (isset($_GET['checkout']) && !isset($_SESSION['user_id'])) {
    $showCheckoutPrompt = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - SHOP NOW</title>
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

        main {
            margin-top: 80px;
            padding: 2rem 0;
        }

        .cart-container {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 10px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .cart-header h1 {
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .cart-items {
            margin-bottom: 2rem;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr 1fr 50px;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-item-title {
            font-weight: bold;
        }

        .cart-item-price {
            color: var(--light-text);
        }

        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            text-align: center;
        }

        .quantity-input::-webkit-inner-spin-button,
        .quantity-input::-webkit-outer-spin-button {
            opacity: 1;
            height: 30px;
        }

        .remove-item {
            color: var(--light-text);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remove-item:hover {
            color: #ff0000;
        }

        .cart-summary {
            background: var(--primary-color);
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
        }

        .cart-total {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: var(--text-color);
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: var(--light-text);
        }

        .update-cart-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-right: 1rem;
        }

        .update-cart-btn:hover {
            background-color: var(--text-color);
        }

        .continue-shopping {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--secondary-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .continue-shopping:hover {
            background-color: var(--border-color);
            color: var(--accent-color);
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            padding: 1rem;
            background-color: var(--secondary-color);
            border-radius: 5px;
        }

        .cart-summary {
            background-color: var(--secondary-color);
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 2rem;
        }

        .cart-total {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: right;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: var(--primary-color);
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: var(--text-color);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .login-prompt {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .login-prompt a {
            color: #004085;
            text-decoration: underline;
        }

        .login-prompt a:hover {
            color: #002752;
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
                <div class="nav-links">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="cart-container">
                <div class="cart-header">
                    <h1>Your Shopping Cart</h1>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['warning'])): ?>
                    <div class="alert alert-warning">
                        <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($showCheckoutPrompt): ?>
                    <div class="login-prompt">
                        <p>You need to be logged in to proceed to checkout. Please <a href="login.php">log in</a> or <a href="signin.php">register</a>.</p>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="empty-cart">
                        <p>Your cart is empty</p>
                        <a href="shop.php" class="continue-shopping">Continue Shopping</a>
                    </div>
                <?php else: ?>
                    <form method="post" action="cart.php">
                        <div class="cart-items">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="cart-item">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                                    <div>
                                        <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="10" class="quantity-input">
                                    </div>
                                    <div>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                    <a href="?remove=<?php echo $item['id']; ?>" class="remove-item">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cart-actions">
                            <div>
                                <button type="submit" name="update_quantity" class="update-cart-btn">Update Cart</button>
                                <a href="shop.php" class="continue-shopping">Continue Shopping</a>
                            </div>
                        </div>
                        <div class="cart-summary">
                            <div class="cart-total">
                                Total: $<?php echo number_format($total, 2); ?>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                            <?php else: ?>
                                <a href="cart.php?checkout=true" class="checkout-btn">Proceed to Checkout</a>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>