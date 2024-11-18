<?php
include_once('../../include/session_admin.php');
include('../../include/sidebar.php');
include('../../conn_db1.php');

// Dashboard Statistics Query
$pendingRequestsQuery = "SELECT COUNT(*) AS pending_count FROM leave_requests WHERE status = 'កំពុងរងចាំ'";
$monthlyRequestsQuery = "SELECT COUNT(*) AS monthly_count FROM leave_requests WHERE status = 'អនុញ្ញាត' AND MONTH(fromDate) = MONTH(CURDATE())";
$yearlyRequestsQuery = "SELECT COUNT(*) AS yearly_count FROM leave_requests WHERE status = 'អនុញ្ញាត' AND YEAR(fromDate) = YEAR(CURDATE())";
$staffMembersQuery = "SELECT COUNT(*) AS staff_count FROM user_info";
$directorMembersQuery = "SELECT COUNT(*) AS director_count FROM user_info WHERE role = 'staff'";

// Execute the queries using MySQLi
$pendingResult = mysqli_fetch_assoc($conn->query($pendingRequestsQuery));
$monthlyResult = mysqli_fetch_assoc($conn->query($monthlyRequestsQuery));
$yearlyResult = mysqli_fetch_assoc($conn->query($yearlyRequestsQuery));
$staffResult = mysqli_fetch_assoc($conn->query($staffMembersQuery));
$directorResult = mysqli_fetch_assoc($conn->query($directorMembersQuery));
?>


<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">ផ្ទាំងព័ត៌មាន</h3>
      </div>
    </div>

    <div class="row">
      <!-- Monthly Leave Requests Card -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round small-box" style="overflow: hidden;">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-calendar-alt"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">សំណើច្បាប់ក្នុងខែនេះ</p>
                  <h4 class="card-title"><?= $monthlyResult['monthly_count'] ?? 0; ?></h4>
                </div>
              </div>
            </div>
          </div>
          <a href="monthly_requests.php" class="small-box-footer p-1" style="background-color: #aeababc7; color:white; display:flex; justify-content:center; align-items:center;">ព័ត៌មានបន្ថែម <i class="fas fa-arrow-circle-right ml-2"></i></a>
        </div>
      </div>

      <!-- Yearly Leave Requests Card -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round small-box" style="overflow: hidden;">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-calendar"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">សំណើច្បាប់ក្នុងឆ្នាំនេះ</p>
                  <h4 class="card-title"><?= $yearlyResult['yearly_count'] ?? 0; ?></h4>
                </div>
              </div>
            </div>
          </div>
          <a href="yearly_requests.php" class="small-box-footer p-1" style="background-color: #aeababc7; color:white; display:flex; justify-content:center; align-items:center;">ព័ត៌មានបន្ថែម <i class="fas fa-arrow-circle-right ml-2"></i></a>
        </div>
      </div>

      <!-- Staff Members Card -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round small-box" style="overflow: hidden;">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">ចំនួនអ្នកប្រើប្រាស់</p>
                  <h4 class="card-title"><?= $staffResult['staff_count'] ?? 0; ?></h4>
                </div>
              </div>
            </div>
          </div>
          <a href="manage_users.php" class="small-box-footer p-1" style="background-color: #aeababc7; color:white; display:flex; justify-content:center; align-items:center;">ព័ត៌មានបន្ថែម <i class="fas fa-arrow-circle-right ml-2"></i></a>
        </div>

      </div>
      <!-- Staff Members Card -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round small-box" style="overflow: hidden;">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">ចំនួនអ្នកអនុញ្ញាតច្បាប់</p>
                  <h4 class="card-title"><?php echo $directorResult['director_count']; ?></h4>
                </div>
              </div>
            </div>
          </div>
          <a href="derector_list.php" style="background-color:  #aeababc7; display:flex;justify-content: center;align-items:center; color:white" class="small-box-footer p-1">ព័ត៌មានបន្ថែម &nbsp<i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col">
        <div class="card card-stats card-round small-box" style="overflow: hidden;">
          <div class="card-body">
            <!-- Chart Section -->
            <canvas id="departmentChart" width="400" height="200"></canvas>
          </div>

        </div>
      </div>
    </div>

  </div>



  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    fetch('fetch_departments.php')
      .then(response => response.json())
      .then(data => {
        const departmentNames = data.map(item => item.department_name);
        const leaveCounts = data.map(item => item.leave_count);

        new Chart(document.getElementById('departmentChart').getContext('2d'), {
          type: 'bar',
          data: {
            labels: departmentNames,
            datasets: [{
              label: 'Leave Requests per Department',
              data: leaveCounts,
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Number of Leave Requests'
                }
              }
            }
          }
        });
      })
      .catch(error => console.error('Error fetching department data:', error));
  </script>
</div>
</div>

<!-- Footer -->
<?php include_once('../../include/footer.html'); ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>