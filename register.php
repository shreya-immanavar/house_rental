<?php
include 'config/config.php';

$success = false;

if($_POST){
    $n = $_POST['name'];
    $e = $_POST['email'];
    $p = $_POST['password'];

    // Prevent basic SQL injection by escaping
    $n = $conn->real_escape_string($n);
    $e = $conn->real_escape_string($e);
    $p = $conn->real_escape_string($p);

    // Check if email already exists
    $check = $conn->query("SELECT id FROM users WHERE email='$e'");
    
    if($check->num_rows > 0) {
        $msg = "Email is already registered.";
    } else {
        $conn->query("INSERT INTO users(name,email,password) VALUES('$n','$e','$p')");
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>if(localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-mode'); document.documentElement.setAttribute('data-bs-theme', 'dark'); }</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LuxeRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #e2e8f0, var(--light));
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-custom p-5">
                <div class="text-center mb-4">
                    <div class="bg-body-tertiary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-buildings-fill fs-1 text-primary"></i>
                    </div>
                    <h2 class="fw-bolder">Create Account</h2>
                    <p class="text-muted">Join LuxeRent today</p>
                </div>

                <?php if($success): ?>
                    <div class="alert alert-success shadow-sm rounded-3 text-center">
                        <i class="bi bi-check-circle-fill fs-4 d-block mb-2 text-success"></i>
                        Registration successful! <br> <a href="login.php" class="fw-bold mt-2 d-inline-block text-success">Click here to login</a>
                    </div>
                <?php else: ?>
                    <?php if(isset($msg)) echo "<div class='alert alert-danger shadow-sm rounded-3'><i class='bi bi-exclamation-circle-fill me-2'></i>$msg</div>"; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input class="form-control form-control-custom border-start-0 ps-0" name="name" type="text" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input class="form-control form-control-custom border-start-0 ps-0" name="email" type="email" placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input class="form-control form-control-custom border-start-0 ps-0" type="password" name="password" placeholder="Create a password" required minlength="4">
                             </div>
                        </div>
                        
                        <button class="btn btn-primary-custom w-100 py-2 fs-5 mb-4">Register</button>
                        
                        <p class="text-center text-muted m-0">Already have an account? <a href="login.php" class="text-primary text-decoration-none fw-semibold">Login here</a></p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 