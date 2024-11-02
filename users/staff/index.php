<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-9xP3EOMktIl5e+Y5X4cOUgW2FEN0SgV8tmfGMPklfP1YAVC4MOmsPZwE1WrOeo3H" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php
    include('../../include/session_staff.php');
    include('../../include/sidebar.php');
    include('../../conn_db.php');
    ?>

    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                </div>
            </div>

            <div class="row">
                <!-- Statistics Cards -->
                <?php
                // Fetching the counts for dashboard statistics
                $pendingRequestsQuery = "SELECT COUNT(*) AS pending_count FROM leave_requests WHERE status = 'កំពុងរងចាំ'";
                $pendingResult = $conn->query($pendingRequestsQuery)->fetch(PDO::FETCH_ASSOC);

                $monthlyRequestsQuery = "SELECT COUNT(*) AS monthly_count FROM leave_requests WHERE status = 'អនុញ្ញាត' AND MONTH(fromDate) = MONTH(CURDATE())AND YEAR(fromDate) = YEAR(CURDATE())";
                $monthlyResult = $conn->query($monthlyRequestsQuery)->fetch(PDO::FETCH_ASSOC);

                $yearlyRequestsQuery = "SELECT COUNT(*) AS yearly_count FROM leave_requests WHERE status = 'អនុញ្ញាត' AND YEAR(fromDate) = YEAR(CURDATE())";
                $yearlyResult = $conn->query($yearlyRequestsQuery)->fetch(PDO::FETCH_ASSOC);

                $staffMembersQuery = "SELECT COUNT(*) AS staff_count FROM user_info WHERE role = 'user'";
                $staffResult = $conn->query($staffMembersQuery)->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round small-box" style="overflow: hidden;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-hourglass-half"></i> <!-- Icon for pending requests -->
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">សំណើរច្បាប់ដែលកំពុ​ងរងចាំ</p>
                                        <h4 class="card-title"><?php echo $pendingResult['pending_count']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="leave_manage.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round small-box" style="overflow: hidden;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-calendar-alt"></i> <!-- Icon for monthly requests -->
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">សំណើច្បាប់ក្នុងខែនេះ</p>
                                        <h4 class="card-title"><?php echo $monthlyResult['monthly_count']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="monthly_requests.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1"> ព័ត៌មានបន្ថែម &nbsp <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round small-box" style="overflow: hidden;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-calendar"></i> <!-- Icon for yearly requests -->
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">សំណើច្បាប់ក្នុងឆ្នាំនេះ</p>
                                        <h4 class="card-title"><?php echo $yearlyResult['yearly_count']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="yearly_requests.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp<i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round small-box" style="overflow: hidden;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-users"></i> <!-- Icon for staff members -->
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">ចំនួនគ្រូបង្រៀន</p>
                                        <h4 class="card-title"><?php echo $staffResult['staff_count']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="user_list.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp<i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once('../../include/footer.html'); ?>
    <!-- End Footer -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>