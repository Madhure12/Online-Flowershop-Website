<?php 
// Include common functions (login check, session helpers, etc.)
require 'includes/functions.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding -->
    <meta charset="UTF-8">

    <!-- Responsive layout for mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Website title shown in browser tab -->
    <title>Online Flowershop</title>

    <!-- Font Awesome CDN for icons (heart, cart, user, stars, etc.) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    

<!-- ================= HEADER SECTION START ================= -->

<header>

    <!-- Checkbox used for responsive navbar toggle -->
    <input type="checkbox" id="toggler">

    <!-- Hamburger menu icon -->
    <label for="toggler" class="fas fa-bars"></label>

    <!-- Website logo -->
    <a href="index.php" class="logo">flower<span>.</span></a>

    <!-- Navigation menu -->
    <nav class="navbar">
        <a href="index.php">home</a>
        <a href="#about">about</a>
        <a href="products.php">products</a> 
        <a href="#review">review</a>
        <a href="#contact">contact</a>
    </nav>

    <!-- Header icons section -->
    <div class="icons">  

        <!-- Wishlist icon -->
        <a href="user/wishlist.php" class="fas fa-heart"></a>

        <!-- Cart icon -->
        <a href="user/cart.php" class="fas fa-shopping-cart"></a>

        <!-- If user is logged in, show logout icon -->
        <?php if(is_logged_in()): ?>
            <a href="user/logout.php" class="fas fa-user"></a>
        <?php else: ?>
        <!-- If user is not logged in, show login icon -->
            <a href="user/login.php" class="fas fa-user"></a>
        <?php endif; ?>
    </div>

</header>
<!-- ================= HEADER SECTION END ================= -->



<!-- ================= HOME SECTION START ================= -->

<section class="home" id="home">

    <!-- Home page content -->
    <div class="content">
        <h3>fresh flowers</h3>
        <span> natural & beautiful flowers </span>

        <!-- Intro text -->
        <p>
            Lorem ipsum dolor sit amet consecctetur adipisicing elit. 
            Beatae laborum ut minus corrupti dolorum assumenda iste voluptate dolorem partiatur.
        </p>

        <!-- Shop Now button -->
        <a href="products.php" class="btn">shop now</a>  
    </div>

</section>

<!-- ================= HOME SECTION END ================= -->



<!-- ================= ABOUT SECTION START ================= -->

<section class="about" id="about">

    <!-- Section heading -->
    <h1 class="heading"> <span> about </span> us </h1>

    <div class="row">

        <!-- Video container -->
        <div class="video-container">

            <!-- Background video -->
            <video 
                src="image/Girl walking On a Garden full Of Flowers _ Mountains Vlogs _ Nature Shots20 _ Copyright FREE.mp4"
                loop autoplay muted>
            </video>

            <!-- Video overlay text -->
            <h3>best flower sellers</h3>
        </div>

        <!-- About text content -->
        <div class="content">
            <h3>why choose us?</h3>

            <!-- Description paragraph -->
            <p>
                We offer the freshest flowers, carefully handpicked every day 
                to ensure top quality and long-lasting beauty.
                We believe everyone deserves beautiful flowers, 
                so we keep our prices fair and reasonable.
            </p>

            <p>
                Our team is always ready to help you choose the perfect flowers 
                and make your experience special.
            </p>

            <!-- Learn more button -->
            <a href="#" class="btn">learn more</a>
        </div>

    </div>

</section>
 
<!-- ================= ABOUT SECTION END ================= -->



<!-- ================= ICONS SECTION START ================= -->

<section class="icons-container">

    <!-- Free delivery icon -->
    <div class="icons">
        <img src="image/icon1-removebg-preview.png" alt="">
        <div class="info">
            <h3>free delivery</h3>
            <span>on all orders</span>
        </div>
    </div>

    <!-- Return policy icon -->
    <div class="icons">
        <img src="image/icon2-removebg-preview.png" alt="">
        <div class="info">
            <h3>10 days returns</h3>
            <span>moneyback guarantee</span>
        </div>
    </div>

    <!-- Offers icon -->
    <div class="icons">
        <img src="image/icon3-removebg-preview.png" alt="">
        <div class="info">
            <h3>offer & gifts</h3>
            <span>on all orders</span>
        </div>
    </div>

    <!-- Secure payment icon -->
    <div class="icons">
        <img src="image/icon4-removebg-preview.png" alt="">
        <div class="info">
            <h3>secure payments</h3>
            <span>protected by visa cards</span>
        </div>
    </div>

</section>
<!-- ================= ICONS SECTION END ================= -->



<!-- ================= REVIEW SECTION START ================= -->

<section class="review" id="review">

<h1 class="heading"> customer's <span>review</span></h1>

<div class="box-container">

    <!-- Review box 1 -->
    <div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>

        <p>Those flower are fresh</p>

        <div class="user">
            <img src="image/vernon.jpeg" alt="">
            <div class="user-info">
                <h3>vernon</h3>
                <span>happy customer</span>
            </div>
        </div>

        <span class="fas fa-quote-right"></span>
    </div>

    <!-- Review box 2 -->
    <div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>

        <p>nice and attractive</p>

        <div class="user">
            <img src="image/download.jpeg" alt="">
            <div class="user-info">
                <h3>Mohammed Shahabuddin</h3>
                <span>happy customer</span>
            </div>
        </div>

        <span class="fas fa-quote-right"></span>
    </div>

</div>

</section>

<!-- ================= REVIEW SECTION END ================= -->



<!-- ================= FOOTER / CONTACT SECTION START ================= -->

<section class="contact" id="contact">

    <div class="box-container">

        <!-- Quick links -->
        <div class="box">
            <h3>quick links</h3>
            <a href="index.php">home</a>
            <a href="#about">about</a>
            <a href="products.php">products</a>
            <a href="#review">review</a>
            <a href="#contact">contact</a>
        </div>

        <!-- User related links -->
        <div class="box">
            <h3>extra links</h3>
            <a href="user/login.php">my account</a>
            <a href="user/cart.php">my order</a>
            <a href="user/wishlist.php">my favorite</a>
        </div>

        <!-- Location info -->
        <div class="box">
            <h3>locations</h3>
            <a href="#">dhaka</a>
        </div>

        <!-- Contact information -->
        <div class="box">
            <h3>contact</h3>
            <a href="#">+8801789456785</a>
            <a href="#">flowershop@gmail.com</a>
            <a href="#">motijheel , dhaka</a>

            <!-- Payment methods image -->
            <img src="image/bkash-rocket-nagad.png" alt="">
        </div>

    </div>

    <!-- Footer credit -->
    <div class="credit">
        created by <span> Madhure Mondal</span> | all rights reserved
    </div>

</section>

<!-- ================= FOOTER SECTION END ================= -->

</body>
</html>
