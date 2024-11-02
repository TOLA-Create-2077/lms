<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include session and database connection
include('../../include/session_admin.php');

// Database connection function
function getConnection()
{
    $host = 'localhost';
    $dbname = 'lms';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("មិនអាចភ្ជាប់ទៅកាន់មូលដ្ឋានទិន្នន័យបានទេ: " . $e->getMessage());
    }
}

// Update Telegram data
function updateTelegramData($id, $token, $chat_id)
{
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE telegram_data SET token = :token, chat_id = :chat_id WHERE id = :id");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the form
    $id = $_GET['id'] ?? null; // Make sure to pass the ID from the query string
    $token = $_POST['token'] ?? null;
    $chat_id = $_POST['chat_id'] ?? null;

    // Validate the ID
    if ($id && is_numeric($id)) {
        // Attempt to update the data
        if (updateTelegramData($id, $token, $chat_id)) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'ទិន្នន័យតេលេក្រាមបានកែប្រែដោយជោជ័យ'];
            header('Location: view_telegram_data.php?id=' . $id); // Redirect to view page on success
            exit();
        } else {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'មិនអាចកែប្រែទិន្នន័យបានទេ។'];
        }
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'លេខសម្គាល់មិនត្រឹមត្រូវ។'];
    }

    // Redirect to the management page or the previous page
    header('Location: manage_telegram_data.php'); // Adjust this to your redirect target
    exit();
} else {
    // Handle the case when accessed without POST request
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'វិធីសាស្ត្រសំណើមិនត្រឹមត្រូវ។'];
    header('Location: manage_telegram_data.php'); // Adjust this to your redirect target
    exit();
}
