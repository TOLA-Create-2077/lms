<?php
// បញ្ចូលការគ្រប់គ្រងអត្ថបទ
include('../../include/session.php');

// ព័ត៌មានការតភ្ជាប់មូលដ្ឋាន
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// ចាប់ផ្តើមការតភ្ជាប់
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("ការតភ្ជាប់បរាជ័យ: " . $conn->connect_error);
}

// ដោះស្រាយការបញ្ជូនទម្រង់សម្រាប់ការផ្លាស់ប្ដូរពាសពោះ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // គិតថាអ្នករក្សាទុក user_id ក្នុងសេសសងសម្រាប់បន្ទាន់ក្រោយពីចូល
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // ពិនិត្យមើលថា ពាសពោះថ្មី និង ពាសពោះបញ្ជាក់ដូចគ្នា
    if ($newPassword !== $confirmPassword) {
        $_SESSION['statuswrongpassword'] = "ពាក្យសម្ងាត់ថ្មី និងបញ្ជាក់ត្រូវតែដូចគ្នា។";
        header("Location: view_profile.php?user_id=" . urlencode($user_id));
        exit;
    }

    // ទាញយកពាក្យសម្ងាត់បច្ចុប្បន្នពីមូលដ្ឋាន
    $sql = "SELECT password FROM user_info WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashedPassword = $user['password'];

        // បញ្ជាក់ពាក្យសម្ងាត់បច្ចុប្បន្ន
        if (password_verify($currentPassword, $hashedPassword)) {
            // ពិនិត្យមើលថា ពាក្យសម្ងាត់ថ្មីត្រូវជាមួយពាក្យសម្ងាត់បច្ចុប្បន្នឬអត់
            if (password_verify($newPassword, $hashedPassword)) {
                $_SESSION['statuswrongpassword'] = "អ្នកបានដាក់ពាក្យសម្ងាត់ដូចទៅនឹងពាក្យសម្ងាត់ចាស់របស់អ្នក";
                header("Location: view_profile.php?user_id=" . urlencode($user_id));
                exit;
            }

            // ធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់នៅក្នុងមូលដ្ឋាន
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE user_info SET password=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newHashedPassword, $user_id);
            $stmt->execute();

            $_SESSION['status'] = "បានធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់បានដោយជោគជ័យ។";
            header("Location: view_profile.php?user_id=" . urlencode($user_id));
            exit;
        } else {
            $_SESSION['statuswrongpassword'] = "ពាក្យសម្ងាត់បច្ចុប្បន្នមិនត្រឹមត្រូវទេ។";
            header("Location: view_profile.php?user_id=" . urlencode($user_id));
            exit;
        }
    } else {
        $_SESSION['statuswrongpassword'] = "មិនមានអ្នកប្រើប្រាស់ទេ។";
        header("Location: view_profile.php?user_id=" . urlencode($user_id));
        exit;
    }

    // បិទសេចក្ដីថ្លែង
    $stmt->close();
}

// បិទការតភ្ជាប់មូលដ្ឋាន
$conn->close();
