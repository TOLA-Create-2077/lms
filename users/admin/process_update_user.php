<?php
// នៅដើមនៃឯកសារ​កម្មវិធី
session_start(); // ចាប់ផ្តើមសមាជិក
include('../../conn_db.php'); // ធានាថា ផ្លូវត្រឹមត្រូវ

error_reporting(E_ALL); // អនុញ្ញាតឱ្យរាយការណ៍កំហុស
ini_set('display_errors', 1); // បង្ហាញកំហុស

// ពិនិត្យមើលថាតើទម្រង់បានស្នើសុំរួចឬនៅ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // សំអាត និងទាញយកទិន្នន័យទម្រង់
    $user_id = htmlspecialchars($_POST['user_id']);
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $department_id = htmlspecialchars($_POST['department_id']);
    $role = htmlspecialchars($_POST['role']);

    // ពិនិត្យអាសយដ្ឋានអ៊ីមែល
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ទ្រង់ទ្រាយអ៊ីមែលមិនត្រឹមត្រូវ!'
        ];
        header("Location: manage_users.php");
        exit();
    }

    // រៀបចំសេចក្តីថ្លែងការណ៍ SQL ដើម្បីអាប់ដេតទិន្នន័យអ្នកប្រើ
    $update_sql = "UPDATE user_info SET 
                   first_name = :first_name, 
                   last_name = :last_name, 
                   username = :username, 
                   email = :email, 
                   phone_number = :phone_number, 
                   department_id = :department_id, 
                   role = :role 
                   WHERE user_id = :user_id";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':first_name', $first_name);
    $update_stmt->bindParam(':last_name', $last_name);
    $update_stmt->bindParam(':username', $username);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->bindParam(':phone_number', $phone_number);
    $update_stmt->bindParam(':department_id', $department_id);
    $update_stmt->bindParam(':role', $role);
    $update_stmt->bindParam(':user_id', $user_id);

    // បញ្ចេញការអាប់ដេត និងពិនិត្យមើលថាតើវាសម្រេចបានទេ
    if ($update_stmt->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'បានអាប់ដេតអ្នកប្រើដោយជោគជ័យ!'
        ];
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'កំហុសក្នុងការអាប់ដេតកំណត់ត្រា: ' . $update_stmt->errorInfo()[2]
        ];
        header("Location: manage_users.php");
        exit();
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'សំណើមិនត្រឹមត្រូវ!'
    ];
    header("Location: manage_users.php");
    exit();
}
