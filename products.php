<?php
require 'includes/functions.php';
require 'includes/db.php';

// Add to Cart
if (is_logged_in() && isset($_POST['add_cart'])) {
    $pid = (int)$_POST['pid'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product && $product['stock'] >= $qty) {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->bind_param("iiii", $uid, $pid, $qty, $qty);
        $stmt->execute();

        $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
        $_SESSION['message'] = "Added to cart!";
    } else {
        $_SESSION['error'] = "Not enough stock!";
    }
    redirect('products.php');
}

// Add to Wishlist
if (is_logged_in() && isset($_POST['add_wishlist'])) {
    $pid = (int)$_POST['pid'];
    $uid = Soares($_SESSION['user_id']);
    $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $uid, $pid);
    $stmt->execute();
    $_SESSION['message'] = "Added to wishlist!";
    redirect('products.php');
}

$cart_counts = [];
if (is_logged_in()) {
    $uid = $_SESSION['user_id'];
    $result = $conn->query("SELECT product_id, quantity FROM cart WHERE user_id = $uid");
    while ($row = $result->fetch_assoc()) {
        $cart_counts[$row['product_id']] = $row['quantity'];
    }
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Flower Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .message, .error {
            padding: 12px; margin: 15px 5%; border-radius: 8px; font-weight: bold; text-align: center;
        }
        .message { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        
        .products {
            padding: 80px 5% 60px;
            background: #fff5fb;
            text-align: center;
        }
        .products .heading {
            font-size: 2.2rem;
            color: #e84393;
            margin-bottom: 50px;
            position: relative;
            display: inline-block;
        }
        .products .heading::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #e84393;
            border-radius: 2px;
        }
        .products .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            padding: 20px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .products .box {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
            text-align: center;
            position: relative;
        }
        .products .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .discount {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #e91e63;
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: bold;
            z-index: 2;
        }

        .image {
            height: 220px;
            overflow: hidden;
            background: #f9f9f9;
        }
        .image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .box:hover .image img {
            transform: scale(1.1);
        }

        .content {
            padding: 20px;
        }
        .content h3 {
            font-size: 1.3rem;
            margin: 10px 0;
            color: #333;
        }
        .price {
            font-size: 1.3rem;
            color: #e91e63;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .price span {
            text-decoration: line-through;
            color: #999;
            font-size: 1rem;
            margin-left: 8px;
        }

        .stock-info {
            font-size: 0.95rem;
            margin: 8px 0;
            font-weight: bold;
        }
        .in-stock { color: #27ae60; }
        .out-of-stock { color: #e74c3c; }

        .cart-info {
            font-size: 0.9rem;
            color: #e91e63;
            margin: 8px 0;
            font-weight: bold;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .qty-input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            max-width: 200px;
        }
        .add-to-cart {
            background: #e91e63;
            color: white;
        }
        .add-to-cart:hover { background: #c2185b; }
        .add-to-wishlist {
            background: #fce4ec;
            color: #e91e63;
        }
        .add-to-wishlist:hover { background: #f8bbd0; color: #c2185b; }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message"><?= h($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="error"><?= h($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<section class="products" id="products">
    <h1 class="heading">Latest <span>Products</span></h1>

    <div class="box-container">
        <?php while ($p = $products->fetch_assoc()): 
            $final_price = $p['price'] * (100 - $p['discount']) / 100;
            $in_cart = $cart_counts[$p['id']] ?? 0;
            $out_of_stock = $p['stock'] <= 0;
        ?>
            <div class="box">
                <?php if ($p['discount'] > 0): ?>
                    <span class="discount">-<?= $p['discount'] ?>%</span>
                <?php endif; ?>

                <div class="image">
                    <img src="assets/images/<?= h($p['image']) ?>" alt="<?= h($p['name']) ?>">
                </div>

                <div class="content">
                    <h3><?= h($p['name']) ?></h3>

                    <div class="price">
                        ৳<?= number_format($final_price, 2) ?>
                        <?php if ($p['discount'] > 0): ?>
                            <span>৳<?= number_format($p['price'], 2) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="stock-info <?= $out_of_stock ? 'out-of-stock' : 'in-stock' ?>">
                        <?= $out_of_stock ? 'Out of Stock' : "Stock: {$p['stock']}" ?>
                    </div>

                    <?php if ($in_cart > 0): ?>
                        <div class="cart-info"><?= $in_cart ?> in cart</div>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <?php if (!$out_of_stock && is_logged_in()): ?>
                            <form method="post">
                                <input type="hidden" name="pid" value="<?= $p['id'] ?>">
                                <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="qty-input">
                                <button type="submit" name="add_cart" class="btn add-to-cart">Add to Cart</button>
                            </form>
                        <?php elseif (is_logged_in()): ?>
                            <form method="post">
                                <input type="hidden" name="pid" value="<?= $p['id'] ?>">
                                <button type="submit" name="add_wishlist" class="btn add-to-wishlist">Add to Wishlist</button>
                            </form>
                        <?php else: ?>
                            <a href="user/login.php" class="btn add-to-cart" style="text-decoration:none;">Login to Add</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>