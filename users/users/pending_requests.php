<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../include/functions.php');

// Updated function to fetch only "អនុញ្ញាត" status leave requests
function getLeaveRequests()
{
    include('../../conn_db.php'); // Connect to the database

    try {
        $user_id = $_SESSION['user_id']; // Get the logged-in user ID

        // SQL query to get only leave requests with status "អនុញ្ញាត"
        $sql = "SELECT lr.id as leave_id, lr.user_id, lr.fromDate as from_date, lr.toDate as to_date, 
                       lr.total_days, lr.reason, lr.status, ui.first_name, ui.last_name 
                FROM leave_requests lr
                JOIN user_info ui ON lr.user_id = ui.user_id 
                WHERE lr.user_id = :user_id AND lr.status = 'កំពុងរងចាំ'
                ORDER BY lr.id DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch and return results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Fetch leave requests with only "អនុញ្ញាត" status
$leaveRequests = getLeaveRequests();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests</title>
</head>

<body>
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
                            <h4 class="card-title">សំណើច្បាប់ឈប់សម្រាក</h4>
                        </div>
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <a href="../../export/export_leave_requests.php?status=<?php echo $statusFilter; ?>&month=<?php echo $monthFilter; ?>&year=<?php echo $yearFilter; ?>" class="btn btn-success mb-3" style="background-color: #747474; color:aliceblue">
                                <i class="fas fa-download"></i>&nbsp; Export
                            </a>


                        </div>

                        <div class="card-body">
                            <table id="dataTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ឈ្មោះ</th>
                                        <th>ថ្ងៃចាប់ផ្តើម</th>
                                        <th>ថ្ងៃបញ្ចប់</th>
                                        <th>ចំនួនថ្ងៃ</th>
                                        <th>មូលហេតុ</th>
                                        <th>ស្ថានភាព</th>
                                        <th>សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1; // Initialize outside the loop

                                    foreach ($leaveRequests as $request): ?>
                                        <tr>
                                            <td><?php echo $i; ?></td> <!-- This will print the current iteration number -->
                                            <td><?php echo htmlspecialchars($request['first_name'] . " " . $request['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($request['from_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['to_date']); ?></td>
                                            <td><?php echo htmlspecialchars($request['total_days']); ?></td>
                                            <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo getStatusBadgeClass($request['status']); ?>">
                                                    <?php echo htmlspecialchars($request['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($request['status'] == 'កំពុងរងចាំ'): ?>
                                                    <a href="view_leave_request.php?id=<?php echo $request['leave_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit_leave_request.php?id=<?php echo $request['leave_id']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="cancel_leave_request.php?id=<?php echo $request['leave_id']; ?>" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="view_leave_request.php?id=<?php echo $request['leave_id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++; // Increment $i after each iteration
                                    endforeach;
                                    ?>

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
<?php
// Function to get the status badge class
function getStatusBadgeClass($status)
{
    $statusClasses = [
        'អនុញ្ញាត' => 'success',
        'បដិសេធ' => 'danger',
        'បោះបង់' => 'secondary',
        'កំពុងរងចាំ' => 'warning'
    ];
    return $statusClasses[$status] ?? 'secondary';
}
?>
<?php include_once('../../include/footer.html'); ?>