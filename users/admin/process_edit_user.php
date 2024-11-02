<?php
// Start the session and include necessary files
session_start();
include('../../conn_db.php'); // Ensure the path to your database connection is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Retrieve and sanitize form inputs
        $user_id = intval($_POST['user_id']);
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));
        $department_id = intval($_POST['department_id']);
        $role = htmlspecialchars(trim($_POST['role']));
        $status = intval($_POST['status']); // 1 for active, 0 for inactive

        // Handle image upload
        $image_url = $_FILES['image_url']['name'] ? uploadImage($_FILES['image_url']) : null;

        // Prepare the SQL update statement
        $sql = "UPDATE user_info SET first_name = :first_name, last_name = :last_name, username = :username, 
                email = :email, phone_number = :phone_number, department_id = :department_id, role = :role, 
                status = :status" . ($image_url ? ", image_url = :image_url" : "") . " 
                WHERE user_id = :user_id";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $user_id);

        if ($image_url) {
            $stmt->bindParam(':image_url', $image_url);
        }

        // Execute the statement
        $stmt->execute();

        // Success message
        $_SESSION['success'] = 'អ្នកប្រើប្រាស់បានកែប្រែដោយជោគជ័យ។'; // User updated successfully
        header('Location: manage_users.php'); // Redirect to manage_users
        exit();
    } catch (PDOException $e) {
        // Error handling
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = 'ឈ្មោះអ្នកប្រើប្រាស់នេះមានរួចហើយ។'; // This username already exists
        } else {
            $_SESSION['error'] = 'មិនអាចកែប្រែអ្នកប្រើប្រាស់បានទេ។'; // Failed to update user
        }
        header('Location: edit_user.php?id=' . $user_id);
        exit();
    }
}

// Function to handle image upload
function uploadImage($file)
{
    $target_dir = "../../assets/img/"; // Define your upload directory
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        $_SESSION['error'] = "ឯកសារមិនមែនជារូបភាពទេ។"; // File is not an image
        $uploadOk = 0;
    }

    // Check file size (e.g., 5MB limit)
    if ($file["size"] > 5000000) {
        $_SESSION['error'] = "សូមអភ័យទោស, ឯកសាររបស់អ្នកធំជាងនេះ។"; // File is too large
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        $_SESSION['error'] = "សូមអភ័យទោស, តែឯកសារប្រភេទ JPG, JPEG, PNG និង GIF តែប៉ុណ្ណោះដែលត្រូវអនុញ្ញាត។"; // Allowed file types
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return null; // Return null if upload fails
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file; // Return the path of the uploaded file
        } else {
            $_SESSION['error'] = "សូមអភ័យទោស, មានកំហុសក្នុងការផ្ទុកឯកសាររបស់អ្នក។"; // Error uploading file
            return null;
        }
    }
}
