<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

// Handle delete logic
if (isset($_POST['delete_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $conn->query("DELETE FROM bookings WHERE id=$booking_id");
    $msg = "Booking deleted successfully.";
}

// Handle status update
if (isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE bookings SET status='$new_status' WHERE id=$booking_id");
    $msg = "Booking status updated to " . ucfirst($new_status) . ".";
}

// Fetch all bookings with user and property details
$sql = "SELECT b.id as booking_id, b.booking_date, b.tenant_phone, b.move_in_date, b.end_date, b.occupants, b.notes, b.status,
               u.name as user_name, u.email as user_email, 
               p.title as property_title, p.location as property_location, p.id as property_id, p.category
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN properties p ON b.property_id = p.id
        ORDER BY b.id DESC";
$res = $conn->query($sql);
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder mb-1">Manage Bookings</h2>
            <p class="text-muted">Review and manage all property bookings</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
    </div>

    <?php if (isset($msg)): ?>
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
                        <th class="py-3 px-4">Booking ID</th>
                        <th class="py-3 px-4">Property</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $booking_rows = [];
                    if ($res->num_rows > 0):
                        while ($row = $res->fetch_assoc()) {
                            $booking_rows[] = $row;
                        }
                        foreach ($booking_rows as $row):
                            ?>
                            <tr class="border-bottom">
                                <td class="py-3 px-4 fw-bold text-muted">#<?php echo $row['booking_id']; ?></td>
                                <td class="py-3 px-4">
                                    <span class="fw-bold d-block text-truncate"
                                        style="max-width: 200px;"><?php echo htmlspecialchars($row['property_title']); ?></span>
                                    <small class="text-muted"><i
                                            class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($row['property_location']); ?>
                                        (<?php echo htmlspecialchars($row['category']); ?>)</small>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="d-block fw-medium"><?php echo htmlspecialchars($row['user_name']); ?></span>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['user_email']); ?></small>
                                </td>
                                <td class="py-3 px-4">
                                    <?php
                                    if ($row['status'] == 'approved')
                                        echo '<span class="badge bg-success">Approved</span>';
                                    elseif ($row['status'] == 'rejected')
                                        echo '<span class="badge bg-danger">Rejected</span>';
                                    else
                                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                                    ?>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal"
                                        data-bs-target="#modal-<?php echo $row['booking_id']; ?>">
                                        <i class="bi bi-eye"></i> Details
                                    </button>

                                    <?php if ($row['status'] == 'pending'): ?>
                                        <form method="POST" action="manage_bookings.php" class="d-inline-block">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-success me-1"><i
                                                    class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="POST" action="manage_bookings.php" class="d-inline-block">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-danger"><i
                                                    class="bi bi-x-lg"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="manage_bookings.php" class="d-inline-block"
                                            onsubmit="return confirm('Are you sure you want to permanently delete this booking?');">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                            <button type="submit" name="delete_booking" class="btn btn-sm btn-outline-danger"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>



                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals for Details -->
<?php
if (!empty($booking_rows)) {
    foreach ($booking_rows as $row):
        ?>
        <div class="modal fade" id="modal-<?php echo $row['booking_id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Booking Details #<?php echo $row['booking_id']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Property</label>
                            <p class="mb-0 fw-medium"><?php echo htmlspecialchars($row['property_title']); ?></p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Customer Name</label>
                                <p class="mb-0"><?php echo htmlspecialchars($row['user_name']); ?></p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Tenant Phone</label>
                                <p class="mb-0"><?php echo htmlspecialchars($row['tenant_phone']); ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Duration</label>
                                <p class="mb-0">
                                    <?php echo $row['move_in_date'] ? date('M d', strtotime($row['move_in_date'])) : 'N/A'; ?>
                                    -
                                    <?php echo $row['end_date'] ? date('M d, Y', strtotime($row['end_date'])) : 'N/A'; ?>
                                </p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold text-uppercase">Occupants</label>
                                <p class="mb-0"><?php echo htmlspecialchars($row['occupants']); ?></p>
                            </div>
                        </div>
                        <div>
                            <label class="text-muted small fw-bold text-uppercase">Special Notes</label>
                            <p class="mb-0 bg-body-tertiary p-3 rounded text-muted">
                                <?php echo !empty($row['notes']) ? nl2br(htmlspecialchars($row['notes'])) : 'No notes provided.'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach;
}
?>
<?php include '../footer.php'; ?>