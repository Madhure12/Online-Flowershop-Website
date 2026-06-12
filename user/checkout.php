<?php
// Include helper functions (login check, redirect, escaping, etc.)
require '../includes/functions.php';

// Include database connection
require '../includes/db.php';

// If user is not logged in, redirect to login page
if (!is_logged_in()) redirect('login.php');

// Get total amount from URL (fallback to 0)
$total = (float)($_GET['total'] ?? 0);

// Message container for errors / success
$msg = '';

// Handle form submission
if ($_POST) {

    // Collect and sanitize user inputs
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);           
    $address  = trim($_POST['address']);
    $del_type = $_POST['delivery_type'];
    $pay      = $_POST['payment'];

    // bKash transaction ID validation (only if payment is bkash)
    $tranid   = ($pay == 'bkash' && strlen($_POST['tranid'] ?? '') >= 12)
                ? trim($_POST['tranid'])
                : null;

    // Delivery date logic based on delivery type
    $del_date = ($del_type == 'fast') 
                ? date('Y-m-d') 
                : $_POST['del_date'];

    // Basic validation
    if (empty($name) || empty($phone) || empty($address)) {

        $msg = "<p style='color:red;'>All fields are required!</p>";

    } elseif ($pay == 'bkash' && empty($tranid)) {

        $msg = "<p style='color:red;'>bKash Transaction ID required!</p>";

    } else {

        // Insert order into orders table
        $stmt = $conn->prepare("
            INSERT INTO orders 
            (user_id, total, delivery_type, delivery_date, payment_method, bkash_tranid, 
             customer_name, customer_phone, customer_address, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->bind_param(
            "idsssssss",
            $_SESSION['user_id'],
            $total,
            $del_type,
            $del_date,
            $pay,
            $tranid,
            $name,
            $phone,
            $address
        );

        $stmt->execute();

        // Get newly created order ID
        $order_id = $conn->insert_id;

        // Fetch cart items for the logged-in user
        $cart = $conn->query("
            SELECT cart.*, products.price
            FROM cart
            JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = ".$_SESSION['user_id']
        );

        // Insert each cart item into order_items table
        while ($c = $cart->fetch_assoc()) {

            $stmt2 = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            // Calculate item total price (qty × unit price)
            $price = $c['quantity'] * $c['price'];

            $stmt2->bind_param(
                "iiid",
                $order_id,
                $c['product_id'],
                $c['quantity'],
                $price
            );

            $stmt2->execute();
        }

        // Clear user's cart after successful order
        $conn->query("DELETE FROM cart WHERE user_id = ".$_SESSION['user_id']);

        // Success message
        $msg = "
        <div style='color:green; font-size:1.2rem; padding:20px; background:#e8f5e9; border-radius:10px;'>
            Order placed successfully!<br>
            Order ID: <strong>#$order_id</strong><br>
            Wait for admin approval.
        </div>
        <br><a href='../index.php' class='btn'>Back to Home</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Main site stylesheet -->
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* Page background and layout */
body {
    background: linear-gradient(135deg, #fdf2f8, #fce4ec);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Checkout container */
.checkout-box {
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Page heading */
h2 {
    text-align: center;
    color: #e91e63;
}

/* Total amount box */
.total {
    text-align: center;
    font-size: 1.8rem;
    color: #e91e63;
    background: #fce4ec;
    padding: 15px;
    border-radius: 15px;
    margin-bottom: 25px;
}

/* Form layout helpers */
.form-group { margin: 15px 0; }
.form-group label { display:block; margin-bottom:6px; }

/* Input styling */
.box {
    width: 100%;
    padding: 12px;
    border-radius: 50px;
    border: 1.5px solid #ddd;
}
textarea.box { border-radius: 15px; }

/* Radio button groups */
.radio-group label { display:block; margin:10px 0; }

/* Submit button */
.btn {
    width:100%;
    padding:14px;
    border:none;
    border-radius:50px;
    background:#e91e63;
    color:#fff;
    font-weight:bold;
    cursor:pointer;
}
.btn:hover { background:#c2185b; }

/* Utility class */
.hidden { display:none; }
</style>

<script>
// Show delivery date picker (Normal delivery)
function showCal() {
    const d = document.getElementById('del_date');
    document.getElementById('cal').style.display = 'block';
    d.disabled = false;
    d.required = true;
}

// Hide delivery date picker (Fast delivery)
function hideCal() {
    const d = document.getElementById('del_date');
    document.getElementById('cal').style.display = 'none';

    d.required = false;
    d.disabled = true;   // Important fix: prevent validation error
}

// Show bKash transaction input
function showTran() {
    document.getElementById('tran').style.display = 'block';
}

// Hide bKash transaction input
function hideTran() {
    document.getElementById('tran').style.display = 'none';
}
</script>

</head>

<body>

<div class="checkout-box">

<!-- Page title -->
<h2>Checkout</h2>

<!-- Display total amount -->
<div class="total">Total: ৳<?= number_format($total, 2) ?></div>

<?php if ($msg): ?>
    <!-- Show success or error message -->
    <?= $msg ?>
<?php else: ?>

<!-- Checkout form -->
<form method="post">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" class="box" required>
</div>

<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone" class="box" required>
</div>

<div class="form-group">
<label>Delivery Address</label>
<textarea name="address" class="box" rows="3" required></textarea>
</div>

<!-- Delivery type selection -->
<div class="radio-group">
<label>
<input type="radio" name="delivery_type" value="normal" checked onclick="showCal()">
Normal Delivery (3-5 days)
</label>

<label>
<input type="radio" name="delivery_type" value="fast" onclick="hideCal()">
Fast Delivery (Today)
</label>
</div>

<!-- Delivery date picker -->
<div id="cal">
<label>Delivery Date</label>
<input type="date"
       id="del_date"
       name="del_date"
       min="<?= date('Y-m-d', strtotime('+3 days')) ?>"
       class="box">
</div>

<!-- Payment method selection -->
<div class="radio-group">
<label>
<input type="radio" name="payment" value="cash" checked onclick="hideTran()">
Cash on Delivery
</label>

<label>
<input type="radio" name="payment" value="bkash" onclick="showTran()">
bKash
</label>
</div>

<!-- bKash transaction ID -->
<div id="tran" class="hidden">
<label>bKash Transaction ID</label>
<input type="text" name="tranid" class="box" minlength="12">
</div>

<!-- Submit button -->
<button type="submit" class="btn">Confirm Order</button>

</form>
<?php endif; ?>

</div>

</body>
</html>
