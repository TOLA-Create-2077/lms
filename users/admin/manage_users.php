<?php include_once('../../include/session_admin.php'); ?>
<?php include('../../include/sidebar.php'); ?>


<?php
include('../../conn_db.php');
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, u.email, u.phone_number, u.status, d.department_name, u.role
        FROM user_info u
        LEFT JOIN departments d ON u.department_id = d.department_id 
        WHERE u.role IN ('user', 'staff', 'admin')";

$result = $conn->query($sql);
$users = $result->fetchAll();

?>

<!DOCTYPE html>
<html lang="km"> <!-- Assuming you're using Khmer language -->



<body>

    <div class="container">


        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <!-- Sidebar can be included here -->

            </div>

            <main>
                <div class="container-fluid px-4">

                    <div class="mt-4" id="title">
                        <div id="right">
                            <!-- Notification message display -->
                            <?php


                            // Check if 'success' or 'error' is passed via the URL query parameters
                            if (isset($_GET['success'])) {
                                $success_message = htmlspecialchars($_GET['success']);
                            }

                            if (isset($_SESSION['error'])) {
                                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                unset($_SESSION['error']); // Clear the message after displaying
                            }
                            ?>

                            <!-- Display success message from URL query parameter, if available -->
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                            <?php endif; ?>

                            <!-- Display error message from URL query parameter, if available -->
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoDismissAlert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <script>
                                    setTimeout(function() {
                                        var alertElement = document.getElementById('autoDismissAlert');
                                        if (alertElement) {
                                            alertElement.classList.remove('show');
                                            alertElement.classList.add('fade');
                                        }
                                    }, 5000); // 5 seconds delay to auto-dismiss
                                </script>
                            <?php endif; ?>



                        </div>


                    </div>

                    <div class="card mb-4">
                        <div class="card-header">

                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h1>បញ្ជីអ្នកប្រើប្រាស់</h1>
                                <a href="add_user.php" class="btn btn-primary">
                                    <i class="fa-solid fa-user-plus"></i> បន្ថែមអ្នកប្រើប្រាស់
                                </a>
                            </div>





                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-hover" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>នាមត្រកូល</th>
                                        <th>នាម</th>
                                        <th>ឈ្មោះអ្នកប្រើប្រាស់</th>
                                        <th>អ៊ីមែល</th>
                                        <th>លេខទូរស័ព្ទ</th>
                                        <th>ដេប៉ាតឺម៉ង</th>
                                        <th>User_role</th>
                                        <th>សកម្មភាព</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    <?php
                                    $i = 1; // Initialize $i outside the loop

                                    foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $i; ?></td> <!-- Display the current iteration number -->
                                            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                            <td><?php echo !empty($user['department_name']) ? htmlspecialchars($user['department_name']) : 'មិនមាន'; ?></td>

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

                                            <td>
                                                <?php echo ($user['status'] == 1) ? 'Active' : 'Inactive'; // Display "Active" or "Inactive" 
                                                ?>
                                            </td>
                                            <td>
                                                <a class="" href="edit_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>">
                                                    <i class="fas fa-edit"></i> កែប្រែ
                                                </a>
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
            </main>
        </div>
    </div>



    <!-- jQuery (required by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <?php include_once('../../include/data_table.php'); ?>
    <?php include_once('../../include/footer.html'); ?>
</body>

</html>