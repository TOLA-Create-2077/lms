<?php
include('../../conn_db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $department_name = trim($_POST['department_name']);

    // Check if the department name is empty
    if (empty($department_name)) {
        $_SESSION['status'] = "ឈ្មោះដេប៉ាតឺម៉ង់មិនអាចទទេរបានទេ!";
        $_SESSION['alert_type'] = 'danger';  // Set alert type for empty name
        header('Location: manage_users.php');
        exit();
    }

    try {
        // Check if the department already exists
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_name = :department_name");
        $check_stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
        $check_stmt->execute();
        $existingDepartmentCount = $check_stmt->fetchColumn();

        if ($existingDepartmentCount > 0) {
            // Department name already exists
            $_SESSION['status'] = "ឈ្មោះដេប៉ាតឺម៉ង់មានរួចហើយ!";
            $_SESSION['alert_type'] = 'danger';  // Define alert type as danger
        } else {
            // Insert new department if it doesn't exist
            $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (:department_name)");
            $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['status'] = "ដេប៉ាតឺម៉ង់ត្រូវបានបន្ថែមដោយជោគជ័យ!";
                $_SESSION['alert_type'] = 'success';  // Define alert type for success
            } else {
                $_SESSION['status'] = "មានបញ្ហាក្នុងការបន្ថែមដេប៉ាតឺម៉ង់!";
                $_SESSION['alert_type'] = 'danger';  // Set alert type for general error
            }
        }
    } catch (PDOException $e) {
        $_SESSION['status'] = "កំហុស: " . $e->getMessage();
        $_SESSION['alert_type'] = 'danger';  // Define alert type for errors
    }

    // Redirect back to the manage users page after processing
    header('Location: view_department.php');
    exit();
}
