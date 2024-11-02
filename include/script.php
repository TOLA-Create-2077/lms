<?php include_once('../../include/session.php'); ?>
<?php include('../../include/sidebar.php'); ?>

<!-- Bootstrap 5 CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome for Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<?php
include('../../conn_db.php');

// SQL Query to fetch user data with department name
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.username, u.email, u.phone_number, d.department_name, u.role
        FROM user_info u
        JOIN departments d ON u.department_id = d.department_id 
        WHERE u.role = 'user'";
$result = $conn->query($sql);
$users = $result->fetchAll();
?>

<div class="container">

    <body class="sb-nav-fixed">

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">

            </div>

            <main>
                <div class="container-fluid px-4">
                    <div class="mt-4" id="title">
                        <div id="left">
                            <h1>បញ្ជីអ្នកប្រើប្រាស់</h1>
                        </div>
                        <div id="right">

                        </div>
                    </div>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">បញ្ជីអ្នកប្រើប្រាស់</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <a href="add_user.php" class="btn btn-primary" style="float:right"><i class="fa-solid fa-square-plus"></i> បន្ថែម</a>

                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-hover" id="userTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>នាមត្រកូល</th>
                                        <th>នាម</th>
                                        <th>ឈ្មោះអ្នកប្រើប្រាស់</th>
                                        <th>អ៊ីមែល</th>
                                        <th>លេខទូរស័ព្ទ</th>
                                        <th>ដេប៉ាតឺម៉ង</th>
                                        <th>តូនាទី</th>
                                        <th>សកម្មភាព</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                            <td><?php echo htmlspecialchars($user['department_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?php echo htmlspecialchars($user['user_id']); ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                        សកម្មភាព <i class="fas fa-caret-down"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo htmlspecialchars($user['user_id']); ?>">
                                                        <a class="dropdown-item" href="edit_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>">
                                                            <i class="fas fa-edit"></i> កែប្រែ
                                                        </a>
                                                        <a class="dropdown-item" href="process_delete_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>" onclick="return confirm('តើអ្នកពិតជាចង់លុបអ្នកប្រើប្រាស់នេះឬ?');">
                                                            <i class="fas fa-trash"></i> លុប
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <nav aria-label="Page navigation example">
                                <ul class="pagination" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
        </div>
</div>

</div>

<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        var table = $('#userTable').DataTable({
            "pageLength": 10, // Default number of rows to display
            "lengthMenu": [10, 25, 50], // Options for rows to display
            "searching": true, // Enable searching
            "info": false, // Disable info display
            "pagingType": "simple", // Change to simple pagination
            "drawCallback": function(settings) {
                var api = this.api();
                var pageInfo = api.page.info();
                var pagination = $('#pagination');

                // Clear existing pagination
                pagination.empty();

                // Add Previous button
                if (pageInfo.page === 0) {
                    pagination.append('<li class="page-item disabled"><span class="page-link">Previous</span></li>');
                } else {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-dt-idx="' + pageInfo.page + '" tabindex="0">Previous</a></li>');
                }

                // Add page numbers
                for (var i = 0; i < pageInfo.pages; i++) {
                    if (i === pageInfo.page) {
                        pagination.append('<li class="page-item active" aria-current="page"><span class="page-link">' + (i + 1) + '</span></li>');
                    } else {
                        pagination.append('<li class="page-item"><a class="page-link" href="#" data-dt-idx="' + i + '" tabindex="0">' + (i + 1) + '</a></li>');
                    }
                }

                // Add Next button
                if (pageInfo.page === pageInfo.pages - 1) {
                    pagination.append('<li class="page-item disabled"><span class="page-link">Next</span></li>');
                } else {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-dt-idx="' + (pageInfo.page + 1) + '" tabindex="0">Next</a></li>');
                }
            }
        });

        // Handle pagination click
        $('#pagination').on('click', 'a', function(e) {
            e.preventDefault();
            var pageIndex = $(this).data('dt-idx');
            table.page(pageIndex).draw(false);
        });
    });
</script>

<?php include_once('../../include/footer.html'); ?>