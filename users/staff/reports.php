<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
include_once('../../include/session.php');
include_once('../../include/sidebar.php');
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">របាយការណ៍</h3>
            </div>
        </div>

        <!-- Button to toggle filter form -->
        <div class="row">
            <div class="col-md-12">
                <button id="toggleFilterForm" class="btn btn-secondary mb-3">
                    <i class="fas fa-filter"></i> Show Filter
                </button>
                <button type="button" class="btn btn-success mb-3" onclick="exportFilteredData()">
                    <i class="fas fa-file-export"></i> Export
                </button>

                <script>
                    document.getElementById('toggleFilterForm').onclick = function() {
                        let filterForm = document.getElementById('filterFormContainer');
                        filterForm.style.display = filterForm.style.display === "none" ? "block" : "none";
                        this.innerHTML = filterForm.style.display === "block" ?
                            '<i class="fas fa-filter"></i> Hide Filter' :
                            '<i class="fas fa-filter"></i> Show Filter';
                    };

                    function exportFilteredData() {
                        let startDate = document.querySelector('input[name="startDate"]').value;
                        let endDate = document.querySelector('input[name="endDate"]').value;
                        let department = document.querySelector('select[name="department"]').value;
                        let fullName = document.querySelector('input[name="employeeName"]').value;
                        let status = document.querySelector('select[name="status"]').value;
                        let month = document.querySelector('select[name="month"]').value;
                        let year = document.querySelector('select[name="year"]').value;

                        window.location.href = `../../export/export_report.php?startDate=${startDate}&endDate=${endDate}&department=${department}&employeeName=${fullName}&status=${status}&month=${month}&year=${year}`;
                    }
                </script>


            </div>
        </div>

        <!-- Filter form -->
        <div class="row" id="filterFormContainer" style="display:none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">តម្រូវការរាយការណ៍</h4>
                    </div>
                    <div class="card-body">
                        <form id="reportFilterForm" method="POST" action="path/to/your/report_script.php">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="startDate">កាលបរិច្ឆេទចាប់ផ្តើម</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="endDate">កាលបរិច្ឆេទបញ្ចប់</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department">ដេប៉ាតឺម៉ង់</label>
                                        <select class="form-control" id="department" name="department">
                                            <option value="">ជ្រើសរើសដេប៉ាតឺម៉ង់</option>
                                            <?php
                                            include('../../conn_db1.php');
                                            $sql = "SELECT department_id, department_name FROM departments ORDER BY department_name ASC";
                                            $result = $conn->query($sql);

                                            if ($result) {
                                                if ($result->num_rows > 0) {
                                                    while ($dept = $result->fetch_assoc()) {
                                                        echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No departments found</option>";
                                                }
                                            } else {
                                                echo "<option value=''>Error fetching departments: " . $conn->error . "</option>";
                                            }

                                            $conn->close();
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="employeeName">ឈ្មោះបុគ្គលិក</label>
                                        <input type="text" class="form-control" id="employeeName" name="employeeName" placeholder="បញ្ចូលឈ្មោះបុគ្គលិក" />
                                        <div id="searchResults" class="mt-2"></div> <!-- Container for search results -->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">ស្ថានភាព</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">ជ្រើសរើសស្ថានភាព</option>
                                            <option value="អនុញ្ញាត">អនុញ្ញាត</option>
                                            <option value="បដិសេធ">បដិសេធ</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="month">ខែ</label>
                                        <select class="form-control" id="month" name="month">
                                            <option value="">ជ្រើសរើសខែ</option>
                                            <?php
                                            for ($i = 1; $i <= 12; $i++) {
                                                echo "<option value='$i'>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">ឆ្នាំ</label>
                                        <select class="form-control" id="year" name="year">
                                            <option value="">ជ្រើសរើសឆ្នាំ</option>
                                            <?php
                                            for ($i = 2022; $i <= date("Y"); $i++) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-danger">
                                    <i class="fas fa-undo"></i> Clear
                                </button>&nbsp;
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>

        <!-- Report table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">របាយការណ៍សំណើច្បាប់</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="reportTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ឈ្មោះ</th>
                                    <th>កាលបរិច្ឆេទចាប់ផ្តើម</th>
                                    <th>កាលបរិច្ឆេទបញ្ចប់</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                    <th>លម្អិត</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated here via JavaScript -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include footer -->
<?php include_once('../../include/footer.html'); ?>

<!-- Include xlsx library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

<!-- JavaScript for managing filter form and generating report -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function liveSearch() {
        const input = document.getElementById('employeeName').value;
        const resultsDiv = document.getElementById('searchResults');

        if (input.length === 0) {
            // Clear results if input is empty
            resultsDiv.innerHTML = '';
            return;
        }

        // Create an AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'search_employee.php', true); // Point to your PHP file
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                // Update the results div with the response
                resultsDiv.innerHTML = this.responseText;
            }
        };
        xhr.send('name=' + encodeURIComponent(input)); // Send the input as a POST parameter
    }

    function fetchAllData() {
        fetch('fetch_report.php', {
                method: 'POST',
                body: new URLSearchParams({
                    allData: 'true'
                }) // Parameter to fetch all data
            })
            .then(response => response.json())
            .then(data => {
                var tableBody = document.querySelector('#reportTable tbody');
                tableBody.innerHTML = ''; // Clear previous data

                let i = 1; // Use `let` to declare the counter
                data.forEach(row => {
                    let tr = document.createElement('tr');

                    // Determine the badge class and translated status
                    let badgeClass;
                    let statusText;
                    switch (row.status) {
                        case 'អនុញ្ញាត':
                            badgeClass = 'success';
                            statusText = 'អនុញ្ញាត';
                            break;
                        case 'បដិសេធ':
                            badgeClass = 'danger';
                            statusText = 'បដិសេធ';
                            break;
                        default:
                            badgeClass = 'warning';
                            statusText = 'កំពុងរងចាំ';
                            break;
                    }

                    tr.innerHTML = `
                        <td>${i}</td>
                        <td>${row.fullname}</td>
                        <td>${row.fromDate}</td>
                        <td>${row.toDate}</td>
                        <td>${row.reason}</td>
                        <td><span class="badge bg-${badgeClass}">${statusText}</span></td>
                        <td>
                            <a href="view_leave_details.php?id=${row.id}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> 
                            </a>
                        </td>
                    `;
                    i++; // Increment counter for each row
                    tableBody.appendChild(tr);
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }



    // Call function to fetch all data on load
    window.onload = fetchAllData;

    // Add event listener for filter form submission
    document.getElementById('reportFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('fetch_report.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                var tableBody = document.querySelector('#reportTable tbody');
                tableBody.innerHTML = ''; // Clear previous data

                let i = 1;
                data.forEach(row => {
                    let tr = document.createElement('tr');

                    // Determine badge class and translated status text
                    let badgeClass;
                    let statusText;
                    switch (row.status) {
                        case 'អនុញ្ញាត':
                            badgeClass = 'success';
                            statusText = 'អនុញ្ញាត';
                            break;
                        case 'បដិសេធ':
                            badgeClass = 'danger';
                            statusText = 'បដិសេធ';
                            break;
                        default:
                            badgeClass = 'warning';
                            statusText = 'កំពុងរងចាំ';
                            break;
                    }

                    tr.innerHTML = `
                        <td>${i}</td>
                        <td>${row.fullname}</td>
                        <td>${row.fromDate}</td>
                        <td>${row.toDate}</td>
                        <td>${row.reason}</td>
                        <td><span class="badge bg-${badgeClass}">${statusText}</span></td>
                        <td>
                            <a href="view_leave_details.php?id=${row.id}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> 
                            </a>
                        </td>
                    `;
                    i++;
                    tableBody.appendChild(tr);
                });
            })
            .catch(error => console.error('Error fetching filtered data:', error));
    });
</script>