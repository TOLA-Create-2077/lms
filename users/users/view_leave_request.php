<?php

include('../../include/session_users.php');
include('../../include/sidebar.php'); // Include your sidebar if necessary
include('../../include/functions.php'); // Include any additional functions you may need

// Function to retrieve leave request details
function getLeaveDetails($leave_id, $user_id)
{
    include('../../conn_db.php'); // Database connection

    try {
        // Prepare the SQL statement to fetch leave details with necessary joins
        $sql = "SELECT 
                    lr.id AS leave_id,
                    lr.user_id,
                    submitter.first_name AS submitter_first_name,
                    submitter.last_name AS submitter_last_name,
                    lr.fromDate AS from_date,
                    lr.toDate AS to_date,
                    lr.total_days,
                    lr.reason,
                    lr.status,
                    lr.comment,
                    lr.date_send,
                    lr.approved_by,
                    approver.first_name AS approver_first_name,
                    approver.last_name AS approver_last_name,
                    lr.department_id,
                    departments.department_name,
                    lr.updated_at
                 
                FROM 
                    leave_requests lr
                JOIN 
                    user_info submitter ON lr.user_id = submitter.user_id
                LEFT JOIN 
                    user_info approver ON lr.approved_by = approver.user_id
                LEFT JOIN 
                    departments ON lr.department_id = departments.department_id
                WHERE 
                    lr.id = :leave_id 
                    AND lr.user_id = :user_id"; // Ensures users can only view their own leave requests

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the leave request
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log the error message (consider logging to a file in production)
        error_log("Database Error: " . $e->getMessage());
        return null;
    }
}

// Check if 'id' parameter is set and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $leave_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in and has a user_id

    // Retrieve leave details
    $leaveDetails = getLeaveDetails($leave_id, $user_id);

    if (!$leaveDetails) {
        // If no leave request is found or the user doesn't have access
        $error_message = "សំណើនេះមិនមានទេ។"; // "This request does not exist."
    }
} else {
    // Invalid or missing 'id' parameter
    $error_message = "ID មិនត្រឹមត្រូវ!"; // "Invalid ID!"
}
?>
<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ព័ត៌មានលម្អិតសំណើច្បាប់ឈប់សម្រាក</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Khmer OS Battambang';
            src: url('../assets/fonts/KhmerOSBattambang-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Khmer OS Battambang', sans-serif;
        }

        /* Optional: Adjust the table styling */
        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-inner"><br>
            <!-- Back Button -->
            <div class="mb-3">
                <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                    <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
            </div>
            <!-- Display Alert Messages -->
            <?php if (isset($_SESSION['alert'])): ?>
                <?php
                $alert = $_SESSION['alert'];
                $alertType = htmlspecialchars($alert['type']);
                $alertMessage = htmlspecialchars($alert['message']);
                ?>
                <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $alertMessage; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
                <a href="leave_requests.php" class="btn btn-primary">ត្រលប់ទៅបញ្ជីសំណើ</a> <!-- "Back to Requests List" -->
            <?php elseif ($leaveDetails): ?>
                <!-- Display Leave Details -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">ព័ត៌មានលម្អិតសំណើច្បាប់ឈប់សម្រាក</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ឈ្មោះ</th>
                                <td><?php echo htmlspecialchars($leaveDetails['submitter_first_name']) . ' ' . htmlspecialchars($leaveDetails['submitter_last_name']); ?></td>
                            </tr>
                            <tr>
                                <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                <td><?php
                                    $fromDate = new DateTime($leaveDetails['from_date']);
                                    echo $fromDate->format('d/m/Y'); // Format to dd/mm/yyyy
                                    ?></td>
                            </tr>
                            <tr>
                                <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                <td><?php
                                    $toDate = new DateTime($leaveDetails['to_date']);
                                    echo $toDate->format('d/m/Y'); // Format to dd/mm/yyyy
                                    ?></td>
                            </tr>
                            <tr>
                                <th>ចំនួនថ្ងៃ</th>
                                <td><?php echo htmlspecialchars($leaveDetails['total_days']); ?></td>
                            </tr>
                            <tr>
                                <th>មូលហេតុ</th>
                                <td><?php echo htmlspecialchars($leaveDetails['reason']); ?></td>
                            </tr>
                            <tr>
                                <th>ស្ថានភាព</th>
                                <td>
                                    <span class='badge bg-<?php
                                                            echo ($leaveDetails['status'] == 'Approved') ? 'success' : (($leaveDetails['status'] == 'Rejected') ? 'danger' : (($leaveDetails['status'] == 'Canceled') ? 'secondary' : 'warning'));
                                                            ?>'>
                                        <?php echo htmlspecialchars($leaveDetails['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>បានបញ្ជូននៅថ្ងៃ</th>
                                <td> <?php
                                        if (!empty($leaveDetails['updated_at'])) {
                                            $updatedAt = new DateTime($leaveDetails['date_send']);
                                            echo $updatedAt->format('d/m/Y H:i:s'); // Format to dd/mm/yyyy HH:mm:ss
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?></td>
                            </tr>
                            <tr>
                                <th>ផ្នែក</th>
                                <td><?php echo htmlspecialchars($leaveDetails['department_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>បានធ្វើបច្ចុប្បន្នភាពនៅ</th>
                                <td> <?php
                                        if (!empty($leaveDetails['updated_at'])) {
                                            $updatedAt = new DateTime($leaveDetails['updated_at']);
                                            echo $updatedAt->format('d/m/Y H:i:s'); // Format to dd/mm/yyyy HH:mm:ss
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                </td>
                            </tr>
                            <tr>
                                <th>បានអនុម័តដោយ</th>
                                <td>
                                    <?php
                                    if (!empty($leaveDetails['approved_by'])) {
                                        echo htmlspecialchars($leaveDetails['approver_first_name']) . ' ' . htmlspecialchars($leaveDetails['approver_last_name']);
                                    } else {
                                        echo 'N/A'; // Or any appropriate placeholder
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>មតិយោបល់</th>
                                <td><?php echo htmlspecialchars($leaveDetails['comment']); ?></td>
                            </tr>
                        </table>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>