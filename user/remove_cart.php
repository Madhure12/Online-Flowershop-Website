<?php
require '../includes/functions.php';
if(!is_logged_in()) redirect('../index.php');

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $uid = $_SESSION['user_id'];
    $conn->query("DELETE FROM cart WHERE id = $id AND user_id = $uid");
}
redirect('cart.php');
?>