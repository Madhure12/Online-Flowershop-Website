<?php 
// Include common helper functions (session, auth, redirect, DB connection)
require '../includes/functions.php';

// Check if the current user is an admin
// If not admin, redirect to index page
if(!is_admin()) redirect('index.php');

// Check if the form is submitted using POST method
if($_POST){

    // Get product name from form input
    $name = $_POST['name'];

    // Get product price and cast to float
    $price = (float)$_POST['price'];

    // Get stock quantity and cast to integer
    $stock = (int)$_POST['stock'];

    // Get discount percentage and cast to integer
    $discount = (int)$_POST['discount'];
    
    // Check if image file is uploaded and no upload error occurred
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        // Create unique image name using current timestamp
        $img = time() . "_" . $_FILES['image']['name']; 

        // Temporary file path of uploaded image
        $tmp = $_FILES['image']['tmp_name'];

        // Move uploaded image to assets/images directory
        move_uploaded_file($tmp, "../assets/images/$img");

    } else {
        // If no image uploaded, keep image empty
        $img = '';
    }

    // Prepare SQL query to insert product data securely
    $stmt = $conn->prepare(
        "INSERT INTO products (name, price, stock, image, discount) 
         VALUES (?, ?, ?, ?, ?)"
    );

    // Bind parameters to the prepared statement
    // s = string, d = double, i = integer
    $stmt->bind_param("sdisi", $name, $price, $stock, $img, $discount);

    // Execute the insert query
    $stmt->execute();

    // Redirect admin to dashboard after successful insert
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"> <!-- Character encoding -->
<title>Add Product</title> <!-- Page title -->
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

<!-- External CSS file -->
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* Page background and font */
body {
    font-family: 'Arial', sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

/* Main form container */
.container {
    max-width: 500px;
    margin: 50px auto;
    padding: 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

/* Page heading */
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 25px;
}

/* Input fields styling */
form input[type="text"],
form input[type="number"],
form input[type="file"] {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 16px;
}

/* Input focus effect */
form input[type="number"]:focus,
form input[type="text"]:focus,
form input[type="file"]:focus {
    border-color: #28a745;
    outline: none;
}

/* Button styling */
.btn {
    display: inline-block;
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    font-size: 16px;
    color: #fff;
    background: #e84393;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background 0.3s ease;
}

/* Button hover effect */
.btn:hover {
    background: #bc2d72ff;
}

/* Back button styling */
.back-btn {
    background: #666;
}

.back-btn:hover {
    background: #555;
}
</style>
</head>
<body>

<!-- Main container -->
<div class="container">

    <!-- Page title -->
    <h2>Add New Product</h2>

    <!-- Product add form -->
    <!-- enctype required for file upload -->
    <form method="post" enctype="multipart/form-data">

        <!-- Product name input -->
        <input type="text" name="name" placeholder="Product Name" required>

        <!-- Product price input -->
        <input type="number" step="0.01" name="price" placeholder="Price" required>

        <!-- Stock quantity input -->
        <input type="number" name="stock" placeholder="Stock Quantity" required>

        <!-- Discount percentage input -->
        <input type="number" name="discount" placeholder="Discount %" value="0" min="0" max="90">

        <!-- Product image upload -->
        <input type="file" name="image" accept="image/*" required>

        <!-- Submit button -->
        <input type="submit" value="Add Product" class="btn">
    </form>

    <!-- Back to dashboard button -->
    <a href="dashboard.php" class="btn back-btn">Back to Dashboard</a>
</div>

</body>
</html>
