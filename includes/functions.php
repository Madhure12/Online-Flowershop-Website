<?php
session_start();
require_once 'db.php';

// DB connection (db.php)
if (!isset($conn)) {
    die("Database connection failed. Check includes/db.php");
}

// Helper functions
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}
?>