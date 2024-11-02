<?php
include('../../include/session_users.php');
include('../../conn_db.php'); // រួមបញ្ចូលស្គ្រីបការតភ្ជាប់ទៅកាន់មូលដ្ឋានទិន្នន័យរបស់អ្នក

// ពិនិត្យមើលថាតើID ច្បាប់ឈប់សម្រាកត្រូវបានបញ្ជូនមកតាម GET ឬទេ
if (isset($_GET['id'])) {
    // ធ្វើការពិចារណាផ្នែកខាងក្នុងដើម្បីរាងសំណួរដើម្បីការពារ SQL injection
    $leave_id = htmlspecialchars($_GET['id']);

    try {
        // រៀបចំសំណើ SQL ដើម្បីធ្វើការកែប្រែស្ថានភាពសំណើឈប់សម្រាកទៅជា "Canceled"
        $sql = "UPDATE leave_requests SET status = 'បោះបង់', updated_at = NOW() WHERE id = :leave_id";

        // រៀបចំសំណើ
        $stmt = $conn->prepare($sql);

        // បញ្ជាក់ទំនាក់ទំនងID ច្បាប់ឈប់សម្រាក
        $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);

        // ធ្វើការប្រតិបត្តិសំណើ
        if ($stmt->execute()) {
            // ប្រសិនបើសំណើបានជោគជ័យ សូមកំណត់សារជោគជ័យក្នុងសមាជិក
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'ការស្នើសុំឈប់សម្រាកបានត្រូវបានបោះបង់ដោយជោគជ័យ។'
            ];
        } else {
            // ប្រសិនបើមានអ្វីមួយកើតឡើងកំណត់សារកំហុស
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'ការបោះបង់សំណើឈប់សម្រាកអមជ័យ។ សូមព្យាយាមម្តងទៀត។'
            ];
        }
    } catch (PDOException $e) {
        // នៅពេលមានកំហុសកំណត់វានិងបង្ហាញសារកំហុស
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'កំហុស: ' . $e->getMessage()
        ];
    }
} else {
    // ប្រសិនបើគ្មាន ID ត្រូវបានផ្តល់ សូមកំណត់សារកំហុស
    $_SESSION['alert'] = [
        'type' => 'warning',
        'message' => 'សំណើមិនត្រឹមត្រូវ។ គ្មាន ID សំណើឈប់សម្រាកបានផ្តល់ទេ។'
    ];
}

// បង្វិលត្រឡប់ទៅកាន់ទំព័រសំណើឈប់សម្រាក
header('Location: leave_list.php');
exit();
