<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

$user_id = $_SESSION['user']['id'];
$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $new_password = $_POST['new_password'];

    // Check if email exists for other user
    $chk = $conn->query("SELECT id FROM users WHERE email='$email' AND id!=$user_id");
    if($chk->num_rows > 0){
        $error = "Email is already taken by another account.";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email'";
        if(!empty($new_password)){
            $pw = $conn->real_escape_string($new_password);
            $sql .= ", password='$pw'";
        }
        $sql .= " WHERE id=$user_id";

        if($conn->query($sql)){
            $success = "Profile updated successfully!";
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
        } else {
            $error = "Failed to update profile.";
        }
    }
}

$u_res = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user_data = $u_res->fetch_assoc();
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex align-items-center mb-4">
                <a href="<?php echo $_SESSION['user']['role'] == 'admin' ? '../admin/dashboard.php' : 'dashboard.php'; ?>" class="btn btn-outline-custom me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h2 class="fw-bolder mb-0">Edit Profile</h2>
                    <p class="text-muted mb-0">Update your account details</p>
                </div>
            </div>

            <div class="card-custom p-4 p-md-5">
                <?php if($success): ?>
                    <div class="alert alert-success shadow-sm rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if($error): ?>
                    <div class="alert alert-danger shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control form-control-custom" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control form-control-custom" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    <hr class="my-4 text-muted opacity-25">
                    <div class="mb-4">
                        <label class="form-label fw-bold">New Password <small class="text-muted fw-normal">(Leave blank to keep current)</small></label>
                        <input type="password" class="form-control form-control-custom" name="new_password" placeholder="Enter new password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom w-100 py-2 fs-5"><i class="bi bi-save me-2"></i> Save Changes</button>
                </form>
            </div>
            
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
