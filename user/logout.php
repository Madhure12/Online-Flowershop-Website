<?php
require '../includes/functions.php';
if(is_logged_in()){
    unset($_SESSION['user_id']);
}
redirect('../index.php');
?>