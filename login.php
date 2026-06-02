<?php
session_start();
include 'config/config.php';

if($_POST){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Prevent basic SQL injection by escaping
    $email = $conn->real_escape_string($email);
    $pass = $conn->real_escape_string($pass);

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass'");

    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        $_SESSION['user'] = $user;

        if($user['role'] == 'admin'){
            header("Location: admin/dashboard.php");
            exit;
        } else {
            header("Location: user/dashboard.php");
            exit;
        }
    } else {
        $msg = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>if(localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-mode'); document.documentElement.setAttribute('data-bs-theme', 'dark'); }</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LuxeRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--light), #e2e8f0);
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
                    <h2 class="fw-bolder">Welcome Back</h2>
                    <p class="text-muted">Sign in to continue to LuxeRent</p>
                </div>

                <?php if(isset($msg)) echo "<div class='alert alert-danger shadow-sm rounded-3'><i class='bi bi-exclamation-circle-fill me-2'></i>$msg</div>"; ?>
                
                <form method="POST">
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
                            <input class="form-control form-control-custom border-start-0 ps-0" type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary-custom w-100 py-2 fs-5 mb-4">Login</button>
                    
                    <p class="text-center text-muted m-0">Don't have an account? <a href="register.php" class="text-primary text-decoration-none fw-semibold">Register here</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>