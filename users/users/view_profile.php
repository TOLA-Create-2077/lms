<?php
// Include session management and sidebar
include_once('../../include/session_users.php');
include('../../include/sidebar.php');

// Database connection information
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Start the database connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user_id is provided
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Updated SQL query with JOIN to get department name
    $sql = "
        SELECT user_info.*, departments.department_name 
        FROM user_info 
        LEFT JOIN departments ON user_info.department_id = departments.department_id 
        WHERE user_info.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $firstName = $user['first_name'];
        $lastName = $user['last_name'];
        $username = $user['username'];
        $department = $user['department_name'] ?: 'N/A';
        $email = $user['email'];
        $phoneNum = $user['phone_number'];
        $userType = $user['role'];
        $photo = $user['image_url'] ?: '../../assets/img/default.jpg';
    } else {
        echo "User not found.";
        exit;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No user ID provided.";
    exit;
}

// Close the database connection
$conn->close();
?>

<!-- HTML & Bootstrap Content -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<div class="container">
    <div class="page-inner">
        <div class="mt-4" id="title">
            <div id="right">
                <?php if (isset($_SESSION['status'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['status'];
                        unset($_SESSION['status']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php }
                if (isset($_SESSION['statuswrongpassword'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['statuswrongpassword'];
                        unset($_SESSION['statuswrongpassword']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
            </div>
            <div id="left">
                <h1>ប្រវត្តិរូប</h1>
            </div>
        </div>

        <div id="containerFunctionAdd">
            <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
            <a href="profileEdit.php?user_id=<?= urlencode($user_id); ?>" class="btn btn-warning text-white"><i class="fa-solid fa-pen-to-square"></i> កែប្រែប្រវត្តិរូប</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="fa-solid fa-lock"></i> ប្ដូរពាក្យសម្ងាត់</button>
        </div><br>

        <div class="card mb-4">
            <div class="row">
                <div class="col-md-3"><br>
                    <div class="form-group text-center">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <img src="<?= $photo; ?>" width="200px" height="200px" class="border" alt="Profile Picture">
                        </a>
                    </div>
                </div>

                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tr>
                            <td>នាមត្រកូល</td>
                            <td><?= $firstName; ?></td>
                        </tr>
                        <tr>
                            <td>នាម</td>
                            <td><?= $lastName; ?></td>
                        </tr>
                        <tr>
                            <td>ឈ្មោះអ្នកប្រើ</td>
                            <td><?= $username; ?></td>
                        </tr>
                        <tr>
                            <td>តួនាទី</td>
                            <td>
                                <?php
                                // Define an associative array to map role values to Khmer translations
                                $roleTranslations = [
                                    'staff' => 'ថ្នាក់ដឹកនាំ',
                                    'user' => 'គ្រូបង្រៀន',
                                    'admin' => 'អ្នកគ្រប់គ្រង'
                                ];

                                // Display the translated role text, or the role itself if it's not in the array
                                echo htmlspecialchars($roleTranslations[$user['role']] ?? $user['role']);
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <td>ដេប៉ាតឺម៉ង់</td>
                            <td><?= $department; ?></td>
                        </tr>
                        <tr>
                            <td>អ៊ីមែល</td>
                            <td><?= $email; ?></td>
                        </tr>
                        <tr>
                            <td>លេខទូរស័ព្ទ</td>
                            <td><?= $phoneNum; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Full-Size Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="<?= $photo; ?>" class="img-fluid" alt="Full-size image">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">ប្ដូរពាក្យសម្ងាត់</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="processChangePassword.php" method="post">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="current_password">ពាក្យសម្ងាត់បច្ចុប្បន្ន <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password">ពាក្យសម្ងាត់ថ្មី <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password">បញ្ជាក់ពាក្យសម្ងាត់ថ្មី <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #717272; color: white;">បោះបង់</button>
                                <input type="submit" class="btn btn-primary" value="បញ្ជាក់">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<?php include_once('../../include/footer.html'); ?>