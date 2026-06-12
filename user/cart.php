<?php 
// Include helper functions (login check, redirect, escaping, etc.)
require '../includes/functions.php'; 

// Include database connection
require '../includes/db.php';

// If user is not logged in, redirect to home page
if(!is_logged_in()) redirect('../index.php');

// Get logged-in user ID from session
$uid = $_SESSION['user_id'];

// Fetch cart items for the logged-in user along with product details
$cart = $conn->query("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $uid
");

// Initialize total price
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Main site stylesheet -->
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* Cart table styling */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px; 
}

/* Ensure rows behave like normal table rows */
table tr {
    display: table-row;
}

/* Table cell styling */
table th,
table td {
    display: table-cell !important;
    vertical-align: middle;
    text-align: center;
    padding: 12px;
}

/* Product image inside cart */
.cart-img {
    width: 70px;
    height: 70px;
    object-fit: cover;      
    border-radius: 10px;    
    display: block;
    margin: 0 auto;
    border: 1px solid #eee;
}
</style>
</head>

<body>

<div style="padding:2rem;">

<!-- Cart heading -->
<h2 class="heading">Your <span>Cart</span></h2>

<?php if($cart->num_rows == 0): ?>
    <!-- Message when cart is empty -->
    <p>Your cart is empty. <a href="../products.php">Shop now</a></p>

<?php else: ?>

<!-- Cart items table -->
<table>
    <tr style="background:#333;color:#fff;">
        <th>Image</th>
        <th>Name</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th>Remove</th>
    </tr>

    <?php 
    // Loop through each cart item
    while($item = $cart->fetch_assoc()): 
        // Calculate subtotal for this item
        $sub = $item['quantity'] * $item['price']; 

        // Add to total amount
        $total += $sub; 
    ?>
    <tr style="border-bottom:1px solid #ddd;">
        <td>
            <!-- Product image -->
            <img src="../assets/images/<?= h($item['image']) ?>"
                 class="cart-img"
                 alt="<?= h($item['name']) ?>">
        </td>

        <!-- Product name -->
        <td><?= h($item['name']) ?></td>

        <!-- Quantity -->
        <td><?= $item['quantity'] ?></td>

        <!-- Single item price -->
        <td>৳<?= $item['price'] ?></td>

        <!-- Subtotal price -->
        <td>৳<?= $sub ?></td>

        <!-- Remove item link -->
        <td>
            <a href="remove_cart.php?id=<?= $item['id'] ?>"
               onclick="return confirm('Remove?')"
               style="color:red; font-weight:bold;">
               X
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

    <!-- Total row -->
    <tr>
        <td colspan="4" style="text-align:right;"><strong>Total</strong></td>
        <td><strong>৳<?= $total ?></strong></td>
        <td></td>
    </tr>
</table>

<br>

<!-- Checkout button with total passed via URL -->
<a href="checkout.php?total=<?= $total ?>" class="btn">
    Proceed to Checkout
</a>

<?php endif; ?>

<br><br>

<!-- Continue shopping button -->
<a href="../products.php" class="btn" style="background:#666;">
    Continue Shopping
</a>

</div>

</body>
</html>
