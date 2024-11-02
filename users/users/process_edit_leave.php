<?php
session_start(); // Start the session

// Include database connection file
include('../../conn_db.php'); // Ensure this file has the $conn variable

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $leave_id = $_POST['leave_id'] ?? null;
    $fromDate = $_POST['fromDate'] ?? null;
    $toDate = $_POST['toDate'] ?? null;
    $reason = $_POST['reason'] ?? null;

    // Assume user_id is stored in session after login
    $user_id = $_SESSION['user_id'] ?? null;

    // Validate input data
    if (empty($leave_id) || empty($fromDate) || empty($toDate) || empty($reason) || empty($user_id)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "ដូចទិន្នន័យចាស់របស់អ្នក" // All fields are required
        ];
        header("Location: leave_list.php");
        exit;
    }

    // Convert dates to DateTime objects for comparison
    $fromDateObj = new DateTime($fromDate);
    $toDateObj = new DateTime($toDate);

    // Validate toDate (must be within 2 days)
    if ($toDateObj < $fromDateObj || $toDateObj > $fromDateObj->modify('+2 days')) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "ថ្ងៃចុងបញ្ចប់ត្រូវតែបញ្ចូលនៅក្នុង ២ ថ្ងៃពីថ្ងៃចាប់ផ្តើម។" // End date must be within 2 days from start date
        ];
        header("Location: leave_list.php");
        exit;
    }

    // Check for overlapping leave requests for the same user with status 'Pending' or 'Approved'
    $sql_check = "SELECT COUNT(*) FROM leave_requests 
                  WHERE user_id = :user_id 
                  AND status IN ('កំពុងរងចាំ', 'អនុញ្ញាត')
                  AND (fromDate <= :to_date AND toDate >= :from_date)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':from_date', $fromDate);
    $stmt_check->bindParam(':to_date', $toDate);
    $stmt_check->execute();
    $leave_count = $stmt_check->fetchColumn();

    if ($leave_count > 0) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'អ្នកបានដាក់សំណើឈប់សម្រាកសម្រាប់ថ្ងៃនេះរួចហើយ។ សូមព្យាយាមម្តងទៀតដោយសំរេចពីថ្ងៃផ្សេងទៀត។'
        ];
        header('Location: leave_list.php');
        exit;
    }

    // Calculate total_days ensuring at least one day is counted
    $total_days = max(($toDateObj->diff($fromDateObj)->days) + 1, 1); // Ensure at least one day

    try {
        // Prepare SQL update statement
        $sql = "UPDATE leave_requests 
                SET fromDate = :fromDate, 
                    toDate = :toDate, 
                    reason = :reason, 
                    total_days = :total_days, 
                    date_send = NOW() 
                WHERE id = :leave_id";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':fromDate', $fromDate);
        $stmt->bindParam(':toDate', $toDate);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':total_days', $total_days, PDO::PARAM_INT); // Use the calculated total_days
        $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "កែប្រែជោគជ័យ។" // Leave request updated successfully
            ];
            header("Location: leave_list.php");
            exit;
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "កំហុសក្នុងការធ្វើបច្ចុប្បន្នភាពកត់ត្រា: " . implode(", ", $stmt->errorInfo())
            ];
            header("Location: leave_list.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "កំហុស: " . $e->getMessage()
        ];
        header("Location: leave_list.php");
        exit;
    }
} else {
    // If form is not submitted, redirect to leave list
    header("Location: leave_list.php");
    exit;
}
