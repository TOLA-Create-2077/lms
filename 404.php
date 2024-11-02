<?php
session_start(); // Start the session
include('include/sidebar.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>404 Not Found - LMS KSIT</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-9xP3EOMktIl5e+Y5X4cOUgW2FEN0SgV8tmfGMPklfP1YAVC4MOmsPZwE1WrOeo3H" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <style>
        body {
            background-color: #f8f9fa;
        }

        .not-found-container {
            text-align: center;
            padding: 100px;
        }

        .not-found-container h1 {
            font-size: 100px;
            color: #dc3545;
        }

        .not-found-container p {
            font-size: 20px;
            color: #6c757d;
        }

        .btn-home {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="not-found-container">
            <h1>404</h1>
            <p>ទំព័រដែលអ្នកកំពុងស្វែងរកមិនមាន។</p>
            <p>សូមសាកល្បងម្តងទៀតឬចុចលើប៊ូតុងខាងក្រោមដើម្បីត្រលប់ទៅកាន់ផ្ទះ។</p>
            <a href="../index.php" class="btn btn-primary btn-home"><i class="fas fa-home"></i> ទំព័រដើម</a>
        </div>
    </div>
</body>

</html>
<?php include_once('include/footer.html'); ?>