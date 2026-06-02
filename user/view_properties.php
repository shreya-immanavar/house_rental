<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

$user_id = $_SESSION['user']['id'];

// Get user's wishlist
$wishlist = [];
$w_res = $conn->query("SELECT property_id FROM wishlist WHERE user_id=$user_id");
while($w_row = $w_res->fetch_assoc()){
    $wishlist[] = $w_row['property_id'];
}
?>
<?php include '../header.php'; ?>


<div class="container mt-5 min-vh-80">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1">Available Rentals</h2>
            <p class="text-muted">Find your perfect rental from our diverse categories</p>
        </div>
        <div>
            <a href="wishlist.php" class="btn btn-outline-danger me-2"><i class="bi bi-heart-fill me-1"></i> Wishlist</a>
            <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card-custom p-4 mb-5">
        <form method="GET" action="view_properties.php" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label fw-bold">Search</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Title or Location" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label fw-bold">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All</option>
                    <?php
                    $cats = ['House', 'Apartment', 'PG/Hostel', 'Vehicle', 'Commercial Space'];
                    $selected_cat = isset($_GET['category']) ? $_GET['category'] : '';
                    foreach($cats as $c){
                        $sel = ($selected_cat == $c) ? 'selected' : '';
                        echo "<option value=\"$c\" $sel>$c</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="tag" class="form-label fw-bold">Tag</label>
                <select class="form-select" id="tag" name="tag">
                    <option value="">All Tags</option>
                    <?php
                    $all_tags = ['Luxury', 'Budget', 'Family', 'Student Friendly', 'Pet Friendly'];
                    $selected_tag = isset($_GET['tag']) ? $_GET['tag'] : '';
                    foreach($all_tags as $t){
                        $sel = ($selected_tag == $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $sel>$t</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="max_price" class="form-label fw-bold">Max Price: <span id="priceVal" class="text-primary">₹<?php echo isset($_GET['max_price']) && $_GET['max_price'] ? number_format($_GET['max_price']) : '100,000+'; ?></span></label>
                <input type="range" class="form-range" id="max_price" name="max_price" min="1000" max="100000" step="1000" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '100000'; ?>" oninput="document.getElementById('priceVal').innerText = '₹' + parseInt(this.value).toLocaleString('en-IN') + (this.value == 100000 ? '+' : '')">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-search me-2"></i>Filter</button>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['booked']) && $_GET['booked'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Successfully booked the property! We will contact you soon.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-5">
        <?php
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? $_GET['max_price'] : '';
        $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
        $tag = isset($_GET['tag']) ? $conn->real_escape_string($_GET['tag']) : '';

        $sql = "SELECT * FROM properties WHERE 1=1";
        if($search != ''){
            $sql .= " AND (title LIKE '%$search%' OR location LIKE '%$search%')";
        }
        if($max_price != '' && $max_price < 100000){
            $sql .= " AND price <= $max_price";
        }
        if($category != ''){
            $sql .= " AND category = '$category'";
        }
        if($tag != ''){
            $sql .= " AND FIND_IN_SET('$tag', tags) > 0";
        }
        $sql .= " ORDER BY id DESC";
        $res = $conn->query($sql);

        if($res->num_rows > 0) {
            while($p = $res->fetch_assoc()){
                $img_q = $conn->query("SELECT image FROM property_images WHERE property_id=".$p['id']." LIMIT 1");
                $img = $img_q->fetch_assoc();
                $image_path = $img ? "../uploads/".$img['image'] : "https://via.placeholder.com/400x300?text=No+Image";
                $is_saved = in_array($p['id'], $wishlist);
        ?>

        <div class="col-md-6 col-lg-4">
            <div class="card-custom h-100 position-relative">
                <a href="toggle_wishlist.php?id=<?php echo $p['id']; ?>" class="position-absolute top-0 end-0 m-3 text-danger fs-3 z-3" title="<?php echo $is_saved ? 'Remove from Wishlist' : 'Save to Wishlist'; ?>" style="text-shadow: 0px 0px 10px rgba(255,255,255,0.8);">
                    <i class="bi <?php echo $is_saved ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                </a>
                <div class="badge-price">₹<?php echo number_format($p['price']); ?></div>
                <img src="<?php echo $image_path; ?>" class="card-img-top w-100" alt="<?php echo htmlspecialchars($p['title']); ?>">
                
                <div class="card-body p-4">
                    <div class="mb-2">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><?php echo htmlspecialchars($p['category']); ?></span>
                        <?php if(!empty($p['tags'])): ?>
                            <?php foreach(explode(',', $p['tags']) as $t): ?>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 ms-1"><?php echo htmlspecialchars($t); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold mb-2 text-truncate"><?php echo htmlspecialchars($p['title']); ?></h5>
                    <p class="text-muted mb-3"><i class="bi bi-geo-alt text-primary me-1"></i> <?php echo htmlspecialchars($p['location']); ?></p>
                    
                    <p class="text-muted small mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?php echo htmlspecialchars($p['description']); ?>
                    </p>
                    
                    <a href="property_details.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-custom w-100 py-2">
                        <i class="bi bi-eye me-2"></i>View Details
                    </a>
                </div>
            </div>
        </div>

        <?php 
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info text-center py-5'><i class='bi bi-info-circle fs-1 d-block mb-3'></i>No properties available matching your criteria.</div></div>";
        }
        ?>
    </div>
</div>

<?php include '../footer.php'; ?>

