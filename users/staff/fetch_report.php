<?php
header('Content-Type: application/json');
include('../../conn_db1.php');

// Function to validate date inputs
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

$query = "SELECT lr.id, 
                 CONCAT(ui.first_name, ' ', ui.last_name) AS fullname, 
                 lr.fromDate, 
                 lr.toDate, 
                 lr.total_days, 
                 lr.reason, 
                 lr.status, 
                 COALESCE(CONCAT(approver.first_name, ' ', approver.last_name), '') AS approver_name
          FROM leave_requests lr
          JOIN user_info ui ON lr.user_id = ui.user_id
          LEFT JOIN user_info approver ON lr.approved_by = approver.user_id";

$types = '';
$params = [];
$whereClauses = [];

// Check if we need to fetch all data
$fetchAllData = $_POST['allData'] ?? false;

// If not fetching all data, handle filters
if (!$fetchAllData) {
    // Retrieve filter inputs safely
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $departmentId = $_POST['department'] ?? '';
    $userId = $_POST['user_id'] ?? ''; // Expecting user_id for filtering
    $fullName = $_POST['employeeName'] ?? ''; // Field for full name search
    $status = $_POST['status'] ?? '';
    $month = $_POST['month'] ?? '';
    $year = $_POST['year'] ?? '';

    // Base condition for WHERE clause
    $whereClauses[] = "1=1"; // Always true, helps with appending other conditions

    // Date filters
    if (!empty($startDate) && validateDate($startDate) && !empty($endDate) && validateDate($endDate)) {
        $whereClauses[] = "(lr.fromDate >= ? AND lr.toDate <= ?)";
        $types .= 'ss';
        $params[] = $startDate;
        $params[] = $endDate;
    }

    // Department filter
    if (!empty($departmentId) && is_numeric($departmentId)) {
        $whereClauses[] = "ui.department_id = ?";
        $types .= 'i';
        $params[] = $departmentId;
    }

    // Filter by user_id (if provided)
    if (!empty($userId) && is_numeric($userId)) {
        $whereClauses[] = "lr.user_id = ?";
        $types .= 'i';
        $params[] = $userId;
    }

    // Filter by full name (if provided)
    if (!empty($fullName)) {
        $whereClauses[] = "CONCAT(ui.first_name, ' ', ui.last_name) LIKE ?";
        $types .= 's';
        $params[] = '%' . $fullName . '%'; // Wildcard for partial matches
    }

    // Status filter
    if (!empty($status)) {
        $whereClauses[] = "lr.status = ?";
        $types .= 's';
        $params[] = $status;
    }

    // Month and Year filter
    if (!empty($month) && !empty($year) && is_numeric($month) && is_numeric($year)) {
        $whereClauses[] = "YEAR(lr.fromDate) = ? AND MONTH(lr.fromDate) = ?";
        $types .= 'ii'; // Changed to 'ii' since month and year are integers
        $params[] = $year;
        $params[] = $month;
    }

    // Construct the WHERE clause if there are any conditions
    if (count($whereClauses) > 0) {
        $query .= " WHERE " . implode(' AND ', $whereClauses);
    }
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die(json_encode(['error' => 'Query preparation failed: ' . $conn->error]));
}

if ($types) {
    $stmt->bind_param($types, ...$params);
}

// Execute the statement
if (!$stmt->execute()) {
    die(json_encode(['error' => 'Query execution failed: ' . $stmt->error]));
}

// Fetch the results
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Return JSON response
echo json_encode($data);

// Close the statement and connection
$stmt->close();
$conn->close();
