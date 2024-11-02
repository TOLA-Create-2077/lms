<?php
$host = 'localhost'; // Your database host
$db = 'lms'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


?>
    