<?php 
// Include external functions file which contains helper functions like is_admin() and redirect()
require '../includes/functions.php'; 

// Check if the current user is an admin; if not, redirect to the home page
if(!is_admin()) redirect('index.php');

// Check if the URL contains 'approve' GET parameter
if(isset($_GET['approve'])){
    // Cast the 'approve' parameter to integer to prevent SQL injection
    $oid = (int)$_GET['approve'];

    // Update the status of the order with the given ID to 'approved' in the database
    $conn->query("UPDATE orders SET status='approved' WHERE id=$oid");
}

// Fetch all orders joined with their user's email, ordered by most recent first
$orders = $conn->query("SELECT o.*,u.email FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"> <!-- Set character encoding to UTF-8 -->
<title>Admin Orders</title> <!-- Page title -->
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Make page responsive -->

<style>
/* General body styles */
body {
    font-family: Arial, sans-serif;
    padding: 2rem;
    background: #f9f9f9;
}

/* Page heading styles */
h2 {
    font-size: 28px;
    margin-bottom: 20px;
    text-align: center;        
    color: #e91e63; /* Pink color */
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse; /* Remove spacing between cells */
    background: #fff; /* White background */
    box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Subtle shadow */
}

/* Table header and cell styles */
table th, table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd; /* Light gray line between rows */
    font-size: 16px;
    line-height: 1.6; 
    word-break: break-word; /* Handle long text */
}

/* Table header background and text color */
table th {
    background: #e91e63; /* Pink */
    color: #fff; /* White text */
}

/* Row hover effect */
tr:hover {
    background: #f1f1f1;
}

/* Button styles */
.btn {
    display: inline-block;
    padding: 8px 12px;
    background: #b584b8ff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

/* Button hover effect */
.btn:hover {
    background: #791592ff;
}
</style>
</head>
<body>

<!-- Page heading -->
<h2>Orders</h2>

<!-- Orders table -->
<table>
<tr>
    <th>ID</th> <!-- Order ID -->
    <th>User</th> <!-- User email -->
    <th>Total</th> <!-- Total amount -->
    <th>Type</th> <!-- Delivery type -->
    <th>Date</th> <!-- Delivery date -->
    <th>Pay</th> <!-- Payment method and transaction ID -->
    <th>Status</th> <!-- Order status -->
    <th>Action</th> <!-- Approve button if pending -->
</tr>

<?php 
// Loop through each order fetched from database
while($o=$orders->fetch_assoc()): ?>
<tr>
    <td><?=$o['id']?></td> <!-- Display order ID -->
    <td><?=$o['email']?></td> <!-- Display user email -->
    <td>৳<?=$o['total']?></td> <!-- Display total amount with currency -->
    <td><?=$o['delivery_type']?></td> <!-- Display delivery type -->
    <td><?=$o['delivery_date']?></td> <!-- Display delivery date -->
    <td>
        <?=$o['payment_method']?> <!-- Display payment method -->
        <?php if($o['bkash_tranid']): ?> <!-- Check if bKash transaction ID exists -->
            <br>(<?=$o['bkash_tranid']?>) <!-- Display bKash transaction ID -->
        <?php endif; ?>
    </td>
    <td><?=$o['status']?></td> <!-- Display order status -->
    <td>
        <?php if($o['status']=='pending'): ?> <!-- Show approve button only for pending orders -->
        <a href="?approve=<?=$o['id']?>" class="btn">Approve</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
