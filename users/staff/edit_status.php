<?php
session_start(); // ចាប់ផ្តើមសម័យដើម្បីគ្រប់គ្រងស្ថានភាពការចូលរបស់អ្នកប្រើ

// ព័ត៌មានតភ្ជាប់ទៅមូលដ្ឋានទិន្នន័យ
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ការតភ្ជាប់បានបរាជ័យ: " . $e->getMessage());
}

// ពិនិត្យមើលប្រសិនបើទិន្នន័យនៅក្នុងសំណុំបែបបទត្រូវបានបញ្ជូន
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ទទួលបាន ID លុប់ និងស្ថានភាពថ្មីពីសំណុំបែបបទ POST
    $leave_id = $_POST['leave_id'];
    $new_status = $_POST['status'];
    $comment = $_POST['comment']; // វាលបន្ថែមសម្រាប់យោបល់

    // ផ្ទៀងផ្ទាត់ការបញ្ចូល
    if (empty($leave_id) || empty($new_status)) {
        // កំណត់សារជូនដំណឹងសម្រាប់កំហុសផ្ទៀងផ្ទាត់
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'លេខ ID លុប់ និងស្ថានភាពមិនអាចទទេបានទេ។'];
        header("Location: leave_manage.php");
        exit();
    }

    // ទទួលបានស្ថានភាពបច្ចុប្បន្ននៃសំណើលុប់មុនពេលធ្វើបច្ចុប្បន្នភាព
    $sql_get_status = "SELECT status FROM leave_requests WHERE id = :leave_id";
    $stmt = $conn->prepare($sql_get_status);
    $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);
    $stmt->execute();
    $current_status = $stmt->fetchColumn(); // ទទួលបានស្ថានភាពបច្ចុប្បន្ន

    // ពិនិត្យមើលប្រសិនបើស្ថានភាពថ្មីគឺខុសពីស្ថានភាពបច្ចុប្បន្ន
    if ($current_status === $new_status) {
        // កំណត់សារជូនដំណឹងប្រសិនបើស្ថានភាពដូចគ្នា
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'ស្ថានភាពត្រូវបានកំណត់ទៅ ' . $new_status . ' រួចហើយ។'];
        header("Location: leave_manage.php");
        exit();
    }

    // រៀបចំសេចក្ដីថ្លែងការណ៍ SQL ដើម្បីធ្វើបច្ចុប្បន្នភាពស្ថានភាពសំណើលុប់
    $sql_update = "UPDATE leave_requests SET status = :status, comment = :comment, updated_at = NOW() WHERE id = :leave_id";

    try {
        $stmt = $conn->prepare($sql_update);
        $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        // ប្រតិបត្តិការបច្ចុប្បន្នភាព
        if ($stmt->execute()) {
            // កំណត់សារជូនដំណឹងដោយផ្អែកលើការផ្លាស់ប្តូរស្ថានភាព
            if ($current_status === 'Approve' && $new_status === 'Reject') {
                $_SESSION['alert'] = ['type' => 'info', 'message' => 'ស្ថានភាពលុបត្រូវបានផ្លាស់ប្តូរពី "អនុម័ត" ទៅ "បដិសេធ" ។'];
            } elseif ($current_status === 'Reject' && $new_status === 'Approve') {
                $_SESSION['alert'] = ['type' => 'info', 'message' => 'ស្ថានភាពលុបត្រូវបានផ្លាស់ប្តូរពី "បដិសេធ" ទៅ "អនុម័ត" ។'];
            } else {
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'ស្ថានភាពត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ។'];
            }
        } else {
            // កំណត់សារជូនដំណឹងសម្រាប់ការបរាជ័យក្នុងការប្រតិបត្តិការ
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'ការធ្វើបច្ចុប្បន្នភាពស្ថានភាពលុបបានបរាជ័យ។'];
        }
    } catch (PDOException $e) {
        // កំណត់សារជូនដំណឹងនៅក្នុងករណីមានកំហុស
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'កំហុសទិន្នន័យ: ' . $e->getMessage()];
        error_log("Database error: " . $e->getMessage()); // កំណត់កំហុសសម្រាប់ការកែសម្រួល
    }

    // បញ្ជូនចូលទៅផ្នែកគ្រប់គ្រងការលុបវិញ
    header("Location: leave_manage.php");
    exit();
}
