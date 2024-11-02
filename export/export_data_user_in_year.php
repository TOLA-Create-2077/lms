<?php
// Include required files
require '../vendor/autoload.php';
include('../conn_db1.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch data for the current year
$sql = "
    SELECT lr.id, ui.first_name, ui.last_name, lr.fromDate, lr.toDate, lr.total_days, lr.reason, 
           lr.status, lr.date_send, approver.first_name AS approver_first_name, 
           approver.last_name AS approver_last_name, d.department_name, lr.updated_at, lr.comment
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    LEFT JOIN user_info approver ON lr.approved_by = approver.user_id
    LEFT JOIN departments d ON lr.department_id = d.department_id
    WHERE year(lr.fromDate) = year(CURDATE())
    AND lr.status = 'អនុញ្ញាត'"; // Approved status

$result = $conn->query($sql);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch all results into an array
$yearlyRequestsResult = [];
while ($row = $result->fetch_assoc()) {
    $yearlyRequestsResult[] = $row;
}

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');

// Title and styling
$sheet->setCellValue('A1', 'របាយការណ៍សំណើច្បាប់ប្រចាំឆ្នាំ') // Yearly Leave Request Report
    ->mergeCells('A1:L1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'name' => 'Khmer OS Moul Light',
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
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(15);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(25);
$sheet->getColumnDimension('I')->setWidth(25);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(25);
$sheet->getColumnDimension('L')->setWidth(30);

// Initialize the row counter
$i = 3; // Start from the third row for data entries

// Populate data rows
foreach ($yearlyRequestsResult as $request) {
    $approvedBy = $request['approver_first_name'] . ' ' . $request['approver_last_name'];
    $sheet
        ->setCellValue('A' . $i, $i - 2) // Row number
        ->setCellValue('B' . $i, $request['first_name'] . ' ' . $request['last_name']) // Applicant
        ->setCellValue('C' . $i, $request['fromDate']) // Start Date
        ->setCellValue('D' . $i, $request['toDate']) // End Date
        ->setCellValue('E' . $i, $request['total_days']) // Total Days
        ->setCellValue('F' . $i, $request['reason']) // Reason
        ->setCellValue('G' . $i, $request['status']) // Status
        ->setCellValue('H' . $i, $request['date_send']) // Created At
        ->setCellValue('I' . $i, $approvedBy) // Approved By
        ->setCellValue('J' . $i, $request['department_name']) // Department Name
        ->setCellValue('K' . $i, $request['updated_at']) // Updated At
        ->setCellValue('L' . $i, $request['comment']); // Comment

    // Center align for the current row
    $sheet->getStyle('A' . $i . ':L' . $i)->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ]);

    // Increment the row counter
    $i++;
}

// Generate Excel file
$writer = new Xlsx($spreadsheet);
$filename = 'yearly_leave_requests_report.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');
exit();
