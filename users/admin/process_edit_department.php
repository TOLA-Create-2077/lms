<?php
include('../../conn_db.php');
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the department ID and name from the POST request
    $department_id = trim($_POST['department_id']);
    $department_name = trim($_POST['department_name']);

    // Validate input
    if (empty($department_name)) {
        $_SESSION['status'] = "ឈ្មោះដេប៉ាតឺម៉ង់មិនអាចទទេរបានទេ!";
        $_SESSION['alert_type'] = 'danger';
        header('Location: view_department.php'); // Redirect back to view department page
        exit();
    }

    try {
        // Check if the department already exists with a different ID
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_name = :department_name AND department_id != :department_id");
        $check_stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
        $check_stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $existingDepartmentCount = $check_stmt->fetchColumn();

        if ($existingDepartmentCount > 0) {
            $_SESSION['status'] = "ឈ្មោះដេប៉ាតឺម៉ង់មានរួចហើយ!";
            $_SESSION['alert_type'] = 'danger';
        } else {
            // Update the department if it doesn't exist
            $stmt = $conn->prepare("UPDATE departments SET department_name = :department_name WHERE department_id = :department_id");
            $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
            $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['status'] = "ដេប៉ាតឺម៉ង់ត្រូវបានអាប់ដេតដោយជោគជ័យ!";
                $_SESSION['alert_type'] = 'success';
            } else {
                $_SESSION['status'] = "មានបញ្ហាក្នុងការអាប់ដេតដេប៉ាតឺម៉ង់!";
                $_SESSION['alert_type'] = 'danger';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['status'] = "កំហុស: " . $e->getMessage();
        $_SESSION['alert_type'] = 'danger';
    }

    // Redirect back to the view department page after processing
    header('Location: view_department.php');
    exit();
} else {
    // Redirect to view department page if accessed directly
    $_SESSION['status'] = "កំហុស: វិធីសាស្ត្រមិនត្រឹមត្រូវ!";
    $_SESSION['alert_type'] = 'danger';
    header('Location: view_department.php');
    exit();
}
