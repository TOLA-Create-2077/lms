<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../include/functions.php');
// Fetch user name outside the main data loop
function getUserName($user_id)
{
    include('../../conn_db.php'); // Connect to the database

    try {
        $sql = "SELECT first_name, last_name FROM user_info WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}
// Function to fetch leave requests with optional filters for status, month, and year
function getLeaveRequests($user_id, $status = null, $month = null, $year = null)
{
    include('../../conn_db.php'); // Connect to the database

    try {
        // Base SQL query to retrieve leave requests based on user ID
        $sql = "SELECT lr.id AS leave_id, lr.fromDate AS from_date, lr.toDate AS to_date, 
                       lr.total_days, lr.reason, lr.status, ui.first_name, ui.last_name 
                FROM leave_requests lr
                JOIN user_info ui ON lr.user_id = ui.user_id
                WHERE lr.user_id = :user_id";

        // Add filters only if they are not empty
        if (!empty($status)) {
            $sql .= " AND lr.status = :status";
        }
        if (!empty($month)) {
            $sql .= " AND MONTH(lr.fromDate) = :month";
        }
        if (!empty($year)) {
            $sql .= " AND YEAR(lr.fromDate) = :year";
        }

        $sql .= " ORDER BY lr.id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if (!empty($status)) $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        if (!empty($month)) $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        if (!empty($year)) $stmt->bindParam(':year', $year, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Initialize variables for filters
$user_id = $_GET['user_id'] ?? $_SESSION['user_id'];
$statusFilter = $_GET['status'] ?? null;
$monthFilter = $_GET['month'] ?? null;
$yearFilter = $_GET['year'] ?? null;

// Retrieve user's name and leave requests based on filters
$userName = getUserName($user_id);
// Retrieve leave requests based on filters
$leaveRequests = getLeaveRequests($user_id, $statusFilter, $monthFilter, $yearFilter);
?>

<div class="container">
    <div class="page-inner"><br>

        <!-- Notification message display -->
        <?php if (isset($_SESSION['alert'])): ?>
            <?php
            $alert = $_SESSION['alert'];
            $alertType = htmlspecialchars($alert['type']);
            $alertMessage = htmlspecialchars($alert['message']);
            ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert" id="autoDismissAlert">
                <?php echo $alertMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function() {
                    var alertElement = document.getElementById('autoDismissAlert');
                    if (alertElement) {
                        alertElement.classList.remove('show');
                        alertElement.classList.add('fade');
                    }
                }, 5000);
            </script>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Leave Requests Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">សំណើច្បាប់ឈប់សម្រាករបស់​ &nbsp<?php echo htmlspecialchars($userName['first_name'] . ' ' . $userName['last_name']); ?></h4>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Export Button -->
                        <a href="../../export/export_user_data_by_staff.php?user_id=<?php echo $user_id; ?>&status=<?php echo $statusFilter; ?>&month=<?php echo $monthFilter; ?>&year=<?php echo $yearFilter; ?>" class="btn btn-success mb-3 ml-2" style="background-color: #747474; color:aliceblue">
                            <i class="fas fa-download"></i>&nbsp; Export
                        </a>


                        <!-- Filter Form -->
                        <form method="GET" action="" class="form-inline">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <div class="form-group mb-0">
                                <label for="status" class="mr-2">Status:</label>
                                <select name="status" id="statusFilter" class="form-control">
                                    <option value="">ទាំងអស់</option>
                                    <option value="កំពុងរងចាំ" <?php echo ($statusFilter == 'កំពុងរងចាំ') ? 'selected' : ''; ?>>កំពុងរងចាំ</option>
                                    <option value="អនុញ្ញាត" <?php echo ($statusFilter == 'អនុញ្ញាត') ? 'selected' : ''; ?>>អនុញ្ញាត</option>
                                    <option value="បដិសេធ" <?php echo ($statusFilter == 'បដិសេធ') ? 'selected' : ''; ?>>បដិសេធ</option>
                                    <option value="បោះបង់" <?php echo ($statusFilter == 'បោះបង់') ? 'selected' : ''; ?>>បោះបង់</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 ml-2">
                                <label for="month" class="mr-2">ខែ:</label>
                                <select class="form-control" id="month" name="month">
                                    <option value="">ជ្រើសរើសខែ</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>" <?php echo ($monthFilter == $m) ? 'selected' : ''; ?>>
                                            <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group mb-0 ml-2">
                                <label for="year" class="mr-2">ឆ្នាំ:</label>
                                <select name="year" id="yearFilter" class="form-control">
                                    <option value="">ទាំងអស់</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($y = $currentYear - 2; $y <= $currentYear; $y++): ?>
                                        <option value="<?php echo $y; ?>" <?php echo ($yearFilter == $y) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary ml-2">ស្វែងរក</button>
                        </form>
                    </div>

                    <div class="card-body">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ឈ្មោះ</th>
                                    <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                    <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($leaveRequests)): ?>
                                    <tr>
                                        <td colspan="7">មិនមានសំណើច្បាប់ឈប់សម្រាកទេ។</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $i = 1;
                                    foreach ($leaveRequests as $request): ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $request['first_name'] . ' ' . $request['last_name']; ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($request['from_date'])); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($request['to_date'])); ?></td>
                                            <td><?php echo $request['total_days']; ?></td>
                                            <td><?php echo $request['reason']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        if ($request['status'] === 'អនុញ្ញាត') {
                                                                            echo 'success';
                                                                        } elseif ($request['status'] === 'បដិសេធ') {
                                                                            echo 'danger';
                                                                        } else {
                                                                            echo 'warning';
                                                                        }
                                                                        ?>">
                                                    <?php echo htmlspecialchars($request['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php $i++;
                                    endforeach; ?>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>
<?php include_once('../../include/footer.html'); ?>
<?php include_once('../../include/pagination.php'); ?>