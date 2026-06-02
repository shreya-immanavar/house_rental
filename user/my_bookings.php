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
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bolder mb-1">My Bookings</h2>
            <p class="text-muted">Properties you have expressed interest in</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-custom">Back to Dashboard</a>
    </div>

    <ul class="nav nav-pills mb-4" id="bookingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="active-tab" data-bs-toggle="pill" data-bs-target="#active" type="button" role="tab">Active Bookings</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="past-tab" data-bs-toggle="pill" data-bs-target="#past" type="button" role="tab">Past Bookings</button>
        </li>
    </ul>

    <?php
    $q = "SELECT b.id as booking_id, b.booking_date, b.tenant_phone, b.move_in_date, b.end_date, b.occupants, b.notes, b.status, 
                 p.title, p.location, p.price, p.category 
          FROM bookings b 
          JOIN properties p ON b.property_id = p.id 
          WHERE b.user_id='$user_id' 
          ORDER BY b.booking_date DESC";
    $res = $conn->query($q);
    $active_bookings = [];
    $past_bookings = [];
    $today = date('Y-m-d');

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            if($row['end_date'] >= $today && $row['status'] != 'rejected'){
                $active_bookings[] = $row;
            } else {
                $past_bookings[] = $row;
            }
        }
    }

    function renderBookingsTable($bookings) {
        if(count($bookings) == 0) {
            echo '<tr><td colspan="5" class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i><h5 class="text-muted">No bookings found.</h5></td></tr>';
            return;
        }
        foreach($bookings as $row) {
    ?>
        <tr>
            <td class="ps-4 py-3">
                <span class="fw-bold d-block"><?php echo htmlspecialchars($row['title']); ?></span>
                            <small class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><?php echo htmlspecialchars($row['category']); ?></small>
                        </td>
                        <td class="py-3 text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($row['location']); ?></td>
                        <td class="py-3 fw-medium">₹<?php echo number_format($row['price']); ?></td>
                        <td class="py-3">
                            <?php 
                                if($row['status'] == 'approved') echo '<span class="badge bg-success">Approved</span>';
                                elseif($row['status'] == 'rejected') echo '<span class="badge bg-danger">Rejected</span>';
                                else echo '<span class="badge bg-warning text-dark">Pending</span>';
                            ?>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <?php if($row['status'] == 'approved'): ?>
                            <a href="download_receipt.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-outline-secondary me-2" title="Download PDF Receipt">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $row['booking_id']; ?>">
                                <i class="bi bi-eye"></i> Details
                            </button>
                        </td>


        <?php 
            }
        } 
        ?>

    <div class="tab-content" id="bookingsTabContent">
        <div class="tab-pane fade show active" id="active" role="tabpanel" tabindex="0">
            <div class="card-custom overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-body-tertiary text-muted">
                            <tr>
                                <th class="ps-4 py-3">Property</th>
                                <th class="py-3">Location</th>
                                <th class="py-3">Monthly Rent</th>
                                <th class="py-3">Status</th>
                                <th class="pe-4 py-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php renderBookingsTable($active_bookings); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="past" role="tabpanel" tabindex="0">
            <div class="card-custom overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-body-tertiary text-muted">
                            <tr>
                                <th class="ps-4 py-3">Property</th>
                                <th class="py-3">Location</th>
                                <th class="py-3">Monthly Rent</th>
                                <th class="py-3">Status</th>
                                <th class="pe-4 py-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php renderBookingsTable($past_bookings); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Modals for Details -->
<?php
$all_bookings = array_merge($active_bookings, $past_bookings);
foreach($all_bookings as $row) {
?>
<div class="modal fade" id="modal-<?php echo $row['booking_id']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold">My Booking Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4">
        <div class="mb-3">
            <label class="text-muted small fw-bold text-uppercase">Property</label>
            <p class="mb-0 fw-medium"><?php echo htmlspecialchars($row['title']); ?></p>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label class="text-muted small fw-bold text-uppercase">Tenant Phone</label>
                <p class="mb-0"><?php echo htmlspecialchars($row['tenant_phone']); ?></p>
            </div>
            <div class="col-6">
                <label class="text-muted small fw-bold text-uppercase">Occupants</label>
                <p class="mb-0"><?php echo htmlspecialchars($row['occupants']); ?></p>
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
                <label class="text-muted small fw-bold text-uppercase">Booking Status</label>
                <p class="mb-0 fw-bold <?php echo $row['status'] == 'approved' ? 'text-success' : ($row['status'] == 'rejected' ? 'text-danger' : 'text-warning'); ?>">
                    <?php echo ucfirst($row['status']); ?>
                </p>
            </div>
        </div>
        <div>
            <label class="text-muted small fw-bold text-uppercase">Special Notes</label>
            <p class="mb-0 bg-body-tertiary p-3 rounded text-muted"><?php echo !empty($row['notes']) ? nl2br(htmlspecialchars($row['notes'])) : 'No notes provided.'; ?></p>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php include '../footer.php'; ?>
