<?php 
require '../includes/functions.php';  
if(!is_admin()) redirect('index.php');

if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $img = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc()['image'];
    $conn->query("DELETE FROM products WHERE id = $id");
    if($img && file_exists("../assets/images/$img")) unlink("../assets/images/$img");
    redirect('dashboard.php');
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 1rem; background: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .heading { font-size: 2rem; color: #e84393; text-align: center; margin-bottom: 1rem; }
        .btn-group { margin-bottom: 1.5rem; text-align: center; }
        .btn-group a { margin: 0 0.5rem; padding: 0.8rem 1.5rem; border-radius: 30px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th { background: #333; color: white; padding: 1rem; }
        td { padding: 1rem; text-align: center; border-bottom: 1px solid #eee; }
        td img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .action-btn { padding: 0.5rem 1rem; margin: 0 0.3rem; border-radius: 20px; font-size: 1.5rem; text-decoration: none; color: white; }
        .edit-btn { background: #131313ff; }
        .delete-btn { background: #3c8ce7ff; }
        .logout-btn { display: block; width: 150px; margin: 2rem auto 0; padding: 0.8rem; background: #e74c3c; color: white; text-align: center; border-radius: 30px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading">Manage <span>Products</span></h1>

    <div class="btn-group">
        <a href="add_product.php" class="btn">Add New Product</a>
        <a href="orders.php" class="btn">View Orders</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Discount</th>
            <th>Actions</th>
        </tr>
        <?php while($p = $products->fetch_assoc()): ?>
        <tr>
            <td><?= h($p['id']) ?></td>
            <td><img src="../assets/images/<?= h($p['image']) ?>" alt=""></td>
            <td><?= h($p['name']) ?></td>
            <td>৳<?= number_format($p['price'], 2) ?></td>
            <td><?= h($p['stock']) ?></td>
            <td><?= h($p['discount']) ?>%</td>
            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="action-btn edit-btn">Edit</a>
                <a href="?delete=<?= $p['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>