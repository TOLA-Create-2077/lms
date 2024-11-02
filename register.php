<?php
include('conn_db.php'); // Include your database connection

$error = '';
$success = '';

// Fetch departments for the dropdown
$departments = [];
try {
    $deptStmt = $conn->prepare("SELECT department_id, department_name FROM departments");
    $deptStmt->execute();
    $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching departments: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Passwords should not be trimmed to preserve whitespace if any
    $role = trim($_POST['role']);
    $departmentId = $_POST['department_id'];
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);
    $imageUrl = !empty($_POST['image_url']) ? trim($_POST['image_url']) : NULL;
    $createdAt = date('Y-m-d H:i:s');

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($username) || empty($password) || empty($role) || empty($departmentId) || empty($email)) {
        $error = "All required fields are filled out.";
    } else {
        try {
            // Check if department_id exists
            $deptCheckStmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_id = :department_id");
            $deptCheckStmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
            $deptCheckStmt->execute();
            if ($deptCheckStmt->fetchColumn() == 0) {
                $error = "Selected department does not exist.";
            } else {
                // Check if username already exists
                $sql = "SELECT * FROM user_info WHERE username = :username";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "Username already exists.";
                } else {
                    // Hash the password
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    // Insert new user into the database
                    $sql = "INSERT INTO user_info 
                            (first_name, last_name, username, password, role, department_id, email, phone_number, image_url, created_at) 
                            VALUES 
                            (:first_name, :last_name, :username, :password, :role, :department_id, :email, :phone_number, :image_url, :created_at)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':first_name', $firstName);
                    $stmt->bindParam(':last_name', $lastName);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $passwordHash);
                    $stmt->bindParam(':role', $role);
                    $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone_number', $phoneNumber);
                    $stmt->bindParam(':image_url', $imageUrl);
                    $stmt->bindParam(':created_at', $createdAt);

                    if ($stmt->execute()) {
                        $success = "Registration successful!";
                        // Optionally, redirect to a login page or another page
                        // header("Location: login.php");
                        // exit();
                    } else {
                        $error = "An error occurred. Please try again.";
                    }
                }
            }
        } catch (PDOException $e) {
            // Handle potential errors
            $error = "Database error: " . $e->getMessage();
        }
    }
}
