<?php
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Create a new mysqli connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
