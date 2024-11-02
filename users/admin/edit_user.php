<?php
// Start the session and include necessary files
include_once('../../include/session.php');
include('../../include/sidebar.php');
include('../../conn_db.php'); // Ensure the path is correct

// Check if the user ID is provided in the query string
if (!isset($_GET['id'])) {
    die("User ID is missing.");
}

$user_id = intval($_GET['id']); // Ensure user_id is an integer

// Fetch the user data from the database
$sql = "SELECT * FROM user_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// If the user data exists, assign it to variables
if ($user) {
    $username = $user['username'];
    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
    $phone_number = $user['phone_number'];
} else {
    // If no user found, redirect or show error
    $_SESSION['error'] = "អ្នកប្រើប្រាស់មិនមានទេ។"; // User does not exist
    header("Location: manage_users.php");
    exit();
}

if (!$user) {
    die("User not found.");
}

// Fetch departments for the dropdown
$sql_departments = "SELECT department_id, department_name FROM departments";
$stmt_departments = $conn->prepare($sql_departments);
$stmt_departments->execute();
$departments = $stmt_departments->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Bootstrap 5 CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome for Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">កែប្រែព័ត៌មានអ្នកប្រើប្រាស់</h3>
            </div>
        </div>

        <div id="containerFunctionAdd">
            <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fa-solid fa-lock"></i> ប្ដូរពាក្យសម្ងាត់
            </button>
        </div>
        <br>

        <div class="card mb-4">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="form-group">
                        <label for="image_url">
                            <img id="imagePreview" src="<?php echo $user['image_url'] ?: '../../assets/img/image.png'; ?>" width="200px" height="auto" style="border:1px solid black; cursor: pointer;" onclick="document.getElementById('image_url')">
                        </label>
                        <form action="process_edit_user.php" method="post" enctype="multipart/form-data">
                            <input type="file" id="image_url" name="image_url" accept="image/*" style="display: none;" onchange="previewImage(event)">
                    </div>
                </div>

                <script>
                    function previewImage(event) {
                        const imagePreview = document.getElementById('imagePreview');
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result; // Update the src of the image preview
                            }
                            reader.readAsDataURL(file); // Read the file as a data URL
                        }
                    }
                </script>

                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tr>
                            <td>គោត្តនាម</td>
                            <td><input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required></td>
                        </tr>
                        <tr>
                            <td>នាម</td>
                            <td><input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required></td>
                        </tr>
                        <tr>
                            <td>ឈ្មោះអ្នកប្រើ</td>
                            <td><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></td>
                        </tr>
                        <tr>
                            <td>អ៊ីមែល</td>
                            <td><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
                        </tr>
                        <tr>
                            <td>លេខទូរស័ព្ទ</td>
                            <td><input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required></td>
                        </tr>
                        <tr>
                            <td>ដេប៉ាតមិន</td>
                            <td>
                                <select class="form-control" name="department_id" required>
                                    <option value="">ជ្រើសរើសដេប៉ាតមិន</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['department_id']; ?>" <?php echo ($department['department_id'] == $user['department_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($department['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>តួនាទី</td>
                            <td>
                                <select class="form-control" name="role" required>
                                    <option value="">ជ្រើសរើសតួនាទី</option>
                                    <option value="staff" <?php echo ($user['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>ស្ថានភាព</td>
                            <td>
                                <select class="form-control" name="status" required>
                                    <option value="1" <?php echo ($user['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($user['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> រក្សាទុកការផ្លាស់ប្ដូរ</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Structure -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatePasswordModalLabel">កែប្រែពាក្យសម្ងាត់</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="processChangePasswordUser.php" method="POST" id="updatePasswordForm">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <div class="form-group">
                                <label for="new_password">ពាក្យសម្ងាត់ថ្មី</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">បញ្ជាក់ពាក្យសម្ងាត់ថ្មី</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                            <button type="submit" class="btn btn-primary">ប្ដូរពាក្យសម្ងាត់</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../../include/footer.html'); ?>