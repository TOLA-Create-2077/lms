<?php
// Include required files
require '../vendor/autoload.php';
include('../conn_db1.php'); // Database connection

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Retrieve filter parameters from the GET request
$user_id = $_GET['user_id'] ?? null;
$status = $_GET['status'] ?? null;
$month = $_GET['month'] ?? null;
$year = $_GET['year'] ?? null;

// Build the SQL query with optional filters
$sql = "
    SELECT lr.id, ui.first_name AS requester_first_name, ui.last_name AS requester_last_name, 
           lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.date_send, 
           approver.first_name AS approver_first_name, approver.last_name AS approver_last_name, 
           d.department_name, lr.updated_at, lr.comment
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    LEFT JOIN user_info approver ON lr.approved_by = approver.user_id
    LEFT JOIN departments d ON lr.department_id = d.department_id
    WHERE lr.user_id = '" . $conn->real_escape_string($user_id) . "'";

// Apply additional filters based on user input
if (!empty($status)) {
    $sql .= " AND lr.status = '" . $conn->real_escape_string($status) . "'";
}
if (!empty($month)) {
    $sql .= " AND MONTH(lr.fromDate) = '" . $conn->real_escape_string($month) . "'";
}
if (!empty($year)) {
    $sql .= " AND YEAR(lr.fromDate) = '" . $conn->real_escape_string($year) . "'";
}

// Execute the query
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch results
$filteredRequests = [];
while ($row = $result->fetch_assoc()) {
    $filteredRequests[] = $row;
}

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');

// Title and styling
$sheet->setCellValue('A1', 'របាយការណ៍ការសុំច្បាប់') // Leave Report
    ->mergeCells('A1:L1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'name' => 'Khmer OS Battambang',
        'bold' => true,
        'size' => 14,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => '#b5b5b5'],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
]);

// Header setup
$sheet
    ->setCellValue('A2', '#')
    ->setCellValue('B2', 'ឈ្មោះអ្នកប្រើប្រាស់') // Username
    ->setCellValue('C2', 'កាលបរិច្ឆេទចាប់ផ្តើម') // Start Date
    ->setCellValue('D2', 'កាលបរិច្ឆេទបញ្ចប់') // End Date
    ->setCellValue('E2', 'ចំនួនថ្ងៃ') // Total Days
    ->setCellValue('F2', 'មូលហេតុ') // Reason
    ->setCellValue('G2', 'ស្ថានភាព') // Status
    ->setCellValue('H2', 'ថ្ងៃស្នើសុំច្បាប់') // Created At
    ->setCellValue('I2', 'អនុម័តដោយ') // Approved By
    ->setCellValue('J2', 'ដេប៉ាតឺម៉ង់') // Department Name
    ->setCellValue('K2', 'បានធ្វើបច្ចុប្បន្នភាព') // Updated At
    ->setCellValue('L2', 'មតិយោបល់'); // Comment

$sheet->getStyle('A2:L2')->applyFromArray([
    'font' => [
        'name' => 'Khmer OS Battambang',
        'bold' => true,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => 'e3dfde'],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
]);

// Set column widths
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(18);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(18);
$sheet->getColumnDimension('L')->setWidth(25);

// Fill in data rows
$rowIndex = 3;
foreach ($filteredRequests as $data) {
    $sheet->fromArray([
        $data['id'],
        $data['requester_first_name'] . ' ' . $data['requester_last_name'],
        date('d-m-Y', strtotime($data['fromDate'])),
        date('d-m-Y', strtotime($data['toDate'])),
        $data['total_days'],
        $data['reason'],
        $data['status'],
        date('d-m-Y', strtotime($data['date_send'])),
        $data['approver_first_name'] . ' ' . $data['approver_last_name'], // Approved By Name
        $data['department_name'],
        date('d-m-Y', strtotime($data['updated_at'])),
        $data['comment']
    ], null, 'A' . $rowIndex);
    $rowIndex++;
}

// Save as an Excel file
$writer = new Xlsx($spreadsheet);
$exportFile = 'leave_requests_export_user_' . $user_id . '.xlsx';
$writer->save($exportFile);

// Output download headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($exportFile) . '"');
header('Cache-Control: max-age=0');
readfile($exportFile);
unlink($exportFile); // Clean up
exit;
