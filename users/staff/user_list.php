<?php
include('../../include/session.php');
include_once('../../include/sidebar.php');
include('../../conn_db.php'); // Connect to the database
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>

            </div>
        </div>

        <!-- User Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">សមាជិកអ្នកប្រើប្រាស់</h4>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>នាមត្រកូល</th>
                                    <th>នាម</th>
                                    <th>ដេប៉ាតឺម៉ង់</th> <!-- Department -->
                                    <th>អ៊ីមែល</th>
                                    <th>លេខទូរស័ព្ទ</th>
                                    <th>សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // SQL Query to fetch user data with department name
                                $sql = "SELECT u.user_id, u.first_name, u.last_name, d.department_name, u.email, u.phone_number 
                                    FROM user_info u
                                    JOIN departments d ON u.department_id = d.department_id 
                                    WHERE u.role = 'user'";
                                $result = $conn->query($sql);

                                if ($result->rowCount() > 0) {
                                    $i = 1; // Initialize the counter inside the loop
                                    // Output data of each row
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . $row['first_name'] . "</td>";
                                        echo "<td>" . $row['last_name'] . "</td>";
                                        echo "<td>" . $row['department_name'] . "</td>"; // Display department name
                                        echo "<td>" . $row['email'] . "</td>";
                                        echo "<td>" . $row['phone_number'] . "</td>";
                                        // Button to view user leave request details
                                        echo '<td><a href="leave_details.php?user_id=' . $row['user_id'] . '" class="btn btn-info">  <i class="fas fa-eye"></i> </a></td>';
                                        echo "</tr>";
                                        $i++; // Increment counter inside the loop
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>មិនមានអ្នកប្រើប្រាស់ទេ។</td></tr>"; // Adjusted colspan for seven columns
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
<footer class="footer">
    <div class="container-fluid">
        <div class="copyright text-center">
            <p class="text-muted mb-0">© 2024 ក្រុមហ៊ុនរបស់អ្នក។ សិទ្ធិគ្រប់គ្រាន់។</p>
        </div>
    </div>
</footer>

<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>


<?php include_once('../../include/footer.html'); ?>
<?php include_once('../../include/data_table.php'); ?>
</body>

</html>