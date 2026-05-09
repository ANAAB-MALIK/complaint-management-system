 <?php
session_start();
include '../config.php';

if(!isset($_SESSION['admin_id'])){
    header("location: login.php");
}

// Status Update
if(isset($_POST['update_status'])){
    $id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    
    mysqli_query($conn, "UPDATE complaints SET status='$status', 
    admin_remarks='$remarks' WHERE id='$id'");
    
    // Auto Email Simulation
    $complaint = mysqli_query($conn, "SELECT complaints.*, users.email, users.first_name 
                                      FROM complaints 
                                      JOIN users ON complaints.user_id = users.id 
                                      WHERE complaints.id='$id'");
    $comp = mysqli_fetch_assoc($complaint);
    
    $to = $comp['email'];
    $subject = "Complaint Status Updated - CMS";
    $message = "Dear ".$comp['first_name'].",\n\n";
    $message .= "Your complaint '".$comp['title']."' status has been updated to: ".$status."\n\n";
    $message .= "Admin Remarks: ".$remarks."\n\n";
    $message .= "Thank you,\nCMS Team";
    $headers = "From: admin@cms.com";
    
    mail($to, $subject, $message, $headers);
    
    $success = "Status updated successfully! Email sent to user.";
}

// Delete Complaint
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM complaints WHERE id='$id'");
    header("location: manage.php");
}

// Search & Filter
$search = isset($_GET['search']) ? $_GET['search'] : "";
$status_filter = isset($_GET['status']) ? $_GET['status'] : "";

$sql = "SELECT complaints.*, users.first_name, users.last_name 
        FROM complaints 
        JOIN users ON complaints.user_id = users.id";

if($search != "" && $status_filter != ""){
    $sql .= " WHERE complaints.title LIKE '%$search%' AND complaints.status='$status_filter'";
} elseif($search != ""){
    $sql .= " WHERE complaints.title LIKE '%$search%'";
} elseif($status_filter != ""){
    $sql .= " WHERE complaints.status='$status_filter'";
}

$sql .= " ORDER BY complaints.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css">
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
                        <a class="nav-link text-white" href="dashboard.php">📊 Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link active text-white" href="manage.php">📋 Manage Complaints</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white" href="logout.php">🚪 Logout</a>
                    </li>
                </ul>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Manage Complaints</h2>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <!-- SEARCH & FILTER -->
                <form method="GET">
                    <div class="row mb-3">
                        <div class="col-md-4">
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

                <!-- COMPLAINTS TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>User</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
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
                                    <td>
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" 
                                        data-bs-target="#updateModal<?php echo $row['id']; ?>">Update</button>
                                        <a href="manage.php?delete=<?php echo $row['id']; ?>" 
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this complaint?')">Delete</a>
                                    </td>
                                </tr>

                                <!-- UPDATE MODAL -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Complaint Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label>Complaint Title</label>
                                                        <input type="text" class="form-control" value="<?php echo $row['title']; ?>" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Update Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                                            <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                                                            <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Admin Remarks</label>
                                                        <textarea name="remarks" class="form-control" rows="3" 
                                                        placeholder="Add remarks..."><?php echo $row['admin_remarks']; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No complaints found!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>