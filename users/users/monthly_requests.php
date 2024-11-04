<?php
// Include necessary files for session, sidebar, and database connection
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../conn_db.php');

// Get the currently logged-in user's ID from the session
$userId = $_SESSION['user_id']; // Assuming 'user_id' is stored in session after login

// Fetch leave requests for the current month by the logged-in user
$sql = "
    SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.date_send
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    WHERE MONTH(lr.fromDate) = MONTH(CURDATE()) AND YEAR(fromDate) = YEAR(CURDATE())
    AND YEAR(lr.fromDate) = YEAR(CURDATE())
    AND lr.user_id = :userId
    AND lr.status = 'អនុញ្ញាត'
";

$sql .= " ORDER BY lr.fromDate DESC"; // Order by the start date

$stmt = $conn->prepare($sql);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$monthlyRequestsResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total number of leave days (conn_days) for the current month where status is 'អនុញ្ញាត'
$sqlTotalDays = "
    SELECT SUM(lr.total_days) AS conn_days
    FROM leave_requests lr
    WHERE MONTH(lr.fromDate) = MONTH(CURDATE())
    AND YEAR(lr.fromDate) = YEAR(CURDATE())
    AND lr.user_id = :userId
    AND lr.status = 'អនុញ្ញាត'
";

$stmtTotalDays = $conn->prepare($sqlTotalDays);
$stmtTotalDays->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtTotalDays->execute();
$totalDaysResult = $stmtTotalDays->fetch(PDO::FETCH_ASSOC);
$conn_days = $totalDaysResult['conn_days'] ?? 0; // Set to 0 if no leave days are found
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>សំណើរដែលបានស្នើរក្នុងខែនេះ</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- CSS Bootstrap -->
</head>

<body>
    <div class="container">
        <div class="page-inner"><br><br>
            <!-- Back Button -->
            <div class="mb-3">
                <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                    <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
            </div>

            <!-- Leave Requests Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">សំណើឈប់សម្រាកក្នុងខែនេះ <span style="background-color: #4da0f3; padding: 0 5px; border-radius: 3px; color: #ffffff;"><?php echo htmlspecialchars($conn_days); ?> ថ្ងៃ</span></h4>

                            <a href="../../export/export_data_this_month_user.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="dataTable" class="table table-striped ">

                                <thead>
                                    <tr>
                                        <th>ID</th> <!-- ID -->
                                        <th>ឈ្មោះ</th> <!-- Applicant -->
                                        <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                        <th>ចំនួនថ្ងៃ</th> <!-- Total Days -->
                                        <th>មូលហេតុ</th> <!-- Reason -->
                                        <th>ស្ថានភាព</th> <!-- Status -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($monthlyRequestsResult)):
                                        $i = 1;
                                    ?>
                                        <?php foreach ($monthlyRequestsResult as $request): ?>
                                            <tr>
                                                <td><?php echo $i ?></td>
                                                <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($request['fromDate']); ?></td>
                                                <td><?php echo htmlspecialchars($request['toDate']); ?></td>
                                                <td><?php echo htmlspecialchars($request['total_days']); ?></td>
                                                <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                                <td>
                                                    <span class='badge bg-<?php echo $request['status'] == 'អនុញ្ញាត' ? 'success' : ($request['status'] == 'បដិសេដ' ? 'danger' : 'warning'); ?>'>
                                                        <?php echo htmlspecialchars($request['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php $i++ ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">មិនមានសំណើឈប់សម្រាកបានរកឃើញទេ។</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <script src="../../include/colorstatus.js"></script> <!-- Adjust the path as needed -->
    <?php include_once('../../include/footer.html'); ?>
    <?php include_once('../../include/pagination.php'); ?>

</body>

</html>