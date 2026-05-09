 <?php
session_start();
include 'config.php';

if(isset($_POST['register'])){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($first_name) || empty($last_name) || empty($email) || empty($password)){
        $error = "All fields are required!";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format!";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters!";
    } elseif($password != $confirm_password){
        $error = "Passwords do not match!";
    } else {
        $password = md5($password);
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        
        if(mysqli_num_rows($check) > 0){
            $error = "Email already exists!";
        } else {
            $sql = "INSERT INTO users (first_name, last_name, email, phone, password) 
                    VALUES ('$first_name', '$last_name', '$email', '$phone', '$password')";
            
            if(mysqli_query($conn, $sql)){
                $success = "Registration successful! Please login.";
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CMS</title>
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
                    <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow p-4">
                        <h2 class="text-center mb-4">Create Account</h2>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter phone">
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">👁️</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm password" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">👁️</button>
                                </div>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                            <p class="text-center mt-3">Already have account? <a href="login.php">Login</a></p>
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
    <script>
    function togglePassword(fieldId) {
        var field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
        } else {
            field.type = "password";
        }
    }
    </script>
</body>
</html>