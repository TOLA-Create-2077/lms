<style>
    /* Style for modal */
    #editStatusModal {
        font-family: 'Khmer OS Battambang', sans-serif;
        /* Set the font-family */
    }
</style>
<?php
include_once('../../include/session_admin.php');
include('../../include/sidebar.php'); // បន្ថែមម៉ឺនុយខាងកេរ

// ព័ត៌មានតភ្ជាប់មូលដ្ឋានទិន្នន័យ
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// ចាប់ផ្តើមការតភ្ជាប់ដោយប្រើ PDO ដើម្បីកាន់តែមានការគ្រប់គ្រងកំហុសល្អប្រសើរ
try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ការតភ្ជាប់បានបរាជ័យ: " . $e->getMessage());
}

// Retrieve leave requests with a status of 'Pending'
$sql = "
SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.	date_send
FROM leave_requests lr
JOIN user_info ui ON lr.user_id = ui.user_id
WHERE lr.status = 'កំពុងរងចាំ'
";
$result = $conn->query($sql);
?>



<div class="container"><br><br>
    <div class="page-inner">
        <!-- Back Button -->
        <div class="mb-3">
            <button class="btn btn-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> ថយក្រោយ
            </button>
        </div>
        <!-- Display alert messages -->
        <?php if (isset($_SESSION['alert'])): ?>
            <?php
            $alert = $_SESSION['alert'];
            $alertType = htmlspecialchars($alert['type']);
            $alertMessage = htmlspecialchars($alert['message']);
            ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Leave request table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">សំណើសុំការលើក</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ឈ្មោះ</th>
                                    <th>ពីថ្ងៃ</th>
                                    <th>ទៅថ្ងៃ</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>ហេតុផល</th>

                                    <th>សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                if ($result->rowCount() > 0) {
                                    foreach ($result as $row) {
                                        // Display leave request details
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['fromDate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['toDate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['total_days']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                                        echo "<td><span class='badge bg-warning'>" . htmlspecialchars($row['status']) . "</span></td>";


                                        echo "</tr>";
                                        $i++; // Increment $i here
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>គ្មានកំណត់ត្រាផ្តល់ឱ្យ</td></tr>";
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



<!-- Footer -->
<?php include_once('../../include/footer.html'); ?>

<!-- Include JS files -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>