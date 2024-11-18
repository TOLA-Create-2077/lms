<?php include_once('../../include/session_admin.php'); ?>
<?php include('../../include/sidebar.php'); ?>

<?php
// Function to connect to the database
function getConnection()
{
    $host = 'localhost';
    $dbname = 'lms';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("មិនអាចតភ្ជាប់ទៅមូលដ្ឋានទិន្នន័យបានទេ: " . $e->getMessage());
    }
}

// Fetch department data
function fetchDepartmentData()
{
    $pdo = getConnection();
    try {
        $sql = "SELECT * FROM departments";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "មានបញ្ហា​ក្នុងការទាញយកទិន្នន័យ: " . $e->getMessage();
        return [];
    }
}

$departmentData = fetchDepartmentData();
?>

<div class="container"><br><br>
    <div class="page-inner">
        <div id="right">
            <?php
            if (isset($_SESSION['status'])) {
                $alertClass = ($_SESSION['alert_type'] == 'danger') ? 'alert-danger' : 'alert-success';
            ?>
                <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['status']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['status']);
                unset($_SESSION['alert_type']); ?>
            <?php
            }
            ?>

        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">


                    <div class="card-header">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h1><i class="fa-solid fa-building"></i> ដេប៉ាតឺម៉ង់</h1>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                <i class="fa-solid fa-add"></i> បន្ថែមដេប៉ាតឺម៉ង់
                            </button>
                        </div>





                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ឈ្មោះផ្នែក</th>
                                    <th>កាលបរិច្ឆេទបង្កើត</th>
                                    <th>សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($departmentData as $data):  // Initialize $i outside the loop 
                                ?>
                                    <tr>
                                        <td><?php echo $i; ?></td> <!-- Display the current iteration number -->
                                        <td><?php echo htmlspecialchars($data['department_name']); ?></td>
                                        <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        <td>
                                            <a href="#" class="edit-department"
                                                data-id="<?php echo $data['department_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($data['department_name']); ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editDepartmentModal">
                                                <i class="fas fa-edit"></i> កែប្រែ
                                            </a>
                                            <a href="#" class="delete-department" data-id="<?php echo $data['department_id']; ?>">
                                                <i class="fas fa-trash"></i> លុប
                                            </a>

                                        </td>
                                    </tr>
                                <?php $i++; // Increment $i after each iteration 
                                endforeach;

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">បន្ថែមដេប៉ាតឺម៉ង់</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_add_department.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="departmentName" class="form-label">ឈ្មោះដេប៉ាតឺម៉ង់</label>
                        <input type="text" class="form-control" id="departmentName" name="department_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">កែសម្រួលដេប៉ាតឺម៉ង់</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm" method="POST" action="process_edit_department.php">
                    <input type="hidden" name="department_id" id="edit_department_id">
                    <div class="mb-3">
                        <label for="edit_department_name" class="form-label">ឈ្មោះដេប៉ាតឺម៉ង់</label>
                        <input type="text" class="form-control" id="edit_department_name" name="department_name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn btn-primary">រក្សាទុក</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Script to populate the modal with department data
    document.addEventListener('DOMContentLoaded', function() {
        const editLinks = document.querySelectorAll('.edit-department');

        editLinks.forEach(link => {
            link.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Populate the modal fields
                document.getElementById('edit_department_id').value = id;
                document.getElementById('edit_department_name').value = name;
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('.delete-department');

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                const departmentId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'តើអ្នកចង់លុបដេប៉ាតឺម៉ង់នេះ?',
                    text: "សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'យល់ព្រមលុប!',
                    cancelButtonText: 'បោះបង់'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send a POST request to delete the department
                        const formData = new FormData();
                        formData.append('department_id', departmentId);

                        fetch('process_delete_department.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text())
                            .then(data => {
                                Swal.fire('លុបរួច!', 'ដេប៉ាតឺម៉ង់ត្រូវបានលុប.', 'success')
                                    .then(() => {
                                        location.reload(); // Reload the page to update the table
                                    });
                            })
                            .catch(error => {
                                Swal.fire('មានបញ្ហា!', 'មិនអាចលុបបានទេ!', 'error');
                            });
                    }
                });
            });
        });
    });
</script>

<?php include_once('../../include/pagination.php'); ?>
<?php include_once('../../include/footer.html'); ?>