<?php
// Include necessary files for session, sidebar, and database connection
include('../../include/session.php');
include('../../include/sidebar.php');
include('../../conn_db.php');

// Fetch leave requests for the current year with user info where status is 'Pending'
$sql = "
    SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.date_send
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    WHERE YEAR(lr.fromDate) = YEAR(CURDATE()) 
    AND lr.status = 'អនុញ្ញាត'
";

$monthlyRequestsResult = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3"></h3>
            </div>
        </div>
        <!-- Back Button
        <div class="mb-3">
            <button class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> ថយក្រោយ
            </button>
        </div> -->


        <!-- User Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="card-title fs-15 mb-0">សំណើរដែលបានស្នើរក្នុងឆ្នាំនេះ</p>
                        <a href="../../export/export_data_user_in_year.php" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
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
                                    <th>ហេតុផល</th>
                                    <th>ស្ថានភាព</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($monthlyRequestsResult as $request): ?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['fromDate']); ?></td>
                                        <td><?php echo htmlspecialchars($request['toDate']); ?></td>
                                        <td><?php echo htmlspecialchars($request['total_days']); ?></td>
                                        <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                        <td>
                                            <span class='badge bg-<?php echo $request['status'] == 'អនុញ្ញាត' ? 'success' : ($request['status'] == 'បដិសេធ' ? 'danger' : 'warning'); ?>'>
                                                <?php echo htmlspecialchars($request['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_leave_details.php?id=<?php echo $request['id']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++ ?>
                                <?php endforeach; ?>
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