<?php 
require '../includes/functions.php';
if(!is_logged_in()) redirect('login.php');

$uid = $_SESSION['user_id'];

// Remove from wishlist
if(isset($_GET['remove'])){
    $pid = (int)$_GET['remove'];
    $conn->query("DELETE FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    redirect('wishlist.php');
}

$wishlist = $conn->query("SELECT w.*, p.name, p.price, p.image, p.discount FROM wishlist w JOIN products p ON w.product_id=p.id WHERE w.user_id=$uid");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div style="padding:2rem;">
    <h2 class="heading">My <span>Wishlist</span></h2>

    <?php if($wishlist->num_rows == 0): ?>
        <p>Your wishlist is empty. <a href="../products.php">Add items</a></p>
    <?php else: ?>
        <div class="box-container">
            <?php while($item = $wishlist->fetch_assoc()): 
                $final_price = $item['price'] * (100 - $item['discount']) / 100;
            ?>
                <div class="box" style="width:300px;display:inline-block;margin:1rem;vertical-align:top;">
                    <img src="../assets/images/<?= h($item['image']) ?>" width="100%">
                    <h3><?= h($item['name']) ?></h3>
                    <div class="price">৳<?= number_format($final_price, 2) ?>
                        <?php if($item['discount']>0): ?><span>৳<?= $item['price'] ?></span><?php endif; ?>
                    </div>
                    <a href="?remove=<?= $item['product_id'] ?>" class="btn" style="background:#e84393;" onclick="return confirm('Remove?')">Remove</a>
                    <a href="../products.php#add_cart=1&pid=<?= $item['product_id'] ?>&qty=1" class="btn">Add to Cart</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <br><br>
    <a href="../products.php" class="btn" style="background:#666;">Continue Shopping</a>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>