<?php 
require '../includes/functions.php';
if(is_admin()) redirect('dashboard.php');

$error = '';

if($_POST){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $admin = $result->fetch_assoc();
        if(password_verify($password, $admin['password'])){
            $_SESSION['admin_id'] = $admin['id'];
            redirect('dashboard.php');
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/css/style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

.form-container {
    max-width: 400px;
    margin: 100px auto;
    padding: 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 25px;
}

.box {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 16px;
    box-sizing: border-box;
}

.box:focus {
    border-color: #28a745;
    outline: none;
}

.btn {
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #218838;
}

.error-msg {
    color: #e74c3c;
    background: #fdecea;
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="form-container">
    <h2>Admin Login</h2>

    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" class="box" required 
               autocapitalize="off" autocorrect="off" style="text-transform: none;">
        <input type="password" name="password" placeholder="Password" class="box" required>
        <input type="submit" value="Login" class="btn">
    </form>
</div>

</body>
</html>
