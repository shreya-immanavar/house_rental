<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: view_properties.php");
    exit;
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

$prop_query = $conn->query("SELECT * FROM properties WHERE id=$id");

if($prop_query->num_rows == 0){
    echo "Property not found.";
    exit;
}
$property = $prop_query->fetch_assoc();

// Fetch images
$img_query = $conn->query("SELECT * FROM property_images WHERE property_id=$id");
$images = [];
while($row = $img_query->fetch_assoc()){
    $images[] = $row['image'];
}

// Check wishlist
$w_check = $conn->query("SELECT id FROM wishlist WHERE user_id=$user_id AND property_id=$id");
$is_saved = $w_check->num_rows > 0;

?>
<?php include '../header.php'; ?>


<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="mb-4">
        <a href="view_properties.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-2"></i>Back to Properties</a>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <!-- Image Carousel -->
            <div class="card-custom overflow-hidden mb-4 p-0">
                <?php if(count($images) > 0): ?>
                    <div id="propCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach($images as $index => $img): ?>
                                <button type="button" data-bs-target="#propCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach($images as $index => $img): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($img); ?>" class="d-block w-100" alt="Property Image" style="height: 500px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if(count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <img src="https://via.placeholder.com/800x500?text=No+Image+Available" class="d-block w-100" alt="No Image" style="height: 500px; object-fit: cover;">
                <?php endif; ?>
            </div>

            <!-- Property Description -->
            <div class="card-custom p-4 p-md-5">
                <h3 class="fw-bold mb-4">About this property</h3>
                <p class="text-muted" style="line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($property['description']); ?></p>
            </div>
        </div>

        <!-- Sidebar / Booking Box -->
        <div class="col-lg-4">
            <div class="card-custom p-4 sticky-top" style="top: 100px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-6">
                        <?php echo htmlspecialchars($property['category']); ?>
                    </span>
                    <a href="toggle_wishlist.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-outline-danger border-0 fs-4 py-0" title="<?php echo $is_saved ? 'Remove from Wishlist' : 'Save to Wishlist'; ?>">
                        <i class="bi <?php echo $is_saved ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                    </a>
                </div>
                
                <h2 class="fw-bolder mb-1"><?php echo htmlspecialchars($property['title']); ?></h2>
                <p class="text-muted mb-4 fs-5"><i class="bi bi-geo-alt text-primary me-2"></i><?php echo htmlspecialchars($property['location']); ?></p>
                
                <hr class="text-muted opacity-25 my-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-muted">Rental Price</span>
                    <h3 class="fw-bold text-primary mb-0">₹<?php echo number_format($property['price']); ?></h3>
                </div>

                <div class="bg-body-tertiary rounded p-3 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-shield-check text-success fs-4 me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Verified Listing</h6>
                            <small class="text-muted">This property has been verified.</small>
                        </div>
                    </div>
                </div>

                <a href="book_property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary-custom btn-lg w-100 py-3 fw-bold shadow-sm mb-3">
                    <i class="bi bi-calendar-check me-2"></i>Book Now
                </a>

                <a href="contact.php?property_id=<?php echo $property['id']; ?>" class="btn btn-outline-secondary w-100 py-2 fw-medium">
                    <i class="bi bi-chat-dots me-2"></i>Inquire About This
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

