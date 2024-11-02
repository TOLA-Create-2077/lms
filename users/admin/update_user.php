<?php include_once('../../include/session_admin.php'); ?>
<?php include('../../include/sidebar.php'); ?>
<?php include('../../conn_db.php'); // Ensure the path is correct and the file exists 
?>

<?php
// Check if the user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php?msg=error");
    exit();
}

// Fetch the user's current data
$user_id = htmlspecialchars($_GET['id']);
$sql = "SELECT * FROM user_info WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    header("Location: manage_users.php?msg=error");
    exit();
}

// Fetch departments for the dropdown
$departments_sql = "SELECT * FROM departments";
$departments_result = $conn->query($departments_sql);
$departments = $departments_result->fetchAll();
?>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container">
    <div class="page-inner">
        <div class="mt-4" id="title">
            <div id="left">
                <h1>កែប្រែប្រវត្តិរូប</h1>
            </div>
            <div id="right">
                <?php
                if (isset($_SESSION['status'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['status'];
                        unset($_SESSION['status']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php }
                ?>
            </div>
        </div>

        <div id="containerFunctionAdd">
            <a href="view_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>" class="btn btn-secondary">
                <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់
            </a>


            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fa-solid fa-lock"></i> កែពាក្យសម្ងាត់
            </button>
        </div><br>

        <div class="card mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <center>
                            <?php if ($photo != null) { ?>
                                <a href="<?php echo $photo; ?>">
                                    <img src="<?php echo $photo; ?>" width="200px" height="auto" style="border:1px solid black;">
                                </a>
                            <?php } else { ?>
                                <a href="./images/default_img.jpg">
                                    <img src="./images/default_img.jpg" width="200px" height="auto" style="border:1px solid black;">
                                </a>
                            <?php } ?>
                        </center>
                    </div>
                </div>

                <div class="col-md-9">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?user_id=' . urlencode($user_id); ?>" method="post">
                        <table class="table table-bordered">
                            <tr>
                                <td>គោត្តនាម</td>
                                <td><input type="text" class="form-control" name="first_name" value="<?php echo $firstName; ?>" required></td>
                            </tr>
                            <tr>
                                <td>នាម</td>
                                <td><input type="text" class="form-control" name="last_name" value="<?php echo $lastName; ?>" required></td>
                            </tr>
                            <tr>
                                <td>ឈ្មោះអ្នកប្រើ</td>
                                <td><input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required></td>
                            </tr>
                            <tr>
                                <td>អ៊ីមែល</td>
                                <td><input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required></td>
                            </tr>
                            <tr>
                                <td>លេខទូរស័ព្ទ</td>
                                <td><input type="text" class="form-control" name="phone_number" value="<?php echo $phoneNum; ?>" required></td>
                            </tr>
                        </table>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> រក្សាទុកការផ្លាស់ប្ដូរ</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ម៉ូដាល់សម្រាប់ការប្ដូរលេខសំងាត់ -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="changePasswordModalLabel">ប្ដូរលេខសំងាត់</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="current_password">លេខសំងាត់បច្ចុប្បន្ន <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="new_password">លេខសំងាត់ថ្មី <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="confirm_password">បញ្ជាក់លេខសំងាត់ថ្មី <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ថយក្រោយ</button>
                                <input type="submit" class="btn btn-primary" value="បញ្ជក់">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>