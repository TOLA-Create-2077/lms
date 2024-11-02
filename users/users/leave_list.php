<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<!-- SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../include/functions.php');

// Function to fetch leave requests for the logged-in user with optional status, month, and year filters
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

        // If a month filter is provided, include it in the query
        if ($month) {
            $sql .= " AND MONTH(lr.fromDate) = :month";
        }

        // If a year filter is provided, include it in the query
        if ($year) {
            $sql .= " AND YEAR(lr.fromDate) = :year";
        }

        $sql .= " ORDER BY lr.id DESC"; // Order by the latest leave request

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Bind the status parameter if it is provided
        if ($status) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }

        // Bind the month parameter if it is provided
        if ($month) {
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        }

        // Bind the year parameter if it is provided
        if ($year) {
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
$monthFilter = isset($_GET['month']) ? (int)$_GET['month'] : null;
$yearFilter = isset($_GET['year']) ? (int)$_GET['year'] : null;

// Fetch leave requests based on the filters
$leaveRequests = getLeaveRequests($statusFilter, $monthFilter, $yearFilter);
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

                            <!-- Filter Form -->
                            <form method="GET" action="" class="form-inline">
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
                                        <option value="1" <?php echo ($monthFilter == 1) ? 'selected' : ''; ?>>មករា</option>
                                        <option value="2" <?php echo ($monthFilter == 2) ? 'selected' : ''; ?>>កុម្ភៈ</option>
                                        <option value="3" <?php echo ($monthFilter == 3) ? 'selected' : ''; ?>>មីនា</option>
                                        <option value="4" <?php echo ($monthFilter == 4) ? 'selected' : ''; ?>>មេសា</option>
                                        <option value="5" <?php echo ($monthFilter == 5) ? 'selected' : ''; ?>>ឧសភា</option>
                                        <option value="6" <?php echo ($monthFilter == 6) ? 'selected' : ''; ?>>មិថុនា</option>
                                        <option value="7" <?php echo ($monthFilter == 7) ? 'selected' : ''; ?>>កក្កដា</option>
                                        <option value="8" <?php echo ($monthFilter == 8) ? 'selected' : ''; ?>>សីហា</option>
                                        <option value="9" <?php echo ($monthFilter == 9) ? 'selected' : ''; ?>>កញ្ញា</option>
                                        <option value="10" <?php echo ($monthFilter == 10) ? 'selected' : ''; ?>>តុលា</option>
                                        <option value="11" <?php echo ($monthFilter == 11) ? 'selected' : ''; ?>>វិច្ឆិកា</option>
                                        <option value="12" <?php echo ($monthFilter == 12) ? 'selected' : ''; ?>>ធ្នូ</option>
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
                            <table id="dataTable" class="table table-striped text-center"> <!-- Added 'text-center' class -->
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ឈ្មោះ</th>
                                        <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់</th>
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
                                            <td><?php
                                                $fromDate = new DateTime($request['from_date']);
                                                echo $fromDate->format('d/m/Y'); // Format to dd/mm/yyyy
                                                ?></td>
                                            <td><?php
                                                $toDate = new DateTime($request['to_date']);
                                                echo $toDate->format('d/m/Y'); // Format to dd/mm/yyyy
                                                ?></td>
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
                                                    <a href="#" class="btn btn-danger btn-sm"
                                                        onclick="confirmCancelLeave(<?php echo $request['leave_id']; ?>)">
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
        'អនុញ្ញាត' => 'badge bg-success',
        'បដិសេធ' => 'danger',
        'បោះបង់' => 'secondary',
        'កំពុងរងចាំ' => 'warning'
    ];
    return $statusClasses[$status] ?? 'secondary';
}
?>
<script>
    $(document).ready(function() {
        $('.cancel-btn').on('click', function() {
            var leaveRequestId = $(this).data('id');

            // Confirmation alert
            swal({
                title: "Are you sure?",
                text: "Once canceled, you will not be able to recover this request!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, cancel it!",
                closeOnConfirm: false
            }, function() {
                // AJAX request to cancel the leave request
                $.ajax({
                    type: "POST",
                    url: "cancel_leave_request.php",
                    data: {
                        leave_request_id: leaveRequestId
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            swal("Cancelled!", response.message, "success");
                            // Optionally, remove the row from the table or reload the table data
                            $('button[data-id="' + leaveRequestId + '"]').closest('tr').fadeOut();
                        } else {
                            swal("Error!", response.message, "error");
                        }
                    },
                    error: function() {
                        swal("Error!", "Something went wrong. Please try again.", "error");
                    }
                });
            });
        });
    });
</script>
<script>
    function confirmCancelLeave(leaveId) {
        swal({
            title: "តើអ្នកប្រាកដជាចង់បោះបង់?",
            text: "បោះបង់សំណើរនេះនឹងមិនអាចត្រឡប់វិញបានទេ។",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willCancel) => {
            if (willCancel) {
                // Redirect to the cancel leave request page with the leave ID
                window.location.href = "cancel_leave_request.php?id=" + leaveId;
            }
        });
    }
</script>
<!-- Include the JavaScript file -->
<script src="../../include/colorstatus.js"></script> <!-- Adjust the path as needed -->
<?php include_once('../../include/footer.html'); ?>