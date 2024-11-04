<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "lms"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user details from the database
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    // Include image_url in the SQL query
    $sql = "SELECT user_id, first_name, last_name, email, image_url FROM user_info WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    // Bind image_url as well
    $stmt->bind_result($user_id, $firstName, $lastName, $email, $image_url);
    $stmt->fetch();
    $stmt->close();
} else {
    // Default values if user ID is not set
    $user_id = 0; // Assign a default or redirect to login
    $firstName = 'First Name';
    $lastName = 'Last Name';
    $email = 'hello@example.com';
    $image_url = '../../assets/img/default_image.png'; // Default image path
}

// Close the connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>LMS KSIT</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-9xP3EOMktIl5e+Y5X4cOUgW2FEN0SgV8tmfGMPklfP1YAVC4MOmsPZwE1WrOeo3H" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- DataTables CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Fonts and icons -->
    <script src="../../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <style>
        .nav-link {
            color: #000;
            /* Default color for the nav link */
            transition: color 0.3s ease;
            /* Smooth transition for color change */
        }

        .nav-link[aria-expanded="false"] {
            color: #6c757d;
            /* Color when collapsed (you can change this to your desired color) */
        }

        .nav-link[aria-expanded="true"] {
            color: #007bff;
            /* Color when expanded (you can change this as well) */
        }

        /* Initial sidebar styling */
        #sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            left: -250px;
            /* Initially hidden */
            transition: left 0.3s ease;
        }

        #sidebar.active {
            left: 0;
            /* Show sidebar */
        }

        #toggle-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            margin-left: 68%;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        .alert-success1 {
            color: blue;
            /* Set the text color to blue for success alerts */
        }

        .alert-danger {
            color: red;
            /* Set the text color to red for danger alerts */
        }

        .sidebar-logo-text {
            font-size: 24px;
            /* Font size for the logo text */
            font-weight: bold;
            /* Make the text bold */
            color: #ffffff;
            /* White color for the text */
            margin: 0;
            /* Remove default margin */
            text-transform: uppercase;
            /* Make text uppercase for emphasis */
            letter-spacing: 1px;
            /* Add some spacing between letters */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            /* Subtle shadow for depth */
            transition: color 0.3s;
            /* Smooth color transition on hover */
        }

        .logo-header:hover .sidebar-logo-text {
            color: #007bff;
            /* Change color on hover */
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <br>
                <div class="logo-header" data-background-color="dark">
                    <a href="#" class="logo">
                        <img src="../../assets/img/Copy-of-Stamp-KSIT.png" alt="navbar brand" class="navbar-brand sidebar-logo-img" height="70" />
                        <p class="sidebar-logo-text">KSIT</p>
                    </a>

                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <br>
                        <li class="nav-item">
                            <a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                                <i class="fas fa-home"></i> <!-- Home icon for Dashboard -->
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') { ?>
                            <li class="nav-item">
                                <a href="leave_manage.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'leave_manage.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-file-alt"></i> <!-- File icon for "ច្បាប់ដែលបានស្នើ" -->
                                    <p>ច្បាប់ដែលបានស្នើ</p>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['staff', 'admin'])) { ?>
                            <li class="nav-item">
                                <a href="reports.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-chart-bar"></i> <!-- Chart icon for Reports -->
                                    <p>របាយការណ៍</p>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') { ?>
                            <li class="nav-item">
                                <a href="request_leave.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'request_leave.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-paper-plane"></i> <!-- Paper plane icon for "ស្នើសុំច្បាប់សម្រាក" -->
                                    <p>ស្នើសុំច្បាប់សម្រាក</p>
                                </a>
                            </li>
                        <?php } ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') { ?>
                            <li class="nav-item">
                                <a href="leave_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'leave_list.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-list"></i> <!-- List icon for "មើលសំណើច្បាប់ឈប់សម្រាក" -->
                                    <p>មើលសំណើច្បាប់ឈប់សម្រាក</p>
                                </a>
                            </li>
                        <?php } ?>

                        <!-- Staff role links -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') { ?>
                            <li class="nav-item">
                                <a href="user_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'user_list.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-users"></i> <!-- Users icon for "របាយការណ៍សរុប" -->
                                    <p>របាយការណ៍សរុប</p>
                                </a>
                            </li>
                        <?php } ?>

                        <!-- Admin role links -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                            <li class="nav-item">
                                <a href="manage_users.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>">
                                    <i class="fas fa-user-cog"></i> <!-- User settings icon for "បញ្ជីអ្នកប្រើប្រាស់" -->
                                    <p>បញ្ជីអ្នកប្រើប្រាស់</p>
                                </a>
                            </li>
                        <?php } ?>
                        <!-- Admin role links -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                            <li class="nav-item submenu">
                                <a data-bs-toggle="collapse" href="#tables" class="" aria-expanded="true">
                                    <i class="fas fa-plus-circle"></i> <!-- Icon for "បន្ថែម" -->
                                    <p>បន្ថែម</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse <?= (basename($_SERVER['PHP_SELF']) == 'view_department.php' || basename($_SERVER['PHP_SELF']) == 'view_telegram_data.php') ? "show" : "" ?>" id="tables">
                                    <ul class="nav nav-collapse  ">

                                        <a href="view_department.php" class="ml-4 <?= (basename($_SERVER['PHP_SELF']) == 'view_department.php') ? "active" : "" ?>">
                                            <i class="fas fa-building"></i> <!-- Icon for "ដេប៉ាតឺម៉ង់" -->
                                            <span class="sub-item">ដេប៉ាតឺម៉ង់</span>
                                        </a>


                                        <a href="view_telegram_data.php" class="ml-4 <?= (basename($_SERVER['PHP_SELF']) == 'view_telegram_data.php') ? "active" : "" ?>">
                                            <i class="fas fa-robot"></i> <!-- Icon for "Telegram_bot" -->
                                            <span class="sub-item">Telegram_bot</span>
                                        </a>


                                    </ul>
                                </div>
                            </li>
                        <?php } ?>


                    </ul>
                    <!-- <button id="toggle-button" class="btn btn-primary">></button> -->


                </div>
            </div>
        </div>
        <!-- Custom Styles for Sidebar Logo -->

        <!-- End Sidebar -->
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="../../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>

                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"></nav>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                                    <i class="fa fa-search"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-search animated fadeIn">
                                    <form class="navbar-left navbar-form nav-search">
                                        <div class="input-group">
                                            <input type="text" placeholder="Search ..." class="form-control" />
                                        </div>
                                    </form>
                                </ul>
                            </li>

                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <?php if (!empty($image_url)) { ?>
                                            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Profile Image" class="avatar-img rounded-circle" />
                                        <?php } else { ?>
                                            <img src="../../assets/img/default.jpg" alt="Default Profile Image" class="avatar-img rounded-circle" />
                                        <?php } ?>
                                    </div>
                                    <span class="profile-username">
                                        <span class="fw-bold">
                                            <?php
                                            echo htmlspecialchars($firstName) . ' ' . htmlspecialchars($lastName);
                                            ?>
                                        </span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <a class="dropdown-item" href="view_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>">
                                                <i class="fas fa-user-circle"></i> មើលព័ត៏មានរបស់អ្នក
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                                <i class="fas fa-sign-out-alt"></i> ចាកចេញ
                                            </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
            <!-- Bootstrap Modal for Logout Confirmation -->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!-- Added a confirmation icon -->
                            <h5 class="modal-title" id="logoutModalLabel">
                                <i class="fas fa-exclamation-circle text-warning"></i> បញ្ជាក់ការចាកចេញ
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Added a question icon -->
                            <i class="fas fa-question-circle text-info"></i> តើអ្នកពិតជាចង់ចាកចេញមែនទេ?
                        </div>
                        <div class="modal-footer">
                            <!-- Cancel button with an icon -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> បោះបង់
                            </button>
                            <!-- Logout button with an icon -->
                            <a href="../../logout.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt"></i> ចាកចេញ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // JavaScript to toggle sidebar visibility
                const toggleButton = document.getElementById('toggle-button');
                const sidebar = document.getElementById('sidebar');

                toggleButton.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
            </script>