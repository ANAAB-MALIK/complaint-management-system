<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("location: login.php");
}

if(isset($_POST['submit'])){
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $description = $_POST['description'];

    $sql = "INSERT INTO complaints (user_id, title, category, priority, description) 
            VALUES ('$user_id', '$title', '$category', '$priority', '$description')";
    
    if(mysqli_query($conn, $sql)){
        $success = "Complaint submitted successfully!";
    } else {
        $error = "Something went wrong!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint - CMS</title>
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
                    <li class="nav-item"><a class="nav-link active" href="submit.php">Submit Complaint</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-complaints.php">My Complaints</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card shadow p-4">
                        <h2 class="text-center mb-4">Submit New Complaint</h2>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Complaint Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter complaint title" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category</label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <option>Hostel</option>
                                        <option>Fee</option>
                                        <option>Faculty</option>
                                        <option>Facilities</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Priority</label>
                                    <select name="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        <option>Low</option>
                                        <option>Medium</option>
                                        <option>High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Describe your complaint in detail" required></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Submit Complaint</button>
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