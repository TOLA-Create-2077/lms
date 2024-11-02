<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/fontawesome.min.css">
    <link rel="stylesheet" href="path/to/style.css"> <!-- Your custom styles -->
    <script src="path/to/Chart.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Staff Dashboard</h3>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Requests This Month</p>
                                        <h4 class="card-title" id="requestsThisMonth">25</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-calendar-year"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Requests This Year</p>
                                        <h4 class="card-title" id="requestsThisYear">120</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add more statistics cards if needed -->
            </div>

            <!-- Leave Request Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Leave Request Form</h4>
                        </div>
                        <div class="card-body">
                            <form id="leaveRequestForm">
                                <div class="mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="totalDays" class="form-label">Total Days</label>
                                    <input type="number" class="form-control" id="totalDays" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Reason</label>
                                    <textarea class="form-control" id="reason" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Requests Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Leave Requests</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Employee</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="leaveRequestsTable">
                                    <!-- Rows will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Leave Requests Overview</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="leaveRequestsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/bootstrap.bundle.min.js"></script>
    <script>
        // Function to calculate total days
        document.getElementById('fromDate').addEventListener('change', updateTotalDays);
        document.getElementById('toDate').addEventListener('change', updateTotalDays);

        function updateTotalDays() {
            const fromDate = new Date(document.getElementById('fromDate').value);
            const toDate = new Date(document.getElementById('toDate').value);
            if (fromDate && toDate) {
                const diffTime = Math.abs(toDate - fromDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('totalDays').value = diffDays;
            }
        }

        // Example data for chart
        var ctx = document.getElementById('leaveRequestsChart').getContext('2d');
        var leaveRequestsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Leave Requests',
                    data: [10, 20, 15, 30, 25, 10, 15, 20, 25, 30, 15, 20], // Example data
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // Example for dynamically updating table
        const leaveRequests = [{
                id: '#001',
                employee: 'John Doe',
                fromDate: '2024-09-01',
                toDate: '2024-09-05',
                reason: 'Vacation',
                status: 'Pending'
            },
            // Add more leave requests here
        ];

        const tableBody = document.getElementById('leaveRequestsTable');
        leaveRequests.forEach(request => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${request.id}</td>
                <td>${request.employee}</td>
                <td>${request.fromDate}</td>
                <td>${request.toDate}</td>
                <td>${request.reason}</td>
                <td><span class="badge bg-warning">${request.status}</span></td>
            `;
            tableBody.appendChild(row);
        });
    </script>
</body>

</html>