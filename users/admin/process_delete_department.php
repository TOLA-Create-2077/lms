<?php
include_once('../../include/session_admin.php');
include_once('../../conn_db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['department_id'])) {
        $department_id = $_POST['department_id'];

        $host = 'localhost';
        $dbname = 'lms';
        $username = 'root';
        $password = '';
        $mysqli = new mysqli($host, $username, $password, $dbname);

        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("DELETE FROM departments WHERE department_id = ?");
        $stmt->bind_param("i", $department_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
        $mysqli->close();
    }
}
