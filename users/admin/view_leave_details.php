<?php
// view_leave_details.php

// Include session management and sidebar
include('../../include/session.php'); // This includes session_start() and session management
include('../../include/sidebar.php'); // Includes sidebar menu

// Database connection details
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

try {
    // Establish database connection using PDO
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if 'id' is provided in GET request
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'មិនមាន ID សំណើរត្រូវបានផ្តល់ជូន។' // "No leave request ID provided."
    ];
    header("Location: leave_requests.php");
    exit();
}

// Validate and sanitize the 'id'
$leave_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$leave_id) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'ID សំណើរមិនត្រឹមត្រូវ។' // "Invalid leave request ID."
    ];
    header("Location: leave_requests.php");
    exit();
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission for approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ការផ្តល់សិទ្ធិមិនត្រឹមត្រូវ។' // "Invalid CSRF token."
        ];
        header("Location: view_leave_details.php?id=" . $leave_id);
        exit();
    }

    if (isset($_POST['action']) && in_array($_POST['action'], ['approve', 'reject'])) {
        $action = $_POST['action'];
        $comment = trim($_POST['comment'] ?? '');

        // If action is reject, comment is required
        if ($action === 'reject' && empty($comment)) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'សូមបញ្ចូលសេចក្ដីសម្រេចឬមតិយោបល់នៅពេលបដិសេធសំណើ។' // "Please provide a comment when rejecting the leave request."
            ];
            header("Location: view_leave_details.php?id=" . $leave_id);
            exit();
        }

        // Determine the new status based on action
        $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';

        // Update the leave request in the database
        $update_sql = "
            UPDATE leave_requests 
            SET status = :status, 
                comment = :comment, 
                approved_by = :approved_by, 
                updated_at = NOW() 
            WHERE id = :id
        ";
        $stmt = $conn->prepare($update_sql);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':approved_by', $_SESSION['user_id'], PDO::PARAM_INT); // Ensure user_id is integer
        $stmt->bindParam(':id', $leave_id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => ($new_status === 'Approved') ? "សំណើបានធ្វើការអនុម័តដោយជោគជ័យ។" : "សំណើបានបដិសេធដោយជោគជ័យ។" // "Leave request has been successfully approved/rejected."
            ];
            header("Location: view_leave_details.php?id=" . $leave_id);
            exit();
        } catch (PDOException $e) {
            // Log the error instead of displaying raw error messages to the user
            error_log("Database Update Error: " . $e->getMessage());
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "កំហុសក្នុងការកែប្រែសំណើ។" // "Error updating leave request."
            ];
            header("Location: view_leave_details.php?id=" . $leave_id);
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'សកម្មភាពមិនត្រឹមត្រូវ។' // "Invalid action."
        ];
        header("Location: view_leave_details.php?id=" . $leave_id);
        exit();
    }
}

// Fetch leave request details with necessary joins
$sql = "
    SELECT 
        lr.id AS leave_id, 
        ui.first_name AS submitter_first_name, 
        ui.last_name AS submitter_last_name, 
        lr.fromDate AS from_date, 
        lr.toDate AS to_date, 
        lr.total_days, 
        lr.reason, 
        lr.status, 
        lr.date_send,
        lr.updated_at,
        lr.comment,
        a.first_name AS approver_first_name,
        a.last_name AS approver_last_name,
        d.department_name
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    LEFT JOIN user_info a ON lr.approved_by = a.user_id
    LEFT JOIN departments d ON ui.department_id = d.department_id
    WHERE lr.id = :id
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $leave_id, PDO::PARAM_INT);
$stmt->execute();
$leave = $stmt->fetch(PDO::FETCH_ASSOC);

// If leave request not found
if (!$leave) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'មិនរកឃើញសំណើនេះ។' // "Leave request not found."
    ];
    header("Location: leave_requests.php");
    exit();
}

// Function to format dates
function formatDate($dateStr)
{
    if (empty($dateStr)) return 'N/A';
    try {
        $date = new DateTime($dateStr);
        return $date->format('d M Y'); // Example: 25 Dec 2023
    } catch (Exception $e) {
        return 'Invalid Date';
    }
}
?>
<!-- Back Button -->
<div class="mb-3">
    <button class="btn btn-secondary" onclick="history.back()">
        <i class="fas fa-arrow-left"></i> ថយក្រោយ
    </button>
</div>





<div class="container"><br><br>
    <div class="page-inner">
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
        <!-- Leave request table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <table class="table table-striped custom-table">


                            <!-- Display Leave Details -->
                            <div>
                                <h2>ព័ត៌មានលម្អិតសំណើឈប់សម្រាក</h2>
                                <table class="table table-bordered">

                                    <tr>
                                        <th>ឈ្មោះពេញអ្នកប្រើ</th>
                                        <td><?php echo htmlspecialchars($leave['submitter_first_name']) . ' ' . htmlspecialchars($leave['submitter_last_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ថ្ងៃចាប់ផ្តើម</th>
                                        <td><?php echo formatDate($leave['from_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ថ្ងៃបញ្ចប់</th>
                                        <td><?php echo formatDate($leave['to_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ចំនួនថ្ងៃ</th>
                                        <td><?php echo htmlspecialchars($leave['total_days']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>មូលហេតុ</th>
                                        <td><?php echo nl2br(htmlspecialchars($leave['reason'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ស្ថានភាព</th>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'Approved' => 'success',
                                                'Rejected' => 'danger',
                                                'Canceled' => 'secondary',
                                                'Pending'  => 'warning'
                                            ];

                                            $badgeClass = isset($statusClasses[$leave['status']]) ? $statusClasses[$leave['status']] : 'primary';
                                            ?>
                                            <span class='badge bg-<?php echo $badgeClass; ?>'>
                                                <?php echo htmlspecialchars($leave['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>បានបញ្ជូននៅថ្ងៃ</th>
                                        <td><?php echo formatDate($leave['date_send']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>ផ្នែក</th>
                                        <td><?php echo htmlspecialchars($leave['department_name'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>បានធ្វើបច្ចុប្បន្នភាពនៅ</th>
                                        <td><?php echo formatDate($leave['updated_at']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>បានអនុម័តដោយ</th>
                                        <td>
                                            <?php
                                            if (!empty($leave['approver_first_name']) && !empty($leave['approver_last_name'])) {
                                                echo htmlspecialchars($leave['approver_first_name'] . ' ' . $leave['approver_last_name']);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>មតិយោបល់</th>
                                        <td><?php echo nl2br(htmlspecialchars($leave['comment'] ?? 'N/A')); ?></td>
                                    </tr>
                                </table>


                                <br>

                            </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add the JavaScript for handling required comment -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const approveButton = document.getElementById('approveBtn');
                const rejectButton = document.getElementById('rejectBtn');
                const commentField = document.getElementById('comment');

                approveButton.addEventListener('click', function() {
                    commentField.removeAttribute('required');
                    commentField.classList.remove('is-invalid'); // Remove invalid styling if any
                });

                rejectButton.addEventListener('click', function() {
                    commentField.setAttribute('required', 'required');
                });

                // Optional: Real-time validation feedback
                commentField.addEventListener('input', function() {
                    if (commentField.hasAttribute('required')) {
                        if (commentField.value.trim() === '') {
                            commentField.classList.add('is-invalid');
                        } else {
                            commentField.classList.remove('is-invalid');
                        }
                    }
                });
            });
        </script>
        </body>

        </html>

        <?php include_once('../../include/footer.html'); ?>