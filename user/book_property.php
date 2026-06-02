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

$uid = $_SESSION['user']['id'];
$pid = (int)$_GET['id'];

// Fetch property details
$prop_q = $conn->query("SELECT * FROM properties WHERE id=$pid");
if($prop_q->num_rows == 0){
    echo "Property not found.";
    exit;
}
$property = $prop_q->fetch_assoc();

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $phone = $conn->real_escape_string($_POST['phone']);
    $start_date = $conn->real_escape_string($_POST['move_in_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $occupants = (int)$_POST['occupants'];
    $notes = $conn->real_escape_string($_POST['notes']);

    if(strtotime($start_date) >= strtotime($end_date)){
        $error = "End Date must be after the Move-in Date.";
    } else {
        // Check for double booking conflicts
        $conflict_q = "SELECT id FROM bookings WHERE property_id='$pid' 
                       AND move_in_date <= '$end_date' 
                       AND end_date >= '$start_date'
                       AND status != 'rejected'";
        $conflict_res = $conn->query($conflict_q);
        
        if($conflict_res->num_rows > 0) {
            $error = "This property is already booked during these dates. Please select a different range.";
        } else {
            $sql = "INSERT INTO bookings(user_id, property_id, tenant_phone, move_in_date, end_date, occupants, notes, status) 
                    VALUES('$uid', '$pid', '$phone', '$start_date', '$end_date', '$occupants', '$notes', 'pending')";
            if($conn->query($sql)){
                header("Location: view_properties.php?booked=1");
                exit;
            } else {
                $error = "Failed to process booking.";
            }
        }
    }
}
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="property_details.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-2"></i>Back to Property Details</a>
            </div>

            <div class="card-custom p-4 p-md-5">
                <h2 class="fw-bolder mb-1">Book Your Rental</h2>
                <p class="text-muted mb-4">Please provide your tenant details for <strong><?php echo htmlspecialchars($property['title']); ?></strong>.</p>

                <?php if($error): ?>
                    <div class="alert alert-danger shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                            <input class="form-control form-control-custom border-start-0 ps-0" type="text" name="phone" placeholder="+1 234 567 8900" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">From Date (Move-in)</label>
                            <input class="form-control form-control-custom" type="date" name="move_in_date" id="move_in_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <label class="form-label fw-bold">To Date (Move-out)</label>
                            <input class="form-control form-control-custom" type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Number of Occupants</label>
                        <select class="form-select form-control-custom" name="occupants" required>
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5+ People</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">Special Requests or Notes (Optional)</label>
                        <textarea class="form-control form-control-custom" name="notes" rows="4" placeholder="Any specific requirements..."></textarea>
                    </div>

                    <div class="card bg-body-tertiary border-0 mb-4 p-4 rounded-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 text-muted">Estimated Total:</span>
                            <span class="fs-3 fw-bold text-primary" id="total_price_display">₹0</span>
                        </div>
                        <small class="text-muted text-end d-block mt-1">Based on ₹<?php echo number_format($property['price']); ?>/month</small>
                    </div>

                    <button type="submit" class="btn btn-primary-custom btn-lg w-100 py-3 fw-bold shadow-sm">
                        <i class="bi bi-calendar-check me-2"></i> Request to Book
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const pricePerMonth = <?php echo $property['price']; ?>;
    const moveInInput = document.getElementById('move_in_date');
    const endInput = document.getElementById('end_date');
    const totalDisplay = document.getElementById('total_price_display');

    function calculatePrice() {
        if(moveInInput.value && endInput.value) {
            const start = new Date(moveInInput.value);
            const end = new Date(endInput.value);
            
            if(end > start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                // Assuming 30 days in a month for calculation
                const months = diffDays / 30;
                const total = Math.round(months * pricePerMonth);
                
                totalDisplay.innerText = '₹' + total.toLocaleString('en-IN');
            } else {
                totalDisplay.innerText = '₹0';
            }
        }
    }

    moveInInput.addEventListener('change', calculatePrice);
    endInput.addEventListener('change', calculatePrice);
</script>
<?php include '../footer.php'; ?>
