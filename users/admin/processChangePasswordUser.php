<?php
$host = 'localhost'; // គេហទំព័រទិន្នន័យ
$db = 'lms'; // ឈ្មោះទិន្នន័យ
$user = 'root'; // ឈ្មោះអ្នកប្រើទិន្នន័យ
$pass = ''; // ពាក្យសម្ងាត់ទិន្នន័យ
$charset = 'utf8mb4';

// បង្កើត DSN (ឈ្មោះប្រភពទិន្នន័យ)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // ចាប់ផ្តើម PDO instance
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ទាញយក ID នៃអ្នកប្រើ និងពាក្យសម្ងាត់ថ្មីពីទម្រង់
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ពិនិត្យថាពាក្យសម្ងាត់ថ្មី និងពាក្យសម្ងាត់អះអាងត្រូវគ្នា
    if ($new_password !== $confirm_password) {
        echo "ពាក្យសម្ងាត់មិនត្រូវគ្នាទេ!";
        exit;
    }

    // បង្កើត hash សម្រាប់ពាក្យសម្ងាត់ថ្មី (សូមណែនាំ)
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    try {
        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE user_info SET password = :password WHERE user_id = :user_id");
        $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);

        // Redirect after successful password change
        header('Location: manage_users.php?success=ប្តូរពាក្យសម្ងាត់ជោគជ័យ!'); // Password changed successfully
        exit();
    } catch (PDOException $e) {
        // Redirect with a general error message
        header('Location: manage_users.php?error=មិនអាចផ្លាស់ប្តូរពាក្យសម្ងាត់បានទេ!'); // Failed to change password
        exit();
    }
}
