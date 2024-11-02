<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php"); // Change this to your login page
    exit;
}

// Require necessary files
require '../vendor/autoload.php';

$host = 'localhost'; // or your host
$username = 'root'; // your database username
$password = ''; // your database password
$dbname = 'lms'; // your database name

// Create connection
$db = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch filtered leave request data
$statusFilter = $_GET['status'] ?? '';
$monthFilter = $_GET['month'] ?? '';
$yearFilter = $_GET['year'] ?? '';
$userId = $_SESSION['user_id']; // Get logged-in user ID

$query = "SELECT lr.id, lr.user_id, lr.fromDate, lr.toDate, lr.total_days, lr.reason, lr.status, lr.approved_by, lr.department_id, lr.updated_at, lr.date_send, lr.comment, ui.first_name, ui.last_name 
          FROM leave_requests lr 
          LEFT JOIN user_info ui ON lr.user_id = ui.user_id 
          WHERE lr.user_id = ?"; // Only fetch requests for the logged-in user

// Prepare the statement
$stmt = $db->prepare($query);
$stmt->bind_param("i", $userId); // Bind the user_id parameter

// Add filters if set
if ($statusFilter) {
    $query .= " AND lr.status = ?";
    $stmt->bind_param("s", $statusFilter);
}
if ($monthFilter) {
    $query .= " AND MONTH(lr.fromDate) = ?";
    $stmt->bind_param("i", $monthFilter);
}
if ($yearFilter) {
    $query .= " AND YEAR(lr.fromDate) = ?";
    $stmt->bind_param("i", $yearFilter);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $db->error);
}

$leaveRequests = $result->fetch_all(MYSQLI_ASSOC);

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');

// Title and styling
$sheet->setCellValue('A1', 'របាយការណ៍ការសុំច្បាប់') // Leave Report
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
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
]);

// Set header row
$headerTitles = [
    '#',
    'ឈ្មោះអ្នកប្រើប្រាស់',
    'កាលបរិច្ឆេទចាប់ផ្តើម',
    'កាលបរិច្ឆេទបញ្ចប់',
    'ចំនួនថ្ងៃ',
    'មូលហេតុ',
    'ស្ថានភាព',
    'ថ្ងៃស្នើសុំច្បាប់',
    'អនុម័តដោយ',
    'ដេប៉ាតឺម៉ង់',
    'បានធ្វើបច្ចុប្បន្នភាព',
    'មតិយោបល់'
];

// Set header values and styles
foreach ($headerTitles as $key => $title) {
    $col = chr(65 + $key); // Convert to column letter (A, B, C, ...)
    $sheet->setCellValue($col . '2', $title);
    $sheet->getColumnDimension($col)->setWidth(20); // Set column width (adjust as necessary)
    $sheet->getStyle($col . '2')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FFFFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'b5b5b5'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
}

// Populate data rows
$rowNum = 3; // Start from the third row
$i = 1; // Initialize the counter for ID display
foreach ($leaveRequests as $request) {
    $sheet->setCellValue('A' . $rowNum, $i); // Set the sequential number for the ID column
    $sheet->setCellValue('B' . $rowNum, $request['first_name'] . ' ' . $request['last_name']); // Combine first and last name
    $sheet->setCellValue('C' . $rowNum, $request['fromDate']);
    $sheet->setCellValue('D' . $rowNum, $request['toDate']);
    $sheet->setCellValue('E' . $rowNum, $request['total_days']);
    $sheet->setCellValue('F' . $rowNum, $request['reason']);
    $sheet->setCellValue('G' . $rowNum, $request['status']);
    $sheet->setCellValue('H' . $rowNum, $request['date_send']);
    $sheet->setCellValue('I' . $rowNum, $request['approved_by']);
    $sheet->setCellValue('J' . $rowNum, $request['department_id']);
    $sheet->setCellValue('K' . $rowNum, $request['updated_at']);
    $sheet->setCellValue('L' . $rowNum, $request['comment']);

    // Center align the data cells
    for ($col = 'A'; $col <= 'L'; $col++) {
        $sheet->getStyle($col . $rowNum)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    $rowNum++;
    $i++; // Increment the counter
}

// Set filename and headers for download
$filename = 'Leave_Requests_' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Write the file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;