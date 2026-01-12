<?php
require_once 'config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

// Require login (redirect to login page if not logged in)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if already logged in
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}
?>
