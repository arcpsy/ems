<?php
session_start();
require_once 'auth_functions.php';

if (isLoggedIn()) {
    logout();
} else {
    header('Location: login.php');
    exit();
}
?>