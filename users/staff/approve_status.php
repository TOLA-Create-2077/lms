<?php
// reject_status.php

session_start(); // Start session to access session variables

// Include the database configuration (adjust the path as necessary)
include('../../include/db_config.php');

// Database connection details
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Create a connection to the database using PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ការតភ្ជាប់ទៅមូលដ្ឋានទិន្នន័យបានបរាជ័យ: ' . $e->getMessage()];
    header("Location: leave_manage.php"); // Redirect to leave management page
    exit();
}

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the POST data
    $leaveId = isset($_POST['leave_id']) ? trim($_POST['leave_id']) : '';
    $approvedBy = $_SESSION['user_id']; // Get the ID of the user approving the leave

    // Basic validation
    if (empty($leaveId)) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'លេខសម្គាល់នៃសំណើបញ្ឈប់ គឺចាំបាច់សម្រាប់ការបដិសេធ។'];
        header("Location: leave_manage.php");
        exit();
    }

    if (!is_numeric($leaveId)) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'លេខសម្គាល់សំណើបញ្ឈប់មិនត្រឹមត្រូវ។'];
        header("Location: leave_manage.php");
        exit();
    }

    try {
        // Prepare SQL statement to update the leave request status to 'Approved'
        $updateSql = "UPDATE leave_requests SET status = 'អនុញ្ញាត', approved_by = :approved_by, updated_at = NOW() WHERE id = :id";
        $stmt = $conn->prepare($updateSql);
        $stmt->execute(['approved_by' => $approvedBy, 'id' => $leaveId]);

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'សំណើត្រូវបានអនុញ្ញាតដោយជោគជ័យ។'];
        } else {
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'មិនមានការផ្លាស់ប្តូរណាមួយទេ។ សូមពិនិត្យលេខសម្គាល់។'];
        }
    } catch (PDOException $e) {
        // Handle SQL errors
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ការអនុញ្ញាតសំណើបានបរាជ័យ: ' . $e->getMessage()];
    }

    // Redirect to leave management page
    header("Location: leave_manage.php");
    exit();
} else {
    // If accessed without POST data, redirect back
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'វិធីសាស្រ្តសំណើមិនត្រឹមត្រូវ។'];
    header("Location: leave_manage.php");
    exit();
}
