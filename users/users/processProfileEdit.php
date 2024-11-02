<?php
// Include session management
include('../../include/session_users.php');
include('../../conn_db.php'); // Adjust the path as needed

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user ID is set
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));


        // Check for existing username
        $check_sql = "SELECT * FROM user_info WHERE username = :username AND user_id != :user_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $check_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $check_stmt->execute();

        // If username already exists
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['statuswrongpassword'] = "ឈ្មោះអ្នកប្រើប្រាស់នេះមានរួចហើយ​!"; // Error message in Khmer
            header("Location: view_profile.php?user_id=" . urlencode($user_id));
            exit;
        } else {
            // Handle image upload
            $image_url = null;
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                $image_url = uploadImage($_FILES['image_url']);
            }

            // Prepare SQL for updating user info
            if ($image_url) {
                $sql = "UPDATE user_info SET first_name = :first_name, last_name = :last_name, username = :username, email = :email, phone_number = :phone_number,  image_url = :image_url WHERE user_id = :user_id";
            } else {
                $sql = "UPDATE user_info SET first_name = :first_name, last_name = :last_name, username = :username, email = :email, phone_number = :phone_number WHERE user_id = :user_id";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            if ($image_url) {
                $stmt->bindValue(':image_url', $image_url, PDO::PARAM_STR);
            }

            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['status'] = "កែប្រែប្រវត្តិរូបដោយជោគជ័យ!"; // Success message in Khmer
                header("Location: view_profile.php?user_id=" . urlencode($user_id));
                exit;
            } else {
                echo "មានកំហុសក្នុងការកែប្រែប្រវត្តិរូប: " . implode(", ", $stmt->errorInfo()); // Error message for execution failure
            }

            // Close statement
            $stmt->closeCursor();
        }

        // Close check statement
        $check_stmt->closeCursor();
    } else {
        echo "ID អ្នកប្រើមិនបានកំណត់ទេ។"; // Error message for missing user ID
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

// Close database connection
$conn = null;
