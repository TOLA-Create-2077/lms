<?php
// Include necessary files
include_once('../../include/session.php');
include_once('../../include/sidebar.php');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <!-- Page Title -->
            <!-- <h3 class="fw-bold mb-3">របាយការណ៍</h3> -->
        </div>

        <!-- Export Button -->
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-success mb-3" onclick="exportFilteredData()">
                    <i class="fas fa-file-export"></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row" id="filterFormContainer">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">តម្រូវការរាយការណ៍</h4>
                    </div>
                    <div class="card-body">
                        <form id="reportFilterForm" method="POST">
                            <div class="row">
                                <!-- Start Date -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="startDate">កាលបរិច្ឆេទចាប់ផ្តើម</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate" />
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="endDate">កាលបរិច្ឆេទបញ្ចប់</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate" />
                                    </div>
                                </div>

                                <!-- Department -->
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
                                                while ($dept = $result->fetch_assoc()) {
                                                    echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
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

                            <!-- Additional Filters -->
                            <div class="row">
                                <!-- Employee Name -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="employeeName">ឈ្មោះបុគ្គលិក</label>
                                        <input type="text" class="form-control" id="employeeName" name="employeeName" placeholder="បញ្ចូលឈ្មោះបុគ្គលិក" />
                                        <div id="searchResults" class="mt-2"></div> <!-- Search results container -->
                                    </div>
                                </div>

                                <!-- Month -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="month">ខែ</label>
                                        <select class="form-control" id="month" name="month">
                                            <option value="">ជ្រើសរើសខែ</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Year -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">ឆ្នាំ</label>
                                        <select class="form-control" id="year" name="year">
                                            <option value="">ជ្រើសរើសឆ្នាំ</option>
                                            <?php for ($i = 2022; $i <= date("Y"); $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter and Reset Buttons -->
                            <div class="d-flex justify-content-end">
                                <a href="reports.php" class="btn btn-danger">
                                    <i class="fas fa-undo"></i> សម្អាត
                                </a>&nbsp;
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> ស្វែងរក
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Table -->
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
                                            <th>#</th>
                                            <th>ឈ្មោះ</th>
                                            <th>ដេប៉ាតឺម៉ង់</th>
                                            <th>ចំនួនសុំច្បាប់សរុប</th>
                                            <th>មើលលម្អិត</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                            // Apply filters from the form submission
                                            $startDate = $_POST['startDate'] ?? '';
                                            $endDate = $_POST['endDate'] ?? '';
                                            $department = $_POST['department'] ?? '';
                                            $employeeName = $_POST['employeeName'] ?? '';
                                            $month = $_POST['month'] ?? '';
                                            $year = $_POST['year'] ?? '';

                                            // Query with filters
                                            $sql = "SELECT 
                    leave_requests.id, 
                    user_info.first_name, 
                    user_info.last_name, 
                    departments.department_name, 
                    COUNT(leave_requests.id) AS total_requests 
                FROM leave_requests
                JOIN user_info ON leave_requests.user_id = user_info.user_id
                JOIN departments ON user_info.department_id = departments.department_id
                WHERE 1";

                                            // Apply filters (if any)
                                            if ($startDate) {
                                                $sql .= " AND leave_requests.fromDate >= '$startDate'";
                                            }
                                            if ($endDate) {
                                                $sql .= " AND leave_requests.toDate <= '$endDate'";
                                            }
                                            if ($department) {
                                                $sql .= " AND user_info.department_id = '$department'";
                                            }
                                            if ($employeeName) {
                                                $sql .= " AND (user_info.first_name LIKE '%$employeeName%' OR user_info.last_name LIKE '%$employeeName%')";
                                            }
                                            if ($month) {
                                                $sql .= " AND MONTH(leave_requests.fromDate) = '$month'";
                                            }
                                            if ($year) {
                                                $sql .= " AND YEAR(leave_requests.fromDate) = '$year'";
                                            }

                                            $sql .= " GROUP BY leave_requests.user_id";

                                            // Execute the query
                                            include('../../conn_db1.php');
                                            $result = $conn->query($sql);

                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['department_name']}</td>
                        <td>{$row['total_requests']}</td>
                        <td><a href='view_details.php?id={$row['id']}'>View Details</a></td>
                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No records found</td></tr>";
                                            }

                                            $conn->close();
                                        } else {
                                            // If no form is submitted, fetch all records
                                            include('../../conn_db1.php');
                                            $sql = "SELECT 
                    leave_requests.id, 
                    user_info.first_name, 
                    user_info.last_name, 
                    departments.department_name, 
                    COUNT(leave_requests.id) AS total_requests 
                FROM leave_requests
                JOIN user_info ON leave_requests.user_id = user_info.user_id
                JOIN departments ON user_info.department_id = departments.department_id
                GROUP BY leave_requests.user_id";

                                            $result = $conn->query($sql);

                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['department_name']}</td>
                        <td>{$row['total_requests']}</td>
                        <td><a href='view_details.php?id={$row['id']}'>View Details</a></td>
                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No records found</td></tr>";
                                            }

                                            $conn->close();
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
    </div>
