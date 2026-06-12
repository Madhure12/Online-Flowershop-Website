<?php 

require '../includes/functions.php';
require '../includes/db.php'; 

$step = $_GET['step'] ?? 1;
$err = $msg = '';

// Session error message
if (isset($_SESSION['error_msg'])) {
    $err = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// ============ STEP 1 → Email check ============
if ($step == 1 && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim(strtolower($_POST['email']));
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['temp_email'] = $email;
        header("Location: forgot.php?step=2");
        exit;
    } else {
        $err = "Email not found!";
    }
}

// ============ STEP 2 → Security Answer ============
if ($step == 2) {
    // Session check – expired হলে step 1 এ পাঠাও
    if (empty($_SESSION['temp_email'])) {
        $_SESSION['error_msg'] = "Session expired. Please try again.";
        header("Location: forgot.php?step=1");
        exit;
    }
    $email = $_SESSION['temp_email'];

    // Answer submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['answer'])) {
        $answer = trim($_POST['answer']);
        $stmt = $conn->prepare("SELECT favorite_flower FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row && strtolower($answer) === strtolower($row['favorite_flower'])) {
            $_SESSION['reset_email'] = $email;
            unset($_SESSION['temp_email']);
            header("Location: forgot.php?step=3");
            exit;
        } else {
            $err = "Wrong answer!";
        }
    }
}

// ============ STEP 3 → New Password ============
if ($step == 3) {
    if (empty($_SESSION['reset_email'])) {
        $_SESSION['error_msg'] = "Session expired. Please start over.";
        header("Location: forgot.php?step=1");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['newpass'])) {
        $email = $_SESSION['reset_email'];
        $newpass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $newpass, $email);
        $stmt->execute();

        unset($_SESSION['reset_email']);
        header("Location: forgot.php?step=4");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Flower Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
<style>
        body { background: linear-gradient(135deg, #fdf2f8, #fce4ec); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .form-container { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); max-width: 420px; width: 100%; text-align: center; }
        h2 { color: #e91e63; margin-bottom: 10px; }
        p { color: #777; margin-bottom: 20px; }
        .box { width: 100%; padding: 14px 18px; border: 1.5px solid #ddd; border-radius: 50px; font-size: 1rem; margin: 10px 0; }
        .box:focus { outline: none; border-color: #e91e63; box-shadow: 0 0 0 3px rgba(233,30,99,0.1); }
        .btn { background: #e91e63; color: white; padding: 14px; border: none; border-radius: 50px; width: 100%; font-weight: bold; cursor: pointer; margin-top: 15px; font-size: 1.1rem; }
        .btn:hover { background: #c2185b; }
        .error { color: #c2185b; background: #fdecea; padding: 12px; border-radius: 10px; margin: 15px 0; font-size: 0.95rem; }
        .success { color: green; background: #e8f5e9; padding: 20px; border-radius: 15px; margin: 20px 0; font-size: 1.1rem; }
        a { color: #e91e63; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">

        <?php if ($step == 1): ?>
        <h2>Recover Password</h2>
        <p>Enter your registered email</p>
        <?php if ($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <form method="post" action="forgot.php?step=1">
            <input 
                type="email" 
                name="email" 
                placeholder="user@gmail.com" 
                class="box" 
                required 
                autocapitalize="off"
                autocorrect="off"
                spellcheck="false"
                style="text-transform: none !important;">
            <button type="submit" class="btn">Next →</button>
        </form>

    <?php elseif ($step == 2): ?>
        <h2>Security Question</h2>
        <p>What is your favorite flower?</p>
        <?php if ($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <form method="post" action="forgot.php?step=2">
            <input type="text" name="answer" placeholder="Your favorite flower?" class="box" required autocomplete="off">
            <button type="submit" class="btn">Verify</button>
        </form>

    <?php elseif ($step == 3): ?>
        <h2>Set New Password</h2>
        <p>Enter your new password</p>
        <form method="post" action="forgot.php?step=3">
            <input type="password" name="newpass" class="box" placeholder="New password (6+ chars)" minlength="6" required>
            <button type="submit" class="btn">Update Password</button>
        </form>

    <?php elseif ($step == 4): ?>
        <h2>Success!</h2>
        <div class="success">
            Password changed successfully!<br><br>
            <a href="login.php">Back to Login</a>
        </div>
    <?php endif; ?>


</div>

</body>
</html>