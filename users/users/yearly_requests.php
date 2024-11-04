<?php
// Include necessary files for session, sidebar, and database connection
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../conn_db.php');

// Get the currently logged-in user's ID from the session
$userId = $_SESSION['user_id']; // Assuming 'user_id' is stored in session after login

// Get the year filter from the URL if it is set
$yearFilter = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Default to current year

// Fetch leave requests for the current year by the logged-in user with the year filter
$sql = "
    SELECT lr.id AS leave_id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.date_send
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    WHERE YEAR(lr.fromDate) = :yearFilter 
    AND lr.user_id = :userId
    AND lr.status = 'អនុញ្ញាត'
";
$sql .= " ORDER BY lr.fromDate DESC"; // Order by the latest leave request

$stmt = $conn->prepare($sql);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->bindParam(':yearFilter', $yearFilter, PDO::PARAM_INT);
$stmt->execute();
$leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total days of approved leave requests for the current month
$sqlTotalDays = "
    SELECT SUM(lr.total_days) AS total_days
    FROM leave_requests lr
    WHERE YEAR(lr.fromDate) = YEAR(CURDATE())
    AND lr.user_id = :userId
    AND lr.status = 'អនុញ្ញាត'
";

$totalDaysStmt = $conn->prepare($sqlTotalDays);
$totalDaysStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$totalDaysStmt->execute();
$totalDaysResult = $totalDaysStmt->fetch(PDO::FETCH_ASSOC);
$conn_days = $totalDaysResult['total_days'] ?? 0; // Default to 0 if null

// Fetch distinct years for the year filter dropdown
$yearStmt = $conn->prepare("
    SELECT DISTINCT YEAR(fromDate) AS year
    FROM leave_requests
    WHERE user_id = :userId
    ORDER BY year DESC
");
$yearStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$yearStmt->execute();
$years = $yearStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <title>សំណើរដែលបានស្នើក្នុងឆ្នាំនេះ</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- CSS Bootstrap -->
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
                            <h4 class="card-title">សំណើឈប់សម្រាកឆ្នាំនេះ <span style="background-color: #4da0f3; padding: 0 5px; border-radius: 3px; color:#ffff;"><?php echo htmlspecialchars($conn_days); ?> ថ្ងៃ</span> </h4>
                            <a href="../../export/export_data_this_year_user.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                        </div>

                        <div class="card-body">
                            <table id="dataTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th> <!-- ID -->
                                        <th>ឈ្មោះ</th> <!-- Applicant -->
                                        <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                        <th>ចំនួនថ្ងៃ</th> <!-- Total Days -->
                                        <th>មូលហេតុ</th> <!-- Reason -->
                                        <th>ស្ថានភាព</th> <!-- Status -->
                                        <th>ការប្រតិបត្តិ</th> <!-- Actions -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($leaveRequests)):
                                        $i = 1;
                                    ?>
                                        <?php foreach ($leaveRequests as $request): ?>
                                            <tr data-status="<?php echo htmlspecialchars($request['status']); ?>">
                                                <td><?php echo $i ?></td>
                                                <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($request['fromDate']))); ?></td>
                                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($request['toDate']))); ?></td>
                                                <td><?php echo htmlspecialchars($request['total_days']); ?></td>
                                                <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                                <td>
                                                    <span class='badge bg-<?php
                                                                            echo ($request['status'] == 'អនុញ្ញាត') ? 'success' : (($request['status'] == 'បដិសេធ') ? 'danger' : (($request['status'] == 'បោះបង់') ? 'secondary' : 'warning')); ?>'>
                                                        <?php echo htmlspecialchars($request['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="view_leave_request.php?id=<?php echo $request['leave_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $i++ ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">មិនមានសំណើឈប់សម្រាកបានរកឃើញទេ។</td> <!-- No requests found -->
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
    <?php include_once('../../include/footer.html'); ?>
    <?php include_once('../../include/pagination.php'); ?>
</body>

</html>