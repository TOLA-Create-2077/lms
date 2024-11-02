<?php
// Include required files
include('../conn_db1.php');
require('../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Retrieve parameters from the URL
    $startDate = $_GET['startDate'] ?? '';
    $endDate = $_GET['endDate'] ?? '';
    $department = $_GET['department'] ?? '';
    $employeeName = $_GET['employeeName'] ?? '';
    $status = $_GET['status'] ?? '';
    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';

    // Base SQL query with joins
    $sql = "SELECT lr.*, 
    CONCAT(ui.first_name, ' ', ui.last_name) AS employee_full_name, 
    d.department_name, 
    CONCAT(approver.first_name, ' ', approver.last_name) AS approver_full_name
    FROM leave_requests lr
    JOIN user_info ui ON lr.user_id = ui.user_id
    LEFT JOIN departments d ON lr.department_id = d.department_id
    LEFT JOIN user_info approver ON lr.approved_by = approver.user_id
    WHERE 1=1";

    // Apply filters to the SQL query
    if ($startDate) {
        $sql .= " AND lr.fromDate >= '$startDate'";
    }
    if ($endDate) {
        $sql .= " AND lr.toDate <= '$endDate'";
    }
    if ($department) {
        $sql .= " AND lr.department_id = '$department'";
    }
    if ($employeeName) {
        // Adjust full name filter to use CONCAT in SQL
        $sql .= " AND CONCAT(ui.first_name, ' ', ui.last_name) LIKE '%$employeeName%'";
    }
    if ($status) {
        $sql .= " AND lr.status = '$status'";
    }
    if ($month && $year) {
        $sql .= " AND MONTH(lr.fromDate) = '$month' AND YEAR(lr.fromDate) = '$year'";
    }

    $result = $conn->query($sql);

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
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(30);
    $sheet->getColumnDimension('H')->setWidth(25);
    $sheet->getColumnDimension('I')->setWidth(25);
    $sheet->getColumnDimension('J')->setWidth(20);
    $sheet->getColumnDimension('K')->setWidth(20);
    $sheet->getColumnDimension('L')->setWidth(25);

    // Populate data rows
    $row = 3; // Start at row 3
    $i = 1;   // Initialize row counter
    while ($data = $result->fetch_assoc()) {
        $sheet
            ->setCellValue('A' . $row, $i) // Row number
            ->setCellValue('B' . $row, $data['employee_full_name']) // Employee's full name
            ->setCellValue('C' . $row, $data['fromDate'])
            ->setCellValue('D' . $row, $data['toDate'])
            ->setCellValue('E' . $row, $data['total_days'])
            ->setCellValue('F' . $row, $data['reason'])
            ->setCellValue('G' . $row, $data['status'])
            ->setCellValue('H' . $row, $data['date_send'])
            ->setCellValue('I' . $row, $data['approver_full_name']) // Approver's full name
            ->setCellValue('J' . $row, $data['department_name'])
            ->setCellValue('K' . $row, $data['updated_at'])
            ->setCellValue('L' . $row, $data['comment']);

        $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $row++;
        $i++;
    }

    // Generate Excel file
    $writer = new Xlsx($spreadsheet);
    $filename = 'leave_report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer->save('php://output');
    exit();
}
