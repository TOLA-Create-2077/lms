<?php
include('../../include/session.php'); // Include session management
include('../../include/sidebar.php'); // Include sidebar

// Database connection details
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Initialize the connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted for approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['leave_id'])) {
    $leave_id = $_POST['leave_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    // Update leave request status in the database
    $sql_update = "UPDATE leave_requests SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);

    // Determine status based on action
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';

    // Bind parameters
    $stmt_update->bind_param('si', $status, $leave_id);

    // Execute update
    if ($stmt_update->execute()) {
        // Redirect with success message
        header('Location: view_leave_requests.php?msg=success');
        exit;
    } else {
        // Redirect with error message
        header('Location: view_leave_requests.php?msg=error');
        exit;
    }
}

// Fetch leave requests from database
$sql = "
    SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.date_send
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!-- HTML part: Display leave requests table -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Leave Requests</title>
    <!-- Add your stylesheets and JavaScript includes here -->
    <style>
        .custom-table td,
        .custom-table th {
            font-size: 12px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: fixed;
            right: 20px;
            width: 20%;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-inner">
            <!-- Back Button -->
            <div class="mb-3">
                <button class="btn btn-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left"></i> ថយក្រោយ
                </button>
            </div>
            <!-- Display Alert Messages -->
            <?php
            if (isset($_GET['msg'])) {
                $msg = $_GET['msg'];
                if ($msg === 'success') {
                    echo '<div id="alert-box" class="alert alert-success">Leave request status updated successfully.</div>';
                } elseif ($msg === 'error') {
                    echo '<div id="alert-box" class="alert alert-error">Error updating leave request status. Please try again.</div>';
                }
            }
            ?>

            <!-- Leave Requests Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Leave Requests</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Total Days</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Date Sent</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['id']}</td>";
                                            echo "<td>{$row['first_name']} {$row['last_name']}</td>";
                                            echo "<td>{$row['fromDate']}</td>";
                                            echo "<td>{$row['toDate']}</td>";
                                            echo "<td>{$row['total_days']}</td>";
                                            echo "<td>{$row['reason']}</td>";

                                            // Display status with appropriate styling
                                            $status_class = ($row['status'] === 'Approved') ? 'badge-success' : (($row['status'] === 'Rejected') ? 'badge-danger' : 'badge-warning');
                                            echo "<td><span class='badge $status_class'>{$row['status']}</span></td>";

                                            echo "<td>{$row['date_send']}</td>";

                                            // Display appropriate action button based on status
                                            echo "<td>";
                                            if ($row['status'] === 'Pending') {
                                                echo "<form method='POST' action='view_leave_requests.php'>";
                                                echo "<input type='hidden' name='leave_id' value='{$row['id']}'>";
                                                echo "<button type='submit' name='action' value='approve' class='btn btn-success btn-sm'>Approve</button>";
                                                echo "<button type='submit' name='action' value='reject' class='btn btn-danger btn-sm'>Reject</button>";
                                                echo "</form>";
                                            } else {
                                                echo "<form method='POST' action='view_leave_details.php'>";
                                                echo "<input type='hidden' name='id' value='{$row['id']}'>";
                                                echo "<button type='submit' name='action' value='view_details' class='btn btn-info btn-sm'>View Details</button>";
                                                echo "</form>";
                                            }
                                            echo "</td>";

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No records found</td></tr>";
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

    <!-- Include your footer -->
    <?php include_once('../../include/footer.html'); ?>

    <!-- Include your core JS files -->
</body>

</html>