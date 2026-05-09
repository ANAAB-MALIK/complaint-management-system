<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("location: login.php");
}

$user_id = $_SESSION['user_id'];
$search = "";
$status_filter = "";

if(isset($_GET['search'])){
    $search = $_GET['search'];
}
if(isset($_GET['status'])){
    $status_filter = $_GET['status'];
}

$sql = "SELECT * FROM complaints WHERE user_id='$user_id'";

if($search != ""){
    $sql .= " AND title LIKE '%$search%'";
}
if($status_filter != ""){
    $sql .= " AND status='$status_filter'";
}

$sql .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">CMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="submit.php">Submit Complaint</a></li>
                    <li class="nav-item"><a class="nav-link active" href="my-complaints.php">My Complaints</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">My Complaints</h2>

            <!-- SEARCH & FILTER -->
            <form method="GET">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" 
                        placeholder="Search complaints..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Filter by Status</option>
                            <option value="Pending" <?php if($status_filter=="Pending") echo "selected"; ?>>Pending</option>
                            <option value="In Progress" <?php if($status_filter=="In Progress") echo "selected"; ?>>In Progress</option>
                            <option value="Resolved" <?php if($status_filter=="Resolved") echo "selected"; ?>>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['priority']; ?></td>
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
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No complaints found!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-3">
        <p>© 2026 Complaint Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>