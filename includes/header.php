<!-- includes/header.php -->
<header>
    <a href="index.php" class="logo">flower<span>.</span></a>
    <nav class="navbar">
        <a href="index.php">home</a>
        <a href="products.php">products</a>
        <a href="#contact">contact</a>
    </nav>
    <div class="icons">
        <?php if (is_logged_in()): ?>
            <a href="user/wishlist.php" class="fas fa-heart"></a>
            <a href="user/cart.php" class="fas fa-shopping-cart"></a>
            <a href="user/logout.php" class="fas fa-user"></a>
        <?php else: ?>
            <a href="user/wishlist.php" class="fas fa-heart"></a>
            <a href="user/cart.php" class="fas fa-shopping-cart"></a>
            <a href="user/login.php" class="fas fa-user"></a>
        <?php endif; ?>
    </div>
</header>