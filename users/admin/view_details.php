<?php
// Include necessary files for session, sidebar, and database connection
include('../../include/session.php');
include('../../include/sidebar.php');
include('../../conn_db.php');

// Get query parameters
$userId = $_GET['user_id'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

// Build SQL query
$sql = "SELECT 
            leave_requests.id,
            leave_requests.fromDate,
            leave_requests.toDate,
            leave_requests.reason,
            leave_requests.status,
            leave_requests.date_send AS created_at,
            user_info.first_name,
            user_info.last_name,
            departments.department_name
        FROM leave_requests
        JOIN user_info ON leave_requests.user_id = user_info.user_id
        JOIN departments ON user_info.department_id = departments.department_id
        WHERE leave_requests.user_id = '$userId'";

// Apply additional filters
if ($startDate) $sql .= " AND leave_requests.fromDate >= '$startDate'";
if ($endDate) $sql .= " AND leave_requests.toDate <= '$endDate'";
if ($month) $sql .= " AND MONTH(leave_requests.fromDate) = '$month'";
if ($year) $sql .= " AND YEAR(leave_requests.fromDate) = '$year'";

$sql .= " ORDER BY leave_requests.fromDate DESC";

include('../../conn_db1.php');
$result = $conn->query($sql);
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3"></h3>
            </div>
        </div>

        <!-- User Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <?php if ($result && $result->num_rows > 0):
                            $firstRow = $result->fetch_assoc(); // Fetch the first row to display the teacher's name
                        ?>
                            <p class="card-title fs-15 mb-0">សំណើរច្បាប់របស់គ្រូបង្រៀនឈ្មោះ
                                <?php echo htmlspecialchars($firstRow['first_name'] . ' ' . $firstRow['last_name']); ?>
                            </p>
                        <?php endif; ?>
                        <a href="../../export/export_data_user_in_month.php" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>

                    <div class="card-body">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ឈ្មោះ</th>
                                    <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                    <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>ហេតុផល</th>
                                    <th>ស្ថានភាព</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    $i = 1; // Initialize a counter for numbering rows
                                    while ($row = $result->fetch_assoc()) {
                                        $fromDate = date("d-m-Y", strtotime($row['fromDate']));
                                        $toDate = date("d-m-Y", strtotime($row['toDate']));
                                        $totalDays = (strtotime($row['toDate']) - strtotime($row['fromDate'])) / (60 * 60 * 24) + 1; // Calculate total days
                                        $status = ($row['status'] === 'អនុញ្ញាត') ? 'បានអនុញ្ញាត' : (($row['status'] === 'បដិសេធ') ? 'បានបដិសេធ' : 'រងចាំអនុញ្ញាត');

                                        echo "<tr>
                <td>{$i}</td>
                <td>{$row['first_name']} {$row['last_name']}</td>
                <td>{$fromDate}</td>
                <td>{$toDate}</td>
                <td>{$totalDays} ថ្ងៃ</td>
                <td>{$row['reason']}</td>
                <td>{$status}</td>
                <td>";

                                        if ($row['status'] !== 'រងចាំអនុញ្ញាត') {
                                            echo "
                  <a href='view_leave_details.php?id={$row['id']}' class='btn btn-info btn-sm'>
                      <i class='fas fa-eye'></i>
                  </a>";
                                        }

                                        echo "</td>
            </tr>";
                                        $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No leave records found.</td></tr>";
                                }
                                $conn->close();
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once('../../include/footer.html'); ?>
<?php include_once('../../include/pagination.php'); ?>