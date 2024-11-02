<?php
include('../../include/session_users.php');
include('../../conn_db.php'); // Database connection

date_default_timezone_set('Asia/Phnom_Penh');

// Fetch Telegram Bot Configuration from the database
$sql_telegram = "SELECT token, chat_id FROM telegram_data LIMIT 1";
$stmt_telegram = $conn->prepare($sql_telegram);
$stmt_telegram->execute();
$telegram_data = $stmt_telegram->fetch(PDO::FETCH_ASSOC);

$telegram_token = $telegram_data['token']; // Use the token from the database
$chat_id = $telegram_data['chat_id']; // Use the chat ID from the database

// Function to send messages to Telegram
function sendToTelegram($token, $chat_id, $message)
{
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $post_fields = array(
        'chat_id' => $chat_id,
        'text'    => $message,
        'parse_mode' => 'Markdown' // Optional: To format the message
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    // Basic validation
    if (empty($user_id) || empty($from_date) || empty($to_date) || empty($reason)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ទំព័រទាំងអស់ត្រូវតែបំពេញ។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    // Validate date formats
    $from_date_obj = DateTime::createFromFormat('Y-m-d', $from_date);
    $to_date_obj = DateTime::createFromFormat('Y-m-d', $to_date);

    if (!$from_date_obj || !$to_date_obj) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ទ្រង់ទ្រាយកាលបរិច្ឆេទមិនត្រឹមត្រូវ។ សូមប្រើ YYYY-MM-DD។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    // Ensure from_date <= to_date
    if ($from_date_obj > $to_date_obj) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'កាលបរិច្ឆេទ "ចាប់ផ្តើម" ត្រូវតែជាមុនឬស្មើនឹងកាលបរិច្ឆេទ "បញ្ចប់"។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    // Calculate total days
    $total_days = $to_date_obj->diff($from_date_obj)->days + 1; // Inclusive

    if ($total_days <= 0) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ចំនួនថ្ងៃត្រូវតែជាលេខវិជ្ជមាន។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    // Step 1: Calculate total leave days taken in the past year
    $one_year_ago = date('Y-m-d', strtotime('-1 year'));
    $sql_check_leave = "SELECT SUM(total_days) as total_leave_taken FROM leave_requests 
                        WHERE user_id = :user_id 
                        AND status = 'អនុញ្ញាត' 
                        AND fromDate >= :one_year_ago";
    $stmt_check_leave = $conn->prepare($sql_check_leave);
    $stmt_check_leave->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check_leave->bindParam(':one_year_ago', $one_year_ago);
    $stmt_check_leave->execute();
    $leave_taken = $stmt_check_leave->fetchColumn();

    // If there are no records, set leave_taken to 0
    if ($leave_taken === null) {
        $leave_taken = 0;
    }

    // Step 2: Check if the new leave exceeds the 18-day limit
    $remaining_days = 18 - $leave_taken;
    if ($total_days > $remaining_days) {
        // Leave exceeds the limitz
        $note = "សំណើនេះលើសពីដែនកំណត់ត្រឹម 18 ថ្ងៃក្នុងមួយឆ្នាំ។ ចំនួនថ្ងៃដែលមានសមាជិកមិនត្រូវបានអនុញ្ញាតគឺ $remaining_days ថ្ងៃ។";
        $_SESSION['alert'] = [
            'type' => 'warning',
            'message' => 'សំណើឈប់សម្រាកបានលើសពីដែនកំណត់ 18 ថ្ងៃក្នុងមួយឆ្នាំ។'
        ];
    } else {
        $note = null; // No excess
    }

    // Check for overlapping leave requests for the same user with status 'Pending' or 'Approved'
    $sql_check = "SELECT COUNT(*) FROM leave_requests 
                  WHERE user_id = :user_id 
                  AND status IN ('កំពុងរងចាំ', 'អនុញ្ញាត')
                  AND (fromDate <= :to_date AND toDate >= :from_date)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':from_date', $from_date);
    $stmt_check->bindParam(':to_date', $to_date);
    $stmt_check->execute();
    $leave_count = $stmt_check->fetchColumn();

    if ($leave_count > 0) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'អ្នកបានដាក់សំណើឈប់សម្រាកសម្រាប់ថ្ងៃនេះរួចហើយ។ សូមព្យាយាមម្តងទៀតដោយសំរេចពីថ្ងៃផ្សេងទៀត។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    // Fetch department_id and user details from user_info
    $sql_user = "SELECT department_id, first_name, last_name FROM user_info WHERE user_id = :user_id";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_user->execute();
    $user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($user_info === false) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'គ្រូសាស្ត្របច្ចុប្បន្នមិនត្រូវបានរកឃើញ។'
        ];
        header('Location: request_leave.php');
        exit;
    }

    $department_id = $user_info['department_id'];
    $first_name = $user_info['first_name'];
    $last_name = $user_info['last_name'];

    // Fetch department_name from departments
    $sql_dept = "SELECT department_name FROM departments WHERE department_id = :department_id";
    $stmt_dept = $conn->prepare($sql_dept);
    $stmt_dept->bindParam(':department_id', $department_id, PDO::PARAM_INT);
    $stmt_dept->execute();
    $department_name = $stmt_dept->fetchColumn();

    // Current datetime
    $current_datetime = date('Y-m-d H:i:s');

    // Insert the leave request with the note if applicable
    $sql_insert = "INSERT INTO leave_requests 
                (user_id, fromDate, toDate, total_days, reason, status, date_send, approved_by, department_id, note) 
                VALUES 
                (:user_id, :from_date, :to_date, :total_days, :reason, 'កំពុងរងចាំ', :date_send, NULL, :department_id, :note)";

    try {
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':from_date', $from_date);
        $stmt_insert->bindParam(':to_date', $to_date);
        $stmt_insert->bindParam(':total_days', $total_days, PDO::PARAM_INT);
        $stmt_insert->bindParam(':reason', $reason);
        $stmt_insert->bindParam(':date_send', $current_datetime);
        $stmt_insert->bindParam(':department_id', $department_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':note', $note);
        $stmt_insert->execute();

        // Send Telegram notification
        $message = "🔔 **សំណើឈប់សម្រាកថ្មី**\n\n"
            . "👤 **ឈ្មោះ:** $first_name $last_name\n\n"
            . "📅 **កាលបរិច្ឆេទចាប់ផ្តើម:** $from_date\n\n"
            . "📅 **កាលបរិច្ឆេទបញ្ចប់:** $to_date\n\n"
            . "⏳ **ចំនួនថ្ងៃ:** $total_days\n\n"
            . "📋 **មូលហេតុ:** $reason\n\n"
            . "🏢 **ដេប៉ាតឺម៉ង់:** $department_name\n\n"
            . "📌 **សម្គាល់:** $note\n\n";


        sendToTelegram($telegram_token, $chat_id, $message);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'សំណើឈប់សម្រាករបស់អ្នកត្រូវបានដាក់ស្នើដោយជោគជ័យ។'
        ];
        header('Location: request_leave.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'មានបញ្ហាក្នុងការដាក់ស្នើសំណើឈប់សម្រាក។ សូមព្យាយាមម្តងទៀត។'
        ];
        header('Location: request_leave.php');
        exit;
    }
}
