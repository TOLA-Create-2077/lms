<?php
include('../../include/session_users.php');
include('../../include/sidebar.php');
include('../../include/functions.php');
include('../../conn_db.php'); // Connect to the database

// Check if the ID is provided
if (!isset($_GET['id'])) {
    echo "មិនមាន ID ស្នើសុំច្បាប់។";
    exit;
}

// Get the leave request ID
$leave_id = $_GET['id'];

// Function to fetch leave request details
function getLeaveRequestDetails($leave_id)
{
    include('../../conn_db.php');
    try {
        $sql = "SELECT lr.id as leave_id, lr.user_id, lr.fromDate as from_date, lr.toDate as to_date, lr.total_days, lr.reason, lr.status 
                FROM leave_requests lr 
                WHERE lr.id = :leave_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "កំហុស: " . $e->getMessage();
        return null;
    }
}

// Fetch the leave request details
$requestDetails = getLeaveRequestDetails($leave_id);
if (!$requestDetails) {
    echo "មិនមានសំណើសុំច្បាប់នេះទេ។";
    exit;
}
?>

<div class="container">
    <div class="page-inner"><br>
        <?php if (isset($_SESSION['alert'])): ?>
            <div id="alert" class="alert alert-<?php echo $_SESSION['alert']['type']; ?>" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="16" height="16" role="img" aria-label="Info">
                    <use xlink:href="#info-fill"></use>
                </svg>
                <?php echo $_SESSION['alert']['message']; ?>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Leave Request Edit Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">កែប្រែសំណើ</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="process_edit_leave.php?id=<?php echo htmlspecialchars($leave_id); ?>">
                            <input type="hidden" name="leave_id" value="<?php echo htmlspecialchars($leave_id); ?>">
                            <div class="form-group">
                                <label for="fromDate">ថ្ងៃចាប់ផ្តើម</label>
                                <input type="date" class="form-control" name="fromDate" id="fromDate" value="<?php echo htmlspecialchars($requestDetails['from_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="toDate">ថ្ងៃបញ្ចប់</label>
                                <input type="date" class="form-control" name="toDate" id="toDate" value="<?php echo htmlspecialchars($requestDetails['to_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="reason">មូលហេតុ</label>
                                <textarea class="form-control" name="reason" id="reason" required><?php echo htmlspecialchars($requestDetails['reason']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">រក្សាទុក</button>
                            <a href="leave_list.php" class="btn btn-secondary">បោះបង់</a>
                        </form>
                    </div>
                </div>

                <script>
                    window.onload = function() {
                        // Initialize DOM elements
                        const fromDateInput = document.getElementById('fromDate');
                        const toDateInput = document.getElementById('toDate');

                        // Disable toDate initially
                        toDateInput.disabled = true;

                        // Set the minimum selectable date for fromDate to today
                        fromDateInput.min = new Date().toISOString().split('T')[0];

                        // Event listener for when fromDate is changed
                        fromDateInput.addEventListener('change', function() {
                            // Enable toDate when fromDate is selected
                            toDateInput.disabled = false;

                            // Get the selected fromDate
                            const fromDate = new Date(this.value);

                            // Limit the toDate to within 3 days of fromDate
                            const maxToDate = new Date(fromDate);
                            maxToDate.setDate(fromDate.getDate() + 2); // Limit to 3 days

                            // Set the attributes of toDateInput
                            toDateInput.setAttribute('min', this.value);
                            toDateInput.setAttribute('max', maxToDate.toISOString().split('T')[0]);

                            // If toDate is out of range, reset it
                            if (toDateInput.value && new Date(toDateInput.value) > maxToDate) {
                                toDateInput.value = maxToDate.toISOString().split('T')[0];
                            }
                        });

                        // Additional validation for toDate
                        toDateInput.addEventListener('change', function() {
                            const fromDate = new Date(fromDateInput.value);
                            const toDate = new Date(this.value);

                            // Ensure toDate is not before fromDate
                            if (toDate < fromDate) {
                                this.value = fromDateInput.value;
                            }
                        });
                    };
                </script>
            </div>
        </div>
    </div>
</div>