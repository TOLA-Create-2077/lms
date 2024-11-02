<?php
session_start();
include('conn_db.php'); // Ensure this path is correct and the file exists

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to get user data based on username
    $sql = "SELECT * FROM user_info WHERE username = :username AND status = 1"; // Check for active status
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['image_url'] = $user['image_url']; // Store the image URL

            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: users/admin/index.php");
            } elseif ($_SESSION['role'] === 'staff') {
                header("Location: users/staff/index.php");
            } elseif ($_SESSION['role'] === 'user') {
                header("Location: users/users/index.php");
            }
            exit();
        } else {
            $error = "ពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ។"; // "Incorrect password."
        }
    } else {
        $error = "មិនមានអ្នកប្រើដែលសកម្មមកដល់ឈ្មោះនោះទេ។"; // "No active user found with that username."
    }
}
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ប្រព័ន្ធស្នើសុំការច្បាប់ចាកចេញនៃបុគ្គលិក - ចូល</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Battambang', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
        }
    </style>
</head>

<body class="flex items-center justify-center h-screen">
    <div class="w-full max-w-md">
        <form class="bg-white shadow-lg rounded-lg px-4 py-10" action="" method="post">
            <p class="font-extrabold mb-8 text-center" style="color: rgb(99 101 105); font-size: 19px;">
                ប្រព័ន្ធសុំច្បាប់សម្រាប់បុគ្គលិកក្នុងវិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ
            </p>

            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="username">ឈ្មោះអ្នកប្រើ</label>
                <input
                    class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    id="username"
                    type="text"
                    name="username"
                    placeholder="បញ្ចូលឈ្មោះអ្នកប្រើរបស់អ្នក"
                    required />
                <?php if (strpos($error, 'ឈ្មោះ') !== false): ?>
                    <span class="text-red-500 text-sm"><?php echo htmlspecialchars($error); ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="password">ពាក្យសម្ងាត់</label>
                <input
                    class="appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    id="password"
                    type="password"
                    name="password"
                    placeholder="បញ្ចូលពាក្យសម្ងាត់របស់អ្នក"
                    required />
                <?php if (strpos($error, 'ពាក្យសម្ងាត់') !== false): ?>
                    <span class="text-red-500 text-sm"><?php echo htmlspecialchars($error); ?></span>
                <?php endif; ?>
                <!-- Checkbox to toggle password visibility -->
                <div class="mt-2">
                    <input type="checkbox" id="show-password" onclick="togglePassword()" />
                    <label for="show-password" class="text-gray-600 text-sm">បង្ហាញពាក្យសម្ងាត់</label>
                </div>
            </div>

            <div class="flex items-center justify-center mb-4">
                <button
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                    type="submit">
                    ចូល
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var passwordType = passwordInput.getAttribute("type");
            if (passwordType === "password") {
                passwordInput.setAttribute("type", "text");
            } else {
                passwordInput.setAttribute("type", "password");
            }
        }
    </script>
</body>

</html>