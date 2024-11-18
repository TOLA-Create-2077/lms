<?php
include('../../include/session_admin.php');
include('../../include/sidebar.php');
include('../../include/functions.php');
include('../../conn_db1.php');

// Get user_id and filter criteria from the query string
$user_id = $_GET['user_id'] ?? null;
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$department = $_GET['department'] ?? '';
$employeeName = $_GET['employeeName'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

if ($user_id) {
    // Prepare the SQL query with optional filter conditions
    $query = "SELECT leave_requests.*, user_info.first_name, user_info.last_name
              FROM leave_requests 
              LEFT JOIN user_info ON leave_requests.user_id = user_info.user_id
              WHERE leave_requests.user_id = '$user_id'";

    // Apply additional filters
    if ($startDate) {
        $query .= " AND leave_requests.fromDate >= '$startDate'";
    }
    if ($endDate) {
        $query .= " AND leave_requests.toDate <= '$endDate'";
    }
    if ($month) {
        $query .= " AND MONTH(leave_requests.fromDate) = '$month'";
    }
    if ($year) {
        $query .= " AND YEAR(leave_requests.fromDate) = '$year'";
    }

    $result = mysqli_query($conn, $query);

    if ($result) {
        $leaveRequests = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch user info for display
    $userQuery = "SELECT first_name, last_name FROM user_info WHERE user_id = '$user_id'";
    $userResult = mysqli_query($conn, $userQuery);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $userName = mysqli_fetch_assoc($userResult);
    } else {
        die("User information not found.");
    }
} else {
    die("User ID is missing.");
}
?>

<div class="container">
    <div class="page-inner"><br>

        <!-- Display Alert Message -->
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
                        <h4 class="card-title">ព័ត៏មានសុំច្បាប់របស់គ្រូបង្រៀនឈ្មោះ <?php echo htmlspecialchars($userName['first_name'] . ' ' . $userName['last_name']); ?></h4>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Export Button -->
                        <a href="../../export/export_user_data_by_staff.php?user_id=<?php echo $user_id; ?>" class="btn btn-success mb-3 ml-2" style="background-color: #747474; color:aliceblue">
                            <i class="fas fa-download"></i>&nbsp; អត្រាបាននាំចេញ
                        </a>
                    </div>

                    <div class="card-body">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ឈ្មោះពេញ</th>
                                    <th>ថ្ងៃចាប់ផ្តើម</th>
                                    <th>ថ្ងៃបញ្ចប់</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($leaveRequests)) {
                                    $i = 1;
                                    foreach ($leaveRequests as $leave) {
                                        echo "<tr>
                                            <td>{$i}</td>
                                            <td>" . htmlspecialchars($leave['first_name'] . ' ' . $leave['last_name']) . "</td>
                                            <td>" . htmlspecialchars($leave['fromDate']) . "</td>
                                            <td>" . htmlspecialchars($leave['toDate']) . "</td>
                                            <td>" . htmlspecialchars($leave['total_days']) . "</td>
                                            <td>" . htmlspecialchars($leave['reason']) . "</td>
                                            <td>" . htmlspecialchars($leave['status']) . "</td>
                                          </tr>";
                                        $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>គ្មានទិន្នន័យសុំច្បាប់សម្រាប់អ្នកប្រើប្រាស់នេះទេ។</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS and jQuery (if needed) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>