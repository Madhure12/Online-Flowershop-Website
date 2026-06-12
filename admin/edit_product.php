<?php 
// Include common helper functions (auth check, redirect, DB connection, escaping)
require '../includes/functions.php';

// Check whether current user is an admin
// If not admin, redirect to admin index page
if(!is_admin()) redirect('../admin/index.php');

// Get product ID from URL and cast to integer for safety
$id = (int)$_GET['id'];

// Fetch the product data from database
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

// If product does not exist, redirect back to dashboard
if(!$product) redirect('dashboard.php');

// Check if the edit form has been submitted
if($_POST){

    // Get updated product name from form
    $name = $_POST['name'];

    // Get updated price and cast to float
    $price = (float)$_POST['price'];

    // Get updated stock quantity and cast to integer
    $stock = (int)$_POST['stock'];

    // Get updated discount percentage
    $discount = (int)$_POST['discount'];

    // Check if a new image is uploaded
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        // Create a unique filename using timestamp
        $img = time() . "_" . $_FILES['image']['name'];

        // Temporary uploaded file path
        $tmp = $_FILES['image']['tmp_name'];

        // Move uploaded image to assets/images directory
        move_uploaded_file($tmp, "../assets/images/$img");

        // If an old image exists, delete it from the server
        if($product['image'] && file_exists("../assets/images/".$product['image'])){
            unlink("../assets/images/".$product['image']);
        }

    } else {
        // If no new image uploaded, keep the old image
        $img = $product['image'];
    }

    // Prepare SQL update statement for product data
    $stmt = $conn->prepare(
        "UPDATE products 
         SET name=?, price=?, stock=?, image=?, discount=? 
         WHERE id=?"
    );

    // Bind parameters to prepared statement
    // s = string, d = double, i = integer
    $stmt->bind_param("sdisii", $name, $price, $stock, $img, $discount, $id);

    // Execute the update query
    $stmt->execute();

    // Redirect back to dashboard after successful update
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Page title -->
    <title>Edit Product</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Form group spacing */
        .form-group { 
            margin: 15px 0; 
        }

        /* Label styling */
        .form-group label { 
            display: block; 
            font-weight: bold; 
            margin-bottom: 5px; 
            color: #333; 
        }

        /* Input field styling */
        .form-group input, 
        .form-group select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }

        /* Current image wrapper */
        .current-img { 
            margin: 10px 0; 
        }

        /* Product image preview */
        .current-img img { 
            width: 120px; 
            border-radius: 8px; 
        }
    </style>
</head>
<body>

<!-- Main container -->
<div style="max-width:500px;margin:50px auto;padding:20px;background:#fff;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.1);">

    <!-- Page heading -->
    <h2 style="text-align:center;color:#e84393;">Edit Product</h2>

    <!-- Edit product form -->
    <!-- enctype required for image upload -->
    <form method="post" enctype="multipart/form-data">

        <!-- Product name field -->
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?= h($product['name']) ?>" required>
        </div>

        <!-- Price field -->
        <div class="form-group">
            <label>Price (৳)</label>
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
        </div>

        <!-- Stock quantity field -->
        <div class="form-group">
            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
        </div>

        <!-- Discount percentage field -->
        <div class="form-group">
            <label>Discount (%)</label>
            <input type="number" name="discount" value="<?= $product['discount'] ?>" min="0" max="90">
        </div>

        <!-- Display current product image -->
        <div class="form-group">
            <label>Current Image</label>
            <div class="current-img">
                <img src="../assets/images/<?= h($product['image']) ?>" alt="Current">
            </div>
        </div>

        <!-- Upload new image (optional) -->
        <div class="form-group">
            <label>New Image (optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn" style="width:100%;background:#333;">
            Update Product
        </button>
    </form>

    <br>

    <!-- Back to dashboard button -->
    <a href="dashboard.php" class="btn" style="width:100%;background:#666;text-align:center;display:block;">
        Back to Dashboard
    </a>
</div>

</body>
</html>
