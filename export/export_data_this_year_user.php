<?php
// Include required files
include('../conn_db1.php');
require('../vendor/autoload.php');

// Start the session
session_start();

// Get the logged-in user's user_id from the session
$userId = $_SESSION['user_id'];

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get the current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');

    $sql = "SELECT lr.*, 
            ui.first_name AS employee_first_name, 
            ui.last_name AS employee_last_name, 
            d.department_name, 
            approver.first_name AS approver_first_name, 
            approver.last_name AS approver_last_name
        FROM leave_requests lr
        JOIN user_info ui ON lr.user_id = ui.user_id
        LEFT JOIN departments d ON lr.department_id = d.department_id
        LEFT JOIN user_info approver ON lr.approved_by = approver.user_id
        WHERE lr.user_id = '$userId' 
        AND lr.status = 'អនុញ្ញាត' 
        AND YEAR(lr.fromDate) = '$currentYear'
        ORDER BY lr.fromDate ASC";

    $result = $conn->query($sql);

    // Create spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Set default font for the entire spreadsheet to 'Khmer OS Battambang'
    $spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');

    // Title and styling for the title
    $sheet->setCellValue('A1', 'របាយការណ៍ការសុំច្បាប់') // Leave Report
        ->mergeCells('A1:N1');

    // Title styling
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

    // Header for the Excel file (removed ID and User ID)
    $sheet->setCellValue('A2', 'ID') // Serial No
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

    // Style for the header
    $sheet->getStyle('A2:L2')->applyFromArray([
        'font' => [
            'name' => 'Khmer OS Battambang',
            'bold' => true,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFb5b5b5'],
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
    $sheet->getColumnDimension('F')->setWidth(30);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(25);
    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->getColumnDimension('J')->setWidth(20);
    $sheet->getColumnDimension('K')->setWidth(25);
    $sheet->getColumnDimension('L')->setWidth(25);

    // Insert data from database
    $row = 3; // Start at row 3 because row 1 is for the title and row 2 is for the header
    $i = 1; // Initialize the serial number
    while ($data = $result->fetch_assoc()) {
        $employeeFullName = $data['employee_first_name'] . ' ' . $data['employee_last_name'];
        $approverFullName = $data['approved_by'] ? $data['approver_first_name'] . ' ' . $data['approver_last_name'] : '';

        $sheet->setCellValue('A' . $row, $i) // Serial No
            ->setCellValue('B' . $row, $employeeFullName)
            ->setCellValue('C' . $row, $data['fromDate'])
            ->setCellValue('D' . $row, $data['toDate'])
            ->setCellValue('E' . $row, $data['total_days'])
            ->setCellValue('F' . $row, $data['reason'])
            ->setCellValue('G' . $row, $data['status'])
            ->setCellValue('H' . $row, $data['date_send'])
            ->setCellValue('I' . $row, $approverFullName)
            ->setCellValue('J' . $row, $data['department_name'])
            ->setCellValue('K' . $row, $data['updated_at'])
            ->setCellValue('L' . $row, $data['comment']);

        // Apply center alignment for the row
        $sheet->getStyle('A' . $row . ':L' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $i++; // Increment the serial number
    }

    // Generate Excel file
    $writer = new Xlsx($spreadsheet);
    $filename = 'leave_report_current_month.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer->save('php://output');
    exit();
}
