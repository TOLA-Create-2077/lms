<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../include/functions.php');

// Function to fetch leave requests for the logged-in user with optional filters
function getLeaveRequests($status = null, $month = null, $year = null)
{
    include('../../conn_db.php'); // Connect to the database

    try {
        $user_id = $_SESSION['user_id']; // Get the logged-in user ID

        // Base SQL query
        $sql = "SELECT lr.id as leave_id, lr.user_id, lr.fromDate as from_date, lr.toDate as to_date, lr.total_days, lr.reason, lr.status, 
                       ui.first_name, ui.last_name 
                FROM leave_requests lr
                JOIN user_info ui ON lr.user_id = ui.user_id 
                WHERE lr.user_id = :user_id";

        // If a status filter is provided, include it in the query
        if ($status) {
            $sql .= " AND lr.status = :status";
        }

        // If month and year filters are provided, include them in the query
        if ($month && $year) {
            $sql .= " AND MONTH(lr.fromDate) = :month AND YEAR(lr.fromDate) = :year";
        }

        $sql .= " ORDER BY lr.id DESC"; // Order by the latest leave request

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Bind the status parameter if it is provided
        if ($status) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }

        // Bind month and year parameters if they are provided
        if ($month && $year) {
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        }

        $stmt->execute();

        // Fetch all the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Get the filters from the URL if they are set
$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;
$monthFilter = isset($_GET['month']) ? $_GET['month'] : null;
$yearFilter = isset($_GET['year']) ? $_GET['year'] : null;

// Fetch leave requests based on the filters
$leaveRequests = getLeaveRequests($statusFilter, $monthFilter, $yearFilter);
?>

<div class="container">
    <div class="page-inner">
        <!-- Back Button -->
        <div class="mb-3">
            <button class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> ថយក្រោយ
            </button>
        </div>
        <div class="row" id="filterFormContainer" style="display:none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">តម្រូវការរាយការណ៍</h4>
                    </div>
                    <div class="card-body">
                        <form id="reportFilterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="startDate">កាលបរិច្ឆេទចាប់ផ្តើម</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="endDate">កាលបរិច្ឆេទបញ្ចប់</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department">ផ្នែក</label>
                                        <select class="form-control" id="department" name="department">
                                            <option value="">ជ្រើសរើសផ្នែក</option>
                                            <?php
                                            // Database connection parameters
                                            $host = 'localhost';
                                            $db = 'lms';
                                            $user = 'root';
                                            $pass = '';

                                            // Create a new mysqli connection
                                            $conn = new mysqli($host, $user, $pass, $db);

                                            // Check for connection errors
                                            if ($conn->connect_error) {
                                                die("Connection failed: " . $conn->connect_error);
                                            }

                                            // Prepare and execute the SQL query using mysqli
                                            $sql = "SELECT department_id, department_name FROM departments ORDER BY department_name ASC";
                                            $result = $conn->query($sql);

                                            // Check if the query was successful
                                            if ($result) {
                                                if ($result->num_rows > 0) {
                                                    while ($dept = $result->fetch_assoc()) {
                                                        echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No departments found</option>";
                                                }
                                            } else {
                                                echo "<option value=''>Error fetching departments: " . $conn->error . "</option>";
                                            }

                                            // Close the database connection
                                            $conn->close();
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">ស្ថានភាព</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">ជ្រើសរើសស្ថានភាព</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                            <option value="Pending">Pending</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="month">ខែ</label>
                                        <select class="form-control" id="month" name="month">
                                            <option value="">ជ្រើសរើសខែ</option>
                                            <option value="1">មករា</option>
                                            <option value="2">កុម្ភៈ</option>
                                            <option value="3">មីនា</option>
                                            <option value="4">មេសា</option>
                                            <option value="5">ឧសភា</option>
                                            <option value="6">មិថុនា</option>
                                            <option value="7">កក្កដា</option>
                                            <option value="8">សីហា</option>
                                            <option value="9">កញ្ញា</option>
                                            <option value="10">តុលា</option>
                                            <option value="11">វិច្ឆិកា</option>
                                            <option value="12">ធ្នូ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">ឆ្នាំ</label>
                                        <select class="form-control" id="year" name="year">
                                            <option value="">ជ្រើសរើសឆ្នាំ</option>
                                            <?php
                                            // Assuming the range of years you want to provide
                                            for ($i = 2020; $i <= date("Y"); $i++) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> ស្វែងរក
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Requests Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- Card Header with Title and Filter Form -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">សំណើច្បាប់ឈប់សម្រាក</h4>
                        <!-- Filter Form -->
                        <form method="GET" action="" class="form-inline">
                            <div class="form-group mb-0">
                                <label for="status" class="mr-2">Filter by Status:</label>
                                <select name="status" id="statusFilter" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approved" <?php echo ($statusFilter == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Rejected" <?php echo ($statusFilter == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="Canceled" <?php echo ($statusFilter == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 ml-2">
                                <label for="month" class="mr-2">Filter by Month:</label>
                                <select name="month" id="monthFilter" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>" <?php echo ($monthFilter == $m) ? 'selected' : ''; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0 ml-2">
                                <label for="year" class="mr-2">Filter by Year:</label>
                                <select name="year" id="yearFilter" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($y = $currentYear - 5; $y <= $currentYear; $y++): ?>
                                        <option value="<?php echo $y; ?>" <?php echo ($yearFilter == $y) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Card Body with Table -->
                    <div class="card-body">
                        <table class="table table-striped" id="leaveRequestsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ឈ្មោះពេញ</th>
                                    <th>ថ្ងៃចាប់ផ្តើម</th>
                                    <th>ថ្ងៃបញ្ចប់</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                    <th>ការប្រតិបត្តិ</th>
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
                                            <td><?php echo htmlspecialchars($request['from_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['to_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['total_days']); ?></td>
                                            <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                            <td>
                                                <span class='badge badge-<?php
                                                                            echo ($request['status'] == 'Approved') ? 'success' : (($request['status'] == 'Rejected') ? 'danger' : (($request['status'] == 'Canceled') ? 'secondary' : 'warning'));
                                                                            ?>'>
                                                    <?php echo htmlspecialchars($request['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?php echo htmlspecialchars($request['leave_id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        សកម្មភាព <i class="fas fa-caret-down"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo htmlspecialchars($request['leave_id']); ?>">
                                                        <a class="dropdown-item" href="view_leave_details.php?id=<?php echo htmlspecialchars($request['leave_id']); ?>">
                                                            <i class="fas fa-info-circle mr-2"></i> លម្អិត
                                                        </a>

                                                        <?php if ($request['status'] == 'Pending'): ?>
                                                            <a class="dropdown-item" href="edit_leave.php?id=<?php echo htmlspecialchars($request['leave_id']); ?>">
                                                                <i class="fas fa-edit mr-2"></i> កែ
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="cancel_leave.php?id=<?php echo htmlspecialchars($request['leave_id']); ?>" onclick="return confirm('តើអ្នកប្រាកដជាចង់បោះបង់សំណើច្បាប់ឈប់សម្រាកនេះទេ?');">
                                                                <i class="fas fa-times-circle mr-2"></i> បោះបង់
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++ ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">មិនមានសំណើច្បាប់ឈប់សម្រាកទេ</td>
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
<?php include_once('../../include/footer.html'); ?>
<!-- Include required JS scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>