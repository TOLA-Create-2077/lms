<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the logged-in user is not an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: 404.php"); // Redirect non-admin users to unauthorized page
    exit();
}
