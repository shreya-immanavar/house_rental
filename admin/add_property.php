<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

$success = false;

if($_POST){
    $t = $conn->real_escape_string($_POST['title']);
    $c = $conn->real_escape_string($_POST['category']);
    $l = $conn->real_escape_string($_POST['location']);
    $p = (int)$_POST['price'];
    $d = $conn->real_escape_string($_POST['desc']);
    $tags = isset($_POST['tags']) ? $conn->real_escape_string(implode(',', $_POST['tags'])) : '';

    $conn->query("INSERT INTO properties(title,category,location,price,description,tags) VALUES('$t','$c','$l','$p','$d','$tags')");
    $id = $conn->insert_id;

    if(!empty($_FILES['images']['name'][0])){
        foreach($_FILES['images']['name'] as $k => $img){
            $tmp = $_FILES['images']['tmp_name'][$k];
            $name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $img);
            
            // Create uploads dir if not exists
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if(move_uploaded_file($tmp, "../uploads/".$name)){
                $conn->query("INSERT INTO property_images(property_id,image) VALUES('$id','$name')");
            }
        }
    }
    $success = true;
}
?>

<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex align-items-center mb-4">
                <a href="dashboard.php" class="btn btn-outline-custom me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h2 class="fw-bolder mb-0">Add New Property</h2>
                    <p class="text-muted mb-0">List a new rental property</p>
                </div>
            </div>

            <div class="card-custom p-4 p-md-5">
                <?php if($success): ?>
                    <div class="alert alert-success shadow-sm rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i> Property added successfully!
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Property Title</label>
                        <input class="form-control form-control-custom" name="title" placeholder="e.g. Modern Villa in Suburbs" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select form-control-custom" name="category" required>
                                <option value="House">House</option>
                                <option value="Apartment">Apartment</option>
                                <option value="PG/Hostel">PG / Hostel</option>
                                <option value="Vehicle">Vehicle</option>
                                <option value="Commercial Space">Commercial Space</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                                <input class="form-control form-control-custom border-start-0 ps-0" name="location" placeholder="City, Area" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rent (₹/mo)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body border-end-0 text-muted">₹</span>
                                <input class="form-control form-control-custom border-start-0 ps-0" type="number" name="price" placeholder="15000" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tags <small class="text-muted fw-normal">(Optional)</small></label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="Luxury" id="tag1">
                                <label class="form-check-label" for="tag1">Luxury</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="Budget" id="tag2">
                                <label class="form-check-label" for="tag2">Budget</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="Family" id="tag3">
                                <label class="form-check-label" for="tag3">Family</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="Student Friendly" id="tag4">
                                <label class="form-check-label" for="tag4">Student Friendly</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="Pet Friendly" id="tag5">
                                <label class="form-check-label" for="tag5">Pet Friendly</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control form-control-custom" name="desc" rows="4" placeholder="Describe the property details, amenities, etc." required></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">Property Images</label>
                        <input class="form-control form-control-custom" type="file" name="images[]" id="imageInput" multiple accept="image/*" required>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> You can select multiple images by holding Ctrl/Cmd.</div>
                        <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <button class="btn btn-primary-custom w-100 py-2 fs-5"><i class="bi bi-plus-circle me-2"></i> Publish Listing</button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        if (this.files) {
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
<?php include '../footer.php'; ?>
