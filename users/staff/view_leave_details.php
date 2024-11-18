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
function formatDate($dateStr, $format = 'Y-m-d H:i:s')
{
    if (empty($dateStr)) return 'N/A';
    try {
        $date = new DateTime($dateStr);
        return $date->format($format);
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
                                        <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>

                                        <td><?php echo formatDate($leave['from_date'], 'd/m/Y'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                        <td><?php echo formatDate($leave['to_date'], 'd/m/Y'); ?></td>
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

                                            $badgeClass = isset($statusClasses[$leave['status']]) ? $statusClasses[$leave['status']] : 'success';
                                            ?>
                                            <span class='badge bg-<?php echo $badgeClass; ?>'>
                                                <?php echo htmlspecialchars($leave['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>បានស្នើនៅថ្ងៃ</th>
                                        <td><?php echo formatDate($leave['date_send'], 'd/m/Y H:i:s'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>ដេប៉ាតឺម៉ង់</th>
                                        <td><?php echo htmlspecialchars($leave['department_name'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>បានអនុម័តនៅថ្ងៃ</th>
                                        <td><?php echo formatDate($leave['updated_at'], 'd/m/Y H:i:s'); ?></td>
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

        </body>

        </html>

        <?php include_once('../../include/footer.html'); ?>