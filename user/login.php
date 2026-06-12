<?php 
require '../includes/functions.php';
require '../includes/db.php';

if (is_logged_in()) redirect('../index.php');

$err = '';
if ($_POST) {
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($password, $res['password'])) {
        $_SESSION['user_id'] = $res['id'];
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['message'] = "Welcome back, " . h($res['name']) . "!";
        redirect('../products.php');
    } else {
        $err = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Flower Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #fdf2f8, #fce4ec);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: #fff;
            padding: 15px 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #e91e63;
            text-decoration: none;
        }
        .logo span { color: #333; }
        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }
        .nav-links a:hover { color: #e91e63; }
        .icons a {
            margin-left: 20px;
            font-size: 1.4rem;
            color: #555;
            position: relative;
        }
        .icons a:hover { color: #e91e63; }

        /* Login Form */
        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 10px;
            color: #e91e63;
            font-size: 1.9rem;
        }
        .login-box p {
            color: #777;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            transition: 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }
        .btn {
            background: #e91e63;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background: #c2185b;
        }
        .error {
            background: #fdecea;
            color: #c2185b;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .links {
            margin-top: 25px;
            font-size: 0.95rem;
        }
        .links a {
            color: #e91e63;
            text-decoration: none;
            font-weight: 500;
        }
        .links a:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #fff;
            color: #777;
            font-size: 0.9rem;
            margin-top: auto;
        }
        /* Force lowercase email */
        input[type="email"], 
        input#email {
        text-transform: none !important;
        font-variant: normal !important;
        text-transform: lowercase !important;
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <div class="navbar">
        <a href="../index.php" class="logo">Flower<span>.</span></a>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../#products">Products</a>
            <a href="../#contact">Contact</a>
        </div>
        <div class="icons">
            <a href="wishlist.php" class="fas fa-heart"></a>
            <a href="cart.php" class="fas fa-shopping-cart"></a>
            <?php if (is_logged_in()): ?>
                <a href="logout.php" class="fas fa-user"></a>
            <?php else: ?>
                <a href="login.php" class="fas fa-user"></a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Login Form -->
<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        <p>Enter your email and password to continue.</p>

        <?php if ($err): ?>
            <div class="error"><?= $err ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">

            <label for="email">Email</label>
            <input 
            type="email" 
            name="email" 
            id="email" 
            required 
            placeholder="user@gmail.com" 
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            autocomplete="email"
            inputmode="email"
            style="text-transform: none !important; font-variant: normal !important;"
            value="<?= h(strtolower($_POST['email'] ?? '')) ?>">
            </div>

            <!-- password secttion starts-->

            <div class="form-group">
            <label for="password">Password</label>
            <input 
            type="password" 
            name="password" 
            id="password" 
            required 
            placeholder="••••••••"
            autocomplete="current-password">
            </div>

            <!-- password secttion ends-->


            <button type="submit" class="btn">Login Now</button>
        </form>

        <div class="links">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            <p><a href="forgot.php">Forgot Password?</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </div>
</div>



<!-- Footer -->
<footer>
    &copy; 2025 Flower Shop. Created by <strong>Madhure Mondal</strong>. All rights reserved.
</footer>

</body>
</html>