<?php
include('../../conn_db1.php');
// Adjust column names based on your database structure
$query = "SELECT d.department_name, COUNT(lr.id) AS leave_count 
          FROM departments d 
          LEFT JOIN leave_requests lr ON d.department_id = lr.department_id 
          GROUP BY d.department_name";

$result = $conn->query($query);

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'department_name' => $row['department_name'],
            'leave_count' => $row['leave_count']
        ];
    }
}

// Return JSON data
echo json_encode($data);
