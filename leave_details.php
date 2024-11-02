<?php
// Sample database connection (replace with your actual connection details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get staff ID from query string
$staff_id = intval($_GET['staff_id']);

// Fetch leave information
$sql = "SELECT * FROM leave_requests WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch staff member's details for the page title
$staff_sql = "SELECT first_name, last_name FROM staff WHERE staff_id = ?";
$staff_stmt = $conn->prepare($staff_sql);
$staff_stmt->bind_param("i", $staff_id);
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();
$staff = $staff_result->fetch_assoc();

if ($staff) {
    $staff_name = htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']);
} else {
    $staff_name = "Unknown";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Details for <?php echo htmlspecialchars($staff_name); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Leave Details for <?php echo htmlspecialchars($staff_name); ?></h3>
                </div>
            </div>

            <!-- Leave Details Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Leave Requests</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        // Output data of each row
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>" . htmlspecialchars($row['id']) . "</td>
                                                <td>" . htmlspecialchars($row['from_date']) . "</td>
                                                <td>" . htmlspecialchars($row['to_date']) . "</td>
                                                <td>" . htmlspecialchars($row['reason']) . "</td>
                                                <td>" . htmlspecialchars($row['status']) . "</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No leave details found.</td></tr>";
                                    }
                                    // Close statements and connection
                                    $stmt->close();
                                    $staff_stmt->close();
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="copyright text-center">
                <p class="text-muted mb-0">© 2024 Your Company. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
