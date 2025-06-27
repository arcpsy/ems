<?php
session_start();
require_once 'auth_functions.php';

// If user is already logged in, redirect to main app
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Redirect to login page
header('Location: login.php');
exit();
?>