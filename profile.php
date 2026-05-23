<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("location: login.php");
}

$user_id = $_SESSION['user_id'];

// Update Profile
if(isset($_POST['update_profile'])){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];

    mysqli_query($conn, "UPDATE users SET first_name='$first_name', 
    last_name='$last_name', phone='$phone' WHERE id='$user_id'");
    
    $_SESSION['user_name'] = $first_name;
    $success = "Profile updated successfully!";
}

// Change Password
if(isset($_POST['change_password'])){
    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' 
    AND password='$old_password'");

    if(mysqli_num_rows($check) == 0){
        $pass_error = "Old password is incorrect!";
    } elseif(strlen($new_password) < 6){
        $pass_error = "New password must be at least 6 characters!";
    } elseif($new_password != $confirm_password){
        $pass_error = "Passwords do not match!";
    } else {
        $new_password = md5($new_password);
        mysqli_query($conn, "UPDATE users SET password='$new_password' 
        WHERE id='$user_id'");
        $pass_success = "Password changed successfully!";
    }
}

// Get user data
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

// Get complaint stats
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE user_id='$user_id'"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE user_id='$user_id' AND status='Pending'"));
$resolved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM complaints WHERE user_id='$user_id' AND status='Resolved'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">CMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="submit.php">Submit Complaint</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-complaints.php">My Complaints</a></li>
                    <li class="nav-item"><a class="nav-link active" href="profile.php">👤 Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">My Profile</h2>

            <div class="row">

                <!-- PROFILE INFO -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow p-4 text-center">
                        <div class="mb-3" style="font-size:80px;">👤</div>
                        <h4><?php echo $user['first_name'].' '.$user['last_name']; ?></h4>
                        <p class="text-muted"><?php echo $user['email']; ?></p>
                        <hr>
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-primary"><?php echo $total; ?></h5>
                                <small>Total</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning"><?php echo $pending; ?></h5>
                                <small>Pending</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-success"><?php echo $resolved; ?></h5>
                                <small>Resolved</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UPDATE FORMS -->
                <div class="col-md-8">

                    <!-- UPDATE PROFILE -->
                    <div class="card shadow p-4 mb-4">
                        <h5 class="mb-3">Update Profile</h5>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" class="form-control" 
                                    value="<?php echo $user['first_name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" class="form-control" 
                                    value="<?php echo $user['last_name']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" class="form-control" 
                                value="<?php echo $user['email']; ?>" readonly>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" 
                                value="<?php echo $user['phone']; ?>">
                            </div>
                            <button type="submit" name="update_profile" 
                            class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>

                    <!-- CHANGE PASSWORD -->
                    <div class="card shadow p-4">
                        <h5 class="mb-3">Change Password</h5>

                        <?php if(isset($pass_error)): ?>
                            <div class="alert alert-danger"><?php echo $pass_error; ?></div>
                        <?php endif; ?>

                        <?php if(isset($pass_success)): ?>
                            <div class="alert alert-success"><?php echo $pass_success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Old Password</label>
                                <input type="password" name="old_password" 
                                class="form-control" placeholder="Enter old password" required>
                            </div>
                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" 
                                class="form-control" placeholder="Enter new password" required>
                            </div>
                            <div class="mb-3">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" 
                                class="form-control" placeholder="Confirm new password" required>
                            </div>
                            <button type="submit" name="change_password" 
                            class="btn btn-warning">Change Password</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-3">
        <p>© 2026 Complaint Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>