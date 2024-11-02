<?php
// Include session management and sidebar
include_once('../../include/session_users.php');
include('../../include/sidebar.php');

// Database connection info
$host = 'localhost';
$db = 'lms';
$user = 'root';
$pass = '';

// Start connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user ID is set in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user data from the database
    $sql = "SELECT * FROM user_info WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $firstName = htmlspecialchars($user['first_name']); // Escape output
        $lastName = htmlspecialchars($user['last_name']);
        $username = htmlspecialchars($user['username']);
        $email = htmlspecialchars($user['email']);
        $phoneNum = htmlspecialchars($user['phone_number']);
        $userType = htmlspecialchars($user['role']);
        $photo = htmlspecialchars($user['image_url']);
        $department_id = $user['department_id']; // Current department
    } else {
        echo "User not found.";
        exit;
    }

    // Close statement
    $stmt->close();
} else {
    echo "No user ID provided.";
    exit;
}

// Fetch departments from the database
$sql_departments = "SELECT department_id, department_name FROM departments";
$result_departments = $conn->query($sql_departments);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Edit Profile</title>
</head>

<body>
    <div class="container">
        <div class="page-inner">
            <div class="mt-4" id="title">
                <div id="left">
                    <h1>កែប្រែប្រវត្តិរូប</h1>
                </div>
            </div>

            <div id="containerFunctionAdd">
                <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                    <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
            </div><br>

            <div class="card mb-4">
                <div class="row">
                    <div class="col-md-3"><br>
                        <div class="form-group">
                            <form action="processProfileEdit.php" method="post" enctype="multipart/form-data">
                                <center>
                                    <label for="image_url">
                                        <img id="imagePreview" src="<?php echo $photo ? $photo : '../../assets/img/default.jpg'; ?>" width="200px" height="200" style="border:1px solid black; cursor: pointer;">
                                    </label>
                                    <input type="file" id="image_url" name="image_url" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                </center>
                        </div>
                    </div>



                    <div class="col-md-9">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                        <table class="table table-bordered">
                            <tr>
                                <td>គោត្តនាម</td>
                                <td><input type="text" class="form-control" name="first_name" value="<?php echo $firstName; ?>" required></td>
                            </tr>
                            <tr>
                                <td>នាម</td>
                                <td><input type="text" class="form-control" name="last_name" value="<?php echo $lastName; ?>" required></td>
                            </tr>
                            <tr>
                                <td>ឈ្មោះអ្នកប្រើ</td>
                                <td><input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required></td>
                            </tr>

                            <tr>
                                <td>អ៊ីមែល</td>
                                <td><input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required></td>
                            </tr>
                            <tr>
                                <td>លេខទូរស័ព្ទ</td>
                                <td><input type="text" class="form-control" name="phone_number" value="<?php echo $phoneNum; ?>" required></td>
                            </tr>
                        </table>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> រក្សាទុកការផ្លាស់ប្ដូរ
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.0-beta3/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</body>

</html>