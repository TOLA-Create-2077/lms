<?php
// Start the session and include necessary files
include_once('../../include/session_admin.php');
include('../../include/sidebar.php');

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

// Fetch departments from the database for the dropdown
$sql = "SELECT department_id, department_name FROM departments"; // Replace with your actual table/column names
$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch all departments as an associative array
$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

// Close the connection after use
$conn->close();
?>


<div class="container">
    <div class="page-inner">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="javascript:history.back()" class="btn" style="background-color: #717272; color: white;">
                <i class="fa-solid fa-circle-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
        </div>

        <div class="mt-4" id="title">
            <div id="left"></div>

        </div>

        <div class="card mb-4">

            <div class="row">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                        <h3 class="fw-bold mb-3">&nbsp;បញ្ចូលអ្នកប្រើប្រាស់</h3>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <center>
                            <label for="image_url">
                                <img id="imagePreview" src="../../assets/img/image.png" width="200px" height="auto" style="border:1px solid black; cursor: pointer;" onclick="document.getElementById('image_url')">
                            </label>
                            <form action="proccess_add_user.php" method="post" enctype="multipart/form-data">
                                <!-- File input -->
                                <input type="file" id="image_url" name="image_url" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                <!-- Other form fields... -->
                        </center>
                    </div>
                </div>


                <script>
                    function previewImage(event) {
                        const imagePreview = document.getElementById('imagePreview'); // Select the image preview element
                        const file = event.target.files[0]; // Get the selected file
                        if (file) {
                            const reader = new FileReader(); // Create a FileReader instance
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result; // Update the src of the image preview
                            };
                            reader.readAsDataURL(file); // Read the file as a data URL
                        } else {
                            // If no file is selected, reset to the default image
                            imagePreview.src = '../../assets/img/image.png'; // Change this path as needed
                        }
                    }
                </script>

                <div class="col-md-9">

                    <table class="table table-bordered">
                        <tr>
                            <td>គោត្តនាម</td>
                            <td><input type="text" class="form-control" name="first_name" required></td>
                        </tr>
                        <tr>
                            <td>នាម</td>
                            <td><input type="text" class="form-control" name="last_name" required></td>
                        </tr>
                        <tr>
                            <td>ឈ្មោះអ្នកប្រើ</td>
                            <td><input type="text" class="form-control" name="username" required></td>
                        </tr>
                        <tr>
                            <td>ដេប៉ាតឺម៉ង់</td>
                            <td>
                                <select class="form-control" name="department_id" required>
                                    <option value="">ជ្រើសរើសដេប៉ាតឺម៉ង់</option>
                                    <?php foreach ($departments as $department) { ?>
                                        <option value="<?php echo $department['department_id']; ?>">
                                            <?php echo $department['department_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>អ៊ីមែល</td>
                            <td><input type="email" class="form-control" name="email" required></td>
                        </tr>
                        <tr>
                            <td>លេខទូរស័ព្ទ</td>
                            <td><input type="text" class="form-control" name="phone_number" required></td>
                        </tr>
                        <tr>
                            <td>ពាក្យសម្ងាត់</td>
                            <td><input type="password" class="form-control" name="password" required></td>
                        </tr>
                        <tr>
                            <td>បញ្ជាក់ពាក្យសម្ងាត់</td>
                            <td><input type="password" class="form-control" name="confirm_password" required></td>
                        </tr>

                        <tr>
                            <td>តួនាទី</td>
                            <td>
                                <select class="form-control" name="role" required>
                                    <option value="staff">ថ្នាក់ដឹកនាំ</option>
                                    <option value="user">គ្រូបង្រៀន</option>
                                    <option value="admin">អ្នកគ្រប់គ្រង់</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> រក្សាទុកការផ្លាស់ប្ដូរ</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateImage(input) {
            const img = document.querySelector('label img');
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result; // Update image source
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</div>