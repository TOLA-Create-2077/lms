<?php include('../../include/session_users.php'); ?>
<?php include('../../include/sidebar.php'); ?>

<!-- SweetAlert CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<svg style="display: none;">
    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
    </symbol>
</svg>

<!-- Container -->
<div class="container">
    <div class="page-inner"><br>

        <!-- Alert Section -->
        <?php if (isset($_SESSION['alert'])): ?>
            <div id="alert" class="alert alert-<?php echo $_SESSION['alert']['type']; ?>" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="16" height="16" role="img" aria-label="Info">
                    <use xlink:href="#info-fill"></use>
                </svg>
                <?php echo $_SESSION['alert']['message']; ?>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- Leave Requests Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Title with Icon -->
                        <h4 class="card-title">
                            <i class="bi bi-calendar-plus"></i> ស្នើសុំច្បាប់សម្រាក
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Leave Request Form -->
                        <form id="leaveRequestForm" action="submit_leave_request.php" method="POST">
                            <div class="form-group">
                                <label for="fromDate">ថ្ងៃចាប់ផ្តើម<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fromDate" name="from_date" required>
                            </div>
                            <div class="form-group">
                                <label for="toDate">ថ្ងៃបញ្ចប់<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="toDate" name="to_date" required disabled> <!-- Initially disabled -->
                            </div>
                            <div class="form-group">
                                <label for="reason">មូលហេតុ<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="សូមបញ្ជាក់មូលហេតុ..." required></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> ដាក់ស្នើ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Fetch total leave days for the current year -->
        <script>
            let totalLeaveDaysThisYear = 0;

            // Function to fetch total leave days from the server
            function fetchTotalLeaveDays() {
                // Assuming you have a PHP script that returns total leave days for the current year
                fetch('fetch_total_leave_days.php')
                    .then(response => response.json())
                    .then(data => {
                        totalLeaveDaysThisYear = data.totalDays;
                    });
            }

            // Call the function on page load
            window.onload = function() {
                fetchTotalLeaveDays();

                // Auto-hide Alert Box
                var alertBox = document.getElementById('alert');
                if (alertBox) {
                    setTimeout(function() {
                        alertBox.style.display = 'none';
                    }, 8000);
                }
            };

            // Listen for form submission
            document.getElementById('leaveRequestForm').addEventListener('submit', function(event) {
                const totalDaysRequested = calculateDays();

                // Check if total leave days exceed the limit
                if (totalLeaveDaysThisYear + totalDaysRequested > 18) {
                    event.preventDefault(); // Prevent form submission

                    // Show SweetAlert confirmation dialog
                    Swal.fire({
                        title: 'ចំណាំ!',
                        text: 'អ្នកបានទាមទារច្បាប់លើស 18 ថ្ងៃក្នុងឆ្នាំនេះ។ តើអ្នកចង់បន្តឬ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'យល់ព្រម',
                        cancelButtonText: 'បោះបង់'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            document.getElementById('leaveRequestForm').submit();
                        }
                    });
                }
            });

            function calculateDays() {
                const fromDate = new Date(document.getElementById('fromDate').value);
                const toDate = new Date(document.getElementById('toDate').value);
                const totalDays = (toDate - fromDate) / (1000 * 60 * 60 * 24) + 1;
                return totalDays > 0 ? totalDays : 0;
            }
        </script>

        <!-- Date Selection Script -->
        <script>
            const fromDateInput = document.getElementById('fromDate');
            const toDateInput = document.getElementById('toDate');
            fromDateInput.min = new Date().toISOString().split('T')[0];

            fromDateInput.addEventListener('change', function() {
                toDateInput.disabled = false;
                const fromDate = new Date(this.value);
                const maxToDate = new Date(fromDate);
                maxToDate.setDate(fromDate.getDate() + 2);

                const maxToDateFormatted = maxToDate.toISOString().split('T')[0];
                toDateInput.setAttribute('min', this.value);
                toDateInput.setAttribute('max', maxToDateFormatted);

                if (new Date(toDateInput.value) > maxToDate) {
                    toDateInput.value = maxToDateFormatted;
                }
            });

            toDateInput.addEventListener('change', function() {
                const fromDate = new Date(fromDateInput.value);
                const toDate = new Date(this.value);

                const diff = toDate.getTime() - fromDate.getTime();
                const diffDays = diff / (1000 * 60 * 60 * 24);

                if (diffDays > 2) {
                    const maxToDate = new Date(fromDate);
                    maxToDate.setDate(fromDate.getDate() + 2);
                    const maxToDateFormatted = maxToDate.toISOString().split('T')[0];
                    this.value = maxToDateFormatted;
                }

                if (toDate < fromDate) {
                    this.value = fromDateInput.value;
                }
            });
        </script>

        <!-- Auto Calculate Days -->
        <script>
            document.getElementById('fromDate').addEventListener('change', calculateDays);
            document.getElementById('toDate').addEventListener('change', calculateDays);
        </script>
    </div>
</div>

<!-- Bootstrap Icons Link -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php include_once('../../include/footer.html'); ?>