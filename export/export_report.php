<?php
// Include necessary files
include('../conn_db1.php');
require_once '../vendor/autoload.php'; // Autoload PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch form data (filters from the form)
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';
$department = $_POST['department'] ?? '';
$employeeName = $_POST['employeeName'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';

// Start building the SQL query with filters
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

// Apply filters if they are provided
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

// Group by user ID to aggregate data
$sql .= " GROUP BY leave_requests.user_id";

// Execute the query to fetch filtered results
$result = $conn->query($sql);

// Check if any results were returned
if ($result->num_rows == 0) {
    die("No data found for the given filters.");
}

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the header row
$sheet->setCellValue('A1', 'លេខកូដ')
    ->setCellValue('B1', 'ឈ្មោះ')
    ->setCellValue('C1', 'ដេប៉ាតឺម៉ង់')
    ->setCellValue('D1', 'ចំនួនសំណើច្បាប់សរុប')
    ->setCellValue('E1', 'មើលលម្អិត');

// Start filling the table with data
$row = 2; // Starting from row 2 to avoid overwriting header
while ($row_data = $result->fetch_assoc()) {
    $sheet->setCellValue("A$row", $row_data['id']);
    $sheet->setCellValue("B$row", $row_data['first_name'] . ' ' . $row_data['last_name']);
    $sheet->setCellValue("C$row", $row_data['department_name']);
    $sheet->setCellValue("D$row", $row_data['total_requests']);
    $sheet->setCellValue("E$row", 'View Details'); // Example static text; can link if needed
    $row++;
}

// Set column widths to auto-size
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set the headers for the file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="leave_report.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to the browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Close the database connection
$conn->close();
