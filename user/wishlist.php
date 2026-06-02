<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';
$user_id = $_SESSION['user']['id'];

?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1"><i class="bi bi-heart-fill text-danger me-2"></i>My Wishlist</h2>
            <p class="text-muted">Properties you've saved for later</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
    </div>

    <div class="row g-4">
        <?php
        $sql = "SELECT p.*, w.id as wishlist_id FROM properties p 
                JOIN wishlist w ON p.id = w.property_id 
                WHERE w.user_id = $user_id 
                ORDER BY w.created_at DESC";
        $res = $conn->query($sql);

        if($res->num_rows > 0) {
            while($p = $res->fetch_assoc()){
                $img_q = $conn->query("SELECT image FROM property_images WHERE property_id=".$p['id']." LIMIT 1");
                $img = $img_q->fetch_assoc();
                $image_path = $img ? "../uploads/".$img['image'] : "https://via.placeholder.com/400x300?text=No+Image";
        ?>

        <div class="col-md-6 col-lg-4">
            <div class="card-custom h-100 position-relative">
                <a href="toggle_wishlist.php?id=<?php echo $p['id']; ?>" class="position-absolute top-0 end-0 m-3 text-danger fs-3 z-3" title="Remove from Wishlist" style="text-shadow: 0px 0px 10px rgba(255,255,255,0.8);">
                    <i class="bi bi-heart-fill"></i>
                </a>
                <div class="badge-price">₹<?php echo number_format($p['price']); ?></div>
                <img src="<?php echo $image_path; ?>" class="card-img-top w-100" alt="<?php echo htmlspecialchars($p['title']); ?>">
                
                <div class="card-body p-4">
                    <div class="mb-2">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><?php echo htmlspecialchars($p['category']); ?></span>
                    </div>
                    <h5 class="fw-bold mb-2 text-truncate"><?php echo htmlspecialchars($p['title']); ?></h5>
                    <p class="text-muted mb-3"><i class="bi bi-geo-alt text-primary me-1"></i> <?php echo htmlspecialchars($p['location']); ?></p>
                    
                    <a href="property_details.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-custom w-100 py-2">
                        <i class="bi bi-eye me-2"></i>View Details
                    </a>
                </div>
            </div>
        </div>

        <?php 
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center py-5'><i class='bi bi-heart fs-1 d-block mb-3'></i>Your wishlist is currently empty.</div></div>";
        }
        ?>
    </div>
</div>
<?php include '../footer.php'; ?>
