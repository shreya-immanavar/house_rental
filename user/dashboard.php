<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';
$user_id = $_SESSION['user']['id'];

// Get total bookings for user
$res = $conn->query("SELECT COUNT(id) as total FROM bookings WHERE user_id='$user_id'");
$total_bookings = $res->fetch_assoc()['total'];
?>
<?php include '../header.php'; ?>


<div class="container mt-5 min-vh-80">
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h2 class="fw-bolder mb-1">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>
            <p class="text-muted">Find and manage your rental bookings from here.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card-custom p-5 text-center h-100 d-flex flex-column justify-content-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4 text-primary mx-auto" style="width: 80px; height: 80px;">
                    <i class="bi bi-search fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Looking for a rental?</h4>
                <p class="text-muted mb-4">Browse our premium collection of available properties.</p>
                <div>
                    <a href="view_properties.php" class="btn btn-primary-custom px-4"><i class="bi bi-buildings me-2"></i>Browse Properties</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom p-5 text-center h-100 d-flex flex-column justify-content-center">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4 text-success mx-auto" style="width: 80px; height: 80px;">
                    <i class="bi bi-calendar2-check fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Your Bookings</h4>
                <p class="text-muted mb-4">You have <strong><?php echo $total_bookings; ?></strong> active bookings right now.</p>
                <div>
                    <a href="my_bookings.php" class="btn btn-outline-custom px-4"><i class="bi bi-list-check me-2"></i>View Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>