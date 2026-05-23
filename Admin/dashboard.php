 <?php
session_start();
include '../config.php';

if(!isset($_SESSION['admin_id'])){
    header("location: login.php");
}

// Stats
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE status='Pending'"));
$inprogress = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE status='In Progress'"));
$resolved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE status='Resolved'"));

// Category wise count
$hostel = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE category='Hostel'"));
$fee = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE category='Fee'"));
$faculty = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE category='Faculty'"));
$facilities = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE category='Facilities'"));
$other = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE category='Other'"));

// Recent complaints
$recent = mysqli_query($conn, "SELECT complaints.*, users.first_name, users.last_name 
                                FROM complaints 
                                JOIN users ON complaints.user_id = users.id 
                                ORDER BY complaints.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand">⚙️ CMS Admin Panel</a>
            <span class="text-white me-3">Welcome, <?php echo $_SESSION['admin_name']; ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar-admin min-vh-100 pt-4">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link active text-white" href="dashboard.php">📊 Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white" href="manage.php">📋 Manage Complaints</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white" href="logout.php">🚪 Logout</a>
                    </li>
                </ul>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Admin Dashboard</h2>

                <!-- STATS CARDS -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary p-3 text-center">
                            <h3><?php echo $total; ?></h3>
                            <p>Total Complaints</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning p-3 text-center">
                            <h3><?php echo $pending; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info p-3 text-center">
                            <h3><?php echo $inprogress; ?></h3>
                            <p>In Progress</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success p-3 text-center">
                            <h3><?php echo $resolved; ?></h3>
                            <p>Resolved</p>
                        </div>
                    </div>
                </div>

                <!-- CHARTS -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="text-center mb-3">Complaints by Status</h5>
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="text-center mb-3">Complaints by Category</h5>
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- RECENT COMPLAINTS -->
                <h4 class="mb-3">Recent Complaints</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>User</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td>
                                    <?php if($row['status'] == 'Pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif($row['status'] == 'In Progress'): ?>
                                        <span class="badge bg-info">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Resolved</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CHART JS -->
    <script>
    // Status Chart
    var ctx1 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved'],
            datasets: [{
                data: [<?php echo $pending; ?>, <?php echo $inprogress; ?>, <?php echo $resolved; ?>],
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754']
            }]
        }
    });

    // Category Chart
    var ctx2 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Hostel', 'Fee', 'Faculty', 'Facilities', 'Other'],
            datasets: [{
                label: 'Complaints',
                data: [<?php echo $hostel; ?>, <?php echo $fee; ?>, <?php echo $faculty; ?>, <?php echo $facilities; ?>, <?php echo $other; ?>],
                backgroundColor: ['#0d6efd', '#ffc107', '#dc3545', '#198754', '#6c757d']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>