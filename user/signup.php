<?php
require '../includes/functions.php';

if (is_logged_in()) redirect('../index.php');

$errors = [];
$success = '';

if ($_POST) {
    $original_email = trim($_POST['email']);
    $email = strtolower($original_email);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm'];
    $flower = trim($_POST['favorite_flower']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    elseif (stripos($original_email, '@gmail') !== false && $original_email !== $email) {
        $errors[] = "Gmail must be lowercase: user@gmail.com (not @Gmail.com)";
    }
    elseif (stripos($original_email, '@gmailcom') !== false) {
        $errors[] = "Invalid Gmail: use user@gmail.com (not @gmailcom)";
    }

    if (strlen($pass1) < 6) $errors[] = "Password must be 6+ characters!";
    if ($pass1 !== $pass2) $errors[] = "Passwords do not match!";
    if (empty($flower)) $errors[] = "Favorite flower is required!";

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "This email is already registered!";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, password, favorite_flower) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hash, $flower);
        if ($stmt->execute()) {
            $success = "Signup successful! <a href='login.php' style='color:#e84393;'>Login now</a>";
        } else {
            $errors[] = "Something went wrong. Try again!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Flower Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(to bottom, #ffe6f2, #fff); 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0;
        }
        .form-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .form-container h2 {
            color: #e84393;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        .box {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }
        .box:focus {
            border-color: #e84393;
            box-shadow: 0 0 8px rgba(232, 67, 147, 0.3);
        }
        .btn {
            background: #e84393;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #c2185b;
        }
        .error, .success {
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .login-link {
            margin-top: 15px;
            font-size: 0.95rem;
        }
        .login-link a {
            color: #e84393;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Sign Up</h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p style="margin:5px 0;">Warning: <?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="email" 
       name="email" 
       placeholder="user@gmail.com" 
       class="box" 
       required 
       autocapitalize="off"
       autocomplete="off"
       style="text-transform: none !important;"
       value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>">

        <input type="password" 
               name="password" 
               placeholder="Password (6+ characters)" 
               class="box" 
               minlength="6" 
               required>

        <input type="password" 
               name="confirm" 
               placeholder="Confirm Password" 
               class="box" 
               required>

        <input type="text" 
               name="favorite_flower" 
               placeholder="Your favorite flower?" 
               class="box" 
               required
               value="<?= isset($_POST['favorite_flower']) ? h($_POST['favorite_flower']) : '' ?>">

        <input type="submit" value="Sign Up" class="btn">
    </form>

    <div class="login-link">
        Have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>