</div>
<!-- Add reference to the xlsx library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script>
    // Export to Excel function with styling
    function exportFilteredData() {
        // Get table and rows
        var table = document.getElementById("reportTable");
        var rows = table.rows;

        // Prepare an array for data
        var data = [];
        var header = [];

        // Add title to the top of the Excel sheet
        data.push(["Leave Requests Report"]);

        // Add header row with custom background
        for (var i = 0; i < rows[0].cells.length; i++) {
            header.push(rows[0].cells[i].innerText);
        }
        data.push(header);

        // Add table data rows
        for (var i = 1; i < rows.length; i++) {
            var rowData = [];
            for (var j = 0; j < rows[i].cells.length; j++) {
                rowData.push(rows[i].cells[j].innerText);
            }
            data.push(rowData);
        }

        // Create a new worksheet and apply styles
        var ws = XLSX.utils.aoa_to_sheet(data);

        // Style for title and headers
        var titleCell = {
            v: "Leave Requests Report",
            s: {
                font: {
                    bold: true,
                    size: 16,
                    name: 'Khmer OS Battambang'
                },
                alignment: {
                    horizontal: 'center'
                }
            }
        };
        ws["!merges"] = [{
            s: {
                r: 0,
                c: 0
            },
            e: {
                r: 0,
                c: header.length - 1
            }
        }]; // Merge title row
        ws["A1"] = titleCell;

        // Add Khmer font style to headers
        for (var col = 0; col < header.length; col++) {
            var cell = ws[XLSX.utils.encode_cell({
                r: 1,
                c: col
            })];
            cell.s = {
                font: {
                    name: "Khmer OS Battambang",
                    bold: true
                },
                fill: {
                    bgColor: {
                        rgb: "D3D3D3"
                    }
                }
            }; // Set background color for headers
        }

        // Add Khmer font style to data rows
        for (var row = 2; row < data.length; row++) {
            for (var col = 0; col < header.length; col++) {
                var cell = ws[XLSX.utils.encode_cell({
                    r: row,
                    c: col
                })];
                cell.s = {
                    font: {
                        name: "Khmer OS Battambang"
                    }
                };
            }
        }

        // Create the Excel workbook
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Leave Requests");

        // Export the Excel file
        XLSX.writeFile(wb, "leave_requests_report.xlsx");
    }
    // Create a new worksheet with styling
    var ws = XLSX.utils.aoa_to_sheet(data);

    // Apply style for title
    ws["A1"].s = {
        font: {
            bold: true,
            size: 16,
            name: "Khmer OS Battambang"
        },
        alignment: {
            horizontal: "center"
        }
    };

    // Apply styles for headers and data rows
    data.forEach((row, rowIndex) => {
        row.forEach((_, colIndex) => {
            var cellAddress = XLSX.utils.encode_cell({
                r: rowIndex,
                c: colIndex
            });
            if (!ws[cellAddress]) return; // Skip empty cells

            if (rowIndex === 1) { // Header row
                ws[cellAddress].s = {
                    font: {
                        bold: true,
                        name: "Khmer OS Battambang"
                    },
                    fill: {
                        fgColor: {
                            rgb: "D3D3D3"
                        }
                    }
                };
            } else { // Data rows
                ws[cellAddress].s = {
                    font: {
                        name: "Khmer OS Battambang"
                    }
                };
            }
        });
    });

    // Export the workbook
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Leave Requests");
    XLSX.writeFile(wb, "leave_requests_report.xlsx");
</script>