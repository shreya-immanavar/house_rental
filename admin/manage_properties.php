<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

// Handle delete logic
if(isset($_POST['delete_property'])){
    $property_id = intval($_POST['property_id']);
    // First, delete images from server
    $img_query = $conn->query("SELECT image FROM property_images WHERE property_id=$property_id");
    while($row = $img_query->fetch_assoc()){
        $img_path = "../uploads/".$row['image'];
        if(file_exists($img_path)){
            unlink($img_path);
        }
    }
    // Delete from DB (cascade handles related rows)
    $conn->query("DELETE FROM properties WHERE id=$property_id");
    $msg = "Property deleted successfully.";
}

$res = $conn->query("SELECT * FROM properties ORDER BY id DESC");
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1">Manage Properties</h2>
            <p class="text-muted">Edit or remove your property listings</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-outline-custom me-2">Back to Dashboard</a>
            <a href="add_property.php" class="btn btn-primary-custom"><i class="bi bi-plus-lg me-2"></i>Add New</a>
        </div>
    </div>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0">
                <thead class="table-secondary border-bottom">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Title & Category</th>
                        <th class="py-3 px-4">Location</th>
                        <th class="py-3 px-4">Rent / Mo</th>
                        <th class="py-3 px-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res->num_rows > 0): ?>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr class="border-bottom">
                            <td class="py-3 px-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td class="py-3 px-4">
                                <span class="fw-bold d-block text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($row['title']); ?></span>
                                <small class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><?php echo htmlspecialchars($row['category']); ?></small>
                            </td>
                            <td class="py-3 px-4"><i class="bi bi-geo-alt text-primary me-1"></i><?php echo htmlspecialchars($row['location']); ?></td>
                            <td class="py-3 px-4 fw-bold text-success">₹<?php echo number_format($row['price']); ?></td>
                            <td class="py-3 px-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="edit_property.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                    
                                    <form method="POST" action="manage_properties.php" onsubmit="return confirm('Are you sure you want to delete this property? All bookings and images will be permanently removed.');" style="display:inline-block;">
                                        <input type="hidden" name="property_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_property" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">No properties found. <a href="add_property.php">Add one now.</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
