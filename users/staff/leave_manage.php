<?php
include('../../include/session.php'); // Include session management
include('../../include/sidebar.php'); // Include sidebar

// Database connection details
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Start connection using PDO for better error handling
try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submissions for approve/reject/update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['leave_id'])) {
        $leaveId = $_POST['leave_id'];

        // Validate leave_id
        if (!is_numeric($leaveId)) {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid leave request ID.'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        if ($_POST['action'] === 'approve') {
            try {
                // Update the leave request status to Approved
                $updateSql = "UPDATE leave_requests SET status = 'Approved', updated_at = NOW() WHERE id = :id";
                $stmt = $conn->prepare($updateSql);
                $stmt->execute(['id' => $leaveId]);

                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Leave request approved successfully.'];
            } catch (PDOException $e) {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Failed to approve leave request: ' . $e->getMessage()];
            }
        } elseif ($_POST['action'] === 'reject') {
            // Ensure 'comment' is provided
            if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
                $comment = trim($_POST['comment']);
                try {
                    // Update the leave request status to Rejected with a comment
                    $updateSql = "UPDATE leave_requests SET status = 'Rejected', comment = :comment, updated_at = NOW() WHERE id = :id";
                    $stmt = $conn->prepare($updateSql);
                    $stmt->execute(['id' => $leaveId, 'comment' => $comment]);

                    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Leave request rejected successfully.'];
                } catch (PDOException $e) {
                    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Failed to reject leave request: ' . $e->getMessage()];
                }
            } else {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Rejection comment is required.'];
            }
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch leave requests from the database
try {
    $sql = "
    SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.comment, lr.date_send
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    WHERE lr.status = 'កំពុងរងចាំ'
    ORDER BY lr.date_send DESC
";

    $stmt = $conn->query($sql);
    $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Failed to fetch leave requests: " . $e->getMessage());
}
?>



<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3"></h3>
            </div>

        </div>

        <!-- Show notification message -->
        <?php if (isset($_SESSION['alert'])): ?>
            <?php
            $alert = $_SESSION['alert'];
            $alertType = htmlspecialchars($alert['type']);
            $alertMessage = htmlspecialchars($alert['message']);
            ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                <?php echo $alertMessage; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Leave request table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">សំណើរដែលកំពុងរង់ចាំ</h4>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-striped ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ឈ្មោះ</th>
                                    <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                    <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                    <th>ចំនួនថ្ងៃ</th>
                                    <th>ហេតុផល</th>
                                    <th>ស្ថានភាព</th>
                                    <th>សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($leaveRequests)):
                                    $i = 1;
                                ?>
                                    <?php foreach ($leaveRequests as $row): ?>
                                        <tr>
                                            <td><?php echo $i ?></td>
                                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['fromDate']); ?></td>
                                            <td><?php echo htmlspecialchars($row['toDate']); ?></td>
                                            <td><?php echo htmlspecialchars($row['total_days']); ?></td>
                                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        if ($row['status'] === 'អនុញ្ញាត') {
                                                                            echo 'success';
                                                                        } elseif ($row['status'] === 'បដិសេធ') {
                                                                            echo 'danger';
                                                                        } else {
                                                                            echo 'warning';
                                                                        }
                                                                        ?>">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] === 'កំពុងរងចាំ'): ?>
                                                    <button type="button" onclick="openApproveCommentModal(<?php echo htmlspecialchars($row['id']); ?>)" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" onclick="openRejectCommentModal(<?php echo htmlspecialchars($row['id']); ?>)" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <a href="view_leave_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <!-- For any other status -->
                                                <?php endif; ?>


                                            </td>
                                        </tr>
                                        <?php $i++ ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">គ្មានកំណត់ត្រាផ្តល់ឱ្យ</td>
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
<!-- Approve Comment Modal -->
<div id="approveCommentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="approveCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approveCommentForm" method="POST" action="approve_status.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveCommentModalLabel">មតិយោបល់សម្រាប់ការអនុញ្ញាត</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeApproveModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="leave_id" id="approve_leave_id" />
                    <div class="form-group">
                        <label for="approve_comment">មតិយោបល់</label>
                        <textarea name="comment" id="approve_comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeApproveModal()">បិទ</button>
                    <button type="submit" name="action" value="approve" class="btn btn-success">អនុញ្ញាត</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Reject Comment Modal -->
<div id="rejectCommentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="rejectCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectCommentForm" method="POST" action="reject_status.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectCommentModalLabel">មតិយោបល់សម្រាប់ការបដិសេធ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeRejectModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="leave_id" id="reject_leave_id" />
                    <div class="form-group">
                        <label for="reject_comment">មតិយោបល់</label>
                        <textarea name="comment" id="reject_comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeRejectModal()">បិទ</button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">បដិសេធ</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Function to open Approve Comment Modal
    function openApproveCommentModal(id) {
        $('#approve_leave_id').val(id);
        $('#approve_comment').val('');
        $('#approveCommentModal').modal('show');
    }

    // Function to close Approve Comment Modal
    function closeApproveModal() {
        $('#approveCommentModal').modal('hide');
    }

    // Function to open Edit Status Modal
    function openEditStatusModal(id, status) {
        $('#edit_leave_id').val(id);
        $('#edit_status').val(status);
        // Clear previous comment
        $('#edit_comment').val('');
        // If status is Rejected, make comment required
        $('#editStatusForm').find('textarea[name="comment"]').prop('required', true);
        $('#editStatusModal').modal('show');
    }

    // Function to close Edit Status Modal
    function closeModal() {
        $('#editStatusModal').modal('hide');
    }

    // Function to open Reject Comment Modal
    function openRejectCommentModal(id) {
        $('#reject_leave_id').val(id);
        $('#reject_comment').val('');
        $('#rejectCommentModal').modal('show');
    }

    // Function to close Reject Comment Modal
    function closeRejectModal() {
        $('#rejectCommentModal').modal('hide');
    }

    // Optional: Automatically close alerts after a few seconds
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").alert('close');
        }, 5000); // 5 seconds
    });
</script>
</body>

</html>
<!-- Footer -->
<?php include_once('../../include/footer.html'); ?>
<?php include_once('../../include/pagination.php'); ?>