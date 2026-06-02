<?php
session_start();

// If already logged in → redirect
if(isset($_SESSION['user'])){
    if($_SESSION['user']['role'] == 'admin'){
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: user/dashboard.php");
        exit;
    }
}

include 'config/config.php';
?>

<?php include 'header.php'; ?>

<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <h1 class="display-3 fw-bolder mb-4 text-white">Find Your Perfect <br><span class="text-gradient">Rental Property</span></h1>
        <p class="lead mb-5 text-white-50">Discover houses, apartments, PG/hostels, vehicles, and commercial spaces with just a few clicks.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="login.php" class="btn btn-primary-custom btn-lg px-5">Login</a>
            <a href="register.php" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-medium">Register</a>
        </div>
    </div>
</div>

</div>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bolder">Recently Added <span class="text-primary">Properties</span></h2>
        <a href="login.php" class="text-decoration-none fw-medium">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4 mb-5">
        <?php
        $recent_q = "SELECT * FROM properties ORDER BY id DESC LIMIT 3";
        $recent_res = $conn->query($recent_q);
        if($recent_res->num_rows > 0) {
            while($p = $recent_res->fetch_assoc()){
                $img_q = $conn->query("SELECT image FROM property_images WHERE property_id=".$p['id']." LIMIT 1");
                $img = $img_q->fetch_assoc();
                $image_path = $img ? "uploads/".$img['image'] : "https://via.placeholder.com/400x300?text=No+Image";
        ?>
        <div class="col-md-4">
            <div class="card-custom h-100 position-relative">
                <div class="badge-price">₹<?php echo number_format($p['price']); ?></div>
                <img src="<?php echo $image_path; ?>" class="card-img-top w-100" alt="<?php echo htmlspecialchars($p['title']); ?>">
                <div class="card-body p-4">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 mb-2"><?php echo htmlspecialchars($p['category']); ?></span>
                    <h5 class="fw-bold mb-2 text-truncate"><?php echo htmlspecialchars($p['title']); ?></h5>
                    <p class="text-muted small"><i class="bi bi-geo-alt text-primary me-1"></i> <?php echo htmlspecialchars($p['location']); ?></p>
                </div>
            </div>
        </div>
        <?php 
            }
        }
        ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bolder">Trending <span class="text-danger">Hotspots 🔥</span></h2>
    </div>
    <div class="row g-4 mb-5">
        <?php
        $trend_q = "SELECT p.*, COUNT(b.id) as b_count FROM properties p LEFT JOIN bookings b ON p.id = b.property_id GROUP BY p.id ORDER BY b_count DESC LIMIT 3";
        $trend_res = $conn->query($trend_q);
        if($trend_res->num_rows > 0) {
            while($p = $trend_res->fetch_assoc()){
                if($p['b_count'] == 0) continue; // Only show if it actually has bookings
                $img_q = $conn->query("SELECT image FROM property_images WHERE property_id=".$p['id']." LIMIT 1");
                $img = $img_q->fetch_assoc();
                $image_path = $img ? "uploads/".$img['image'] : "https://via.placeholder.com/400x300?text=No+Image";
        ?>
        <div class="col-md-4">
            <div class="card-custom h-100 position-relative">
                <div class="position-absolute top-0 start-0 m-3 z-3">
                    <span class="badge bg-danger shadow"><i class="bi bi-fire me-1"></i> Highly Booked</span>
                </div>
                <img src="<?php echo $image_path; ?>" class="card-img-top w-100" alt="<?php echo htmlspecialchars($p['title']); ?>">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2 text-truncate"><?php echo htmlspecialchars($p['title']); ?></h5>
                    <p class="text-muted small mb-0"><i class="bi bi-geo-alt text-primary me-1"></i> <?php echo htmlspecialchars($p['location']); ?></p>
                </div>
            </div>
        </div>
        <?php 
            }
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>