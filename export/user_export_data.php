<?php
require '../../vendor/autoload.php'; // Include PhpSpreadsheet library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function exportToExcel($leaveRequests)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header values in Khmer
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'ឈ្មោះ');
    $sheet->setCellValue('C1', 'ថ្ងៃចាប់ផ្តើម');
    $sheet->setCellValue('D1', 'ថ្ងៃបញ្ចប់');
    $sheet->setCellValue('E1', 'ចំនួនថ្ងៃ');
    $sheet->setCellValue('F1', 'មូលហេតុ');
    $sheet->setCellValue('G1', 'ស្ថានភាព');

    // Populate data
    $row = 2; // Starting row for data
    foreach ($leaveRequests as $request) {
        $sheet->setCellValue('A' . $row, $request['leave_id']);
        $sheet->setCellValue('B' . $row, $request['first_name'] . ' ' . $request['last_name']);
        $sheet->setCellValue('C' . $row, $request['from_date']);
        $sheet->setCellValue('D' . $row, $request['to_date']);
        $sheet->setCellValue('E' . $row, $request['total_days']);
        $sheet->setCellValue('F' . $row, $request['reason']);
        $sheet->setCellValue('G' . $row, $request['status']);
        $row++;
    }

    // Set file name and output to browser
    $fileName = 'Leave_Requests_' . date('Y-m-d') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit();
}

// Check if the export button is clicked
if (isset($_GET['export'])) {
    // Get filtered data
    $leaveRequests = getLeaveRequests($statusFilter, $monthFilter, $yearFilter);

    // Export the filtered data to Excel
    exportToExcel($leaveRequests);
}
