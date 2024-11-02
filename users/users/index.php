<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');

// Database connection
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Assuming user_id is stored in the session
$user_id = $_SESSION['user_id'];

// Fetch total leave requests for the logged-in user
$sql_total_leave = "SELECT COUNT(*) AS total_leave FROM leave_requests WHERE user_id = :user_id";
$stmt_total_leave = $conn->prepare($sql_total_leave);
$stmt_total_leave->bindParam(':user_id', $user_id);
$stmt_total_leave->execute();
$total_leave = $stmt_total_leave->fetch(PDO::FETCH_ASSOC)['total_leave'];

// Fetch requests this month for the logged-in user
$current_month = date('m');
$current_year = date('Y');
$sql_requests_month = "SELECT COUNT(*) AS requests_month FROM leave_requests WHERE status = 'អនុញ្ញាត' AND MONTH(fromDate) = :current_month AND YEAR(fromDate) = :current_year AND user_id = :user_id";
$stmt_requests_month = $conn->prepare($sql_requests_month);
$stmt_requests_month->bindParam(':current_month', $current_month);
$stmt_requests_month->bindParam(':current_year', $current_year);
$stmt_requests_month->bindParam(':user_id', $user_id);
$stmt_requests_month->execute();
$requests_month = $stmt_requests_month->fetch(PDO::FETCH_ASSOC)['requests_month'];

// Fetch requests this year for the logged-in user
$sql_requests_year = "SELECT COUNT(*) AS requests_year FROM leave_requests WHERE status = 'អនុញ្ញាត' AND   YEAR(fromDate) = :current_year AND user_id = :user_id";
$stmt_requests_year = $conn->prepare($sql_requests_year);
$stmt_requests_year->bindParam(':current_year', $current_year);
$stmt_requests_year->bindParam(':user_id', $user_id);
$stmt_requests_year->execute();
$requests_year = $stmt_requests_year->fetch(PDO::FETCH_ASSOC)['requests_year'];

// Fetch pending requests for the logged-in user
$sql_pending_requests = "SELECT COUNT(*) AS pending_requests FROM leave_requests WHERE status = 'កំពុងរងចាំ' AND user_id = :user_id";
$stmt_pending_requests = $conn->prepare($sql_pending_requests);
$stmt_pending_requests->bindParam(':user_id', $user_id);
$stmt_pending_requests->execute();
$pending_requests = $stmt_pending_requests->fetch(PDO::FETCH_ASSOC)['pending_requests'];
?>
<style>
    a.small-box-footer:hover {
        background-color: #6c757d;
        /* Change to a darker shade on hover */
        color: #fff;
        /* Ensure the text remains white */
    }
</style>
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
            </div>
        </div>
        <div class="row">
            <!-- Statistics Cards -->

            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round small-box" style="overflow: hidden;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-calendar-alt"></i> <!-- Leave requests this month -->
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">ច្បាប់ក្នុងខែនេះ</p>
                                    <h4 class="card-title"><?php echo $requests_month; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="monthly_requests.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round small-box" style="overflow: hidden;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-calendar"></i> <!-- Leave requests this year -->
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">ច្បាប់ក្នុងឆ្នាំនេះ</p>
                                    <h4 class="card-title"><?php echo $requests_year; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="yearly_requests.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round small-box" style="overflow: hidden;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-hourglass-start"></i>

                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">ច្បាប់កំពុងរង់ចាំ</p>
                                    <h4 class="card-title"><?php echo $pending_requests; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="pending_requests.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Footer and Scripts -->
<?php include_once('../../include/footer.html'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>