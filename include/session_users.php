<?php
session_start();



// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php"); // Redirect to login page if not logged in
    exit();
}
// Check the role of the logged-in user
switch ($_SESSION['role']) {
    case 'admin':
        // Redirect admin users to their dashboard or admin page
        header("Location: ../admin/dashboard.php"); // Change this to the desired admin page
        exit();

    case 'staff':
        // Allow access for staff users
        // You can place staff-specific content or redirection here
        break;

    case 'user':
        // Allow access for regular users
        // You can place user-specific content or redirection here
        break;

    default:
        // Redirect unauthorized users to the 404 page
        header("Location: 404.php");
        exit();
}

// The rest of your code for staff or user goes here...
