<?php
require '../includes/functions.php';
if(!is_admin()) redirect('index.php');

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $img = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc()['image'];
    $conn->query("DELETE FROM products WHERE id=$id");
    if($img && file_exists("../assets/images/$img")) unlink("../assets/images/$img");
}
redirect('dashboard.php');
?>