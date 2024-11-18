<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php"); // Redirect to login page if not logged in
    exit();
}

// Check user role
$role = $_SESSION['role'];
