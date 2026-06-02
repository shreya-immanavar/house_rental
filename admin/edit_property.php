<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: manage_properties.php");
    exit;
}

$id = intval($_GET['id']);

// Handle update
if(isset($_POST['update_property'])){
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $location = $conn->real_escape_string($_POST['location']);
    $price = $_POST['price'];
    $desc = $conn->real_escape_string($_POST['description']);

    $query = "UPDATE properties SET title='$title', category='$category', location='$location', price='$price', description='$desc' WHERE id=$id";
    if($conn->query($query)){
        $msg = "Property updated successfully!";
    } else {
        $error = "Failed to update property.";
    }
}

// Fetch current details
$res = $conn->query("SELECT * FROM properties WHERE id=$id");
if($res->num_rows == 0){
    echo "Property not found.";
    exit;
}
$property = $res->fetch_assoc();
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="manage_properties.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-2"></i>Back to Manage Properties</a>
            </div>

            <div class="card-custom p-4 p-md-5">
                <h2 class="fw-bolder mb-1">Edit Property</h2>
                <p class="text-muted mb-4">Update the details for #<?php echo $id; ?> - <?php echo htmlspecialchars($property['title']); ?></p>

                <?php if(isset($msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_property.php?id=<?php echo $id; ?>">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-bold">Property Title</label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <select class="form-select form-control-lg" id="category" name="category" required>
                                <?php
                                $cats = ['House', 'Apartment', 'PG/Hostel', 'Vehicle', 'Commercial Space'];
                                foreach($cats as $c){
                                    $sel = ($property['category'] == $c) ? 'selected' : '';
                                    echo "<option value=\"$c\" $sel>$c</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label fw-bold">Location</label>
                            <input type="text" class="form-control form-control-lg" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Rent (₹/mo)</label>
                            <input type="number" class="form-control form-control-lg" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control form-control-lg" id="description" name="description" rows="6" required><?php echo htmlspecialchars($property['description']); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_property" class="btn btn-primary-custom btn-lg w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
