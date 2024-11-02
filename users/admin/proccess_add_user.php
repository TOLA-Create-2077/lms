<?php
// Include the necessary files and start the session
include_once('../../include/session.php');
include('../../conn_db1.php'); // Ensure this file creates the $pdo connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if expected fields are present in the POST request
    $required_fields = ['first_name', 'last_name', 'username', 'email', 'phone_number', 'password', 'confirm_password', 'department_id', 'role'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Missing field: $field");
        }
    }

    // Retrieve and sanitize the form inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $department_id = $_POST['department_id'];
    $role = $_POST['role'];

    // Validate the password and confirm password
    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle the image upload if a file is selected
    $image_url = null;
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        $image_name = basename($_FILES['image_url']['name']);
        $target_dir = "../../uploads/"; // Make sure this directory exists
        $target_file = $target_dir . $image_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
            $image_url = $target_file;
        } else {
            die('Error uploading the image.');
        }
    }

    // Check if $pdo exists, if not, establish a new PDO connection
    if (!isset($pdo)) {
        $host = 'localhost';
        $db = 'lms';
        $user = 'root';
        $pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Prepare the SQL query to insert the new user into user_info
    $sql = "INSERT INTO user_info (first_name, last_name, username, email, phone_number, password, department_id, role, image_url, created_at) 
            VALUES (:first_name, :last_name, :username, :email, :phone_number, :password, :department_id, :role, :image_url, NOW())";

    $stmt = $pdo->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':department_id', $department_id);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':image_url', $image_url);

    if ($stmt->execute()) {
        // Redirect to manage_users.php with a success message in Khmer
        header('Location: manage_users.php?success=បន្ថែមអ្នកប្រើប្រាស់ថ្មីដោយជោគជ័យ');
        exit();
    } else {
        // Redirect with an error message
        header('Location: manage_users.php?error=មានបញ្ហាក្នុងការបន្ថែមអ្នកប្រើ');
        exit();
    }
}
