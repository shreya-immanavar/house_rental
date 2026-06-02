<?php 
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';

// Get total properties
$prop_res = $conn->query("SELECT COUNT(id) as total FROM properties");
$total_properties = $prop_res->fetch_assoc()['total'];

// Get total bookings
$bookings_res = $conn->query("SELECT COUNT(id) as total FROM bookings");
$total_bookings = $bookings_res->fetch_assoc()['total'];

// Get total users
$users_res = $conn->query("SELECT COUNT(id) as total FROM users WHERE role='user'");
$total_users = $users_res->fetch_assoc()['total'];

// Get total unread messages
$msg_res = $conn->query("SELECT COUNT(id) as total FROM messages WHERE receiver_id=0 AND is_read=0");
$total_unread = $msg_res->fetch_assoc()['total'];

// Estimated Revenue (Dummy: SUM of rent of all bookings)
$rev_res = $conn->query("SELECT SUM(p.price) as revenue FROM bookings b JOIN properties p ON b.property_id = p.id");
$revenue = $rev_res->fetch_assoc()['revenue'] ?? 0;

// Most Booked Property
$most_booked_q = "SELECT p.title, COUNT(b.property_id) as count 
                  FROM bookings b 
                  JOIN properties p ON b.property_id = p.id 
                  GROUP BY b.property_id 
                  ORDER BY count DESC LIMIT 1";
$most_booked_res = $conn->query($most_booked_q);
$most_booked = $most_booked_res->fetch_assoc();
$most_booked_title = $most_booked ? $most_booked['title'] : 'N/A';
$most_booked_count = $most_booked ? $most_booked['count'] : 0;

// Chart Data: Properties by Category
$cat_q = $conn->query("SELECT category, COUNT(id) as count FROM properties GROUP BY category");
$cat_labels = [];
$cat_data = [];
while($row = $cat_q->fetch_assoc()) {
    $cat_labels[] = $row['category'];
    $cat_data[] = $row['count'];
}

// Chart Data: Bookings over time (Last 7 Days)
$book_q = $conn->query("SELECT DATE(booking_date) as date, COUNT(id) as count FROM bookings WHERE booking_date >= DATE(NOW()) - INTERVAL 7 DAY GROUP BY DATE(booking_date) ORDER BY date ASC");
$book_labels = [];
$book_data = [];
while($row = $book_q->fetch_assoc()) {
    $book_labels[] = date('M d', strtotime($row['date']));
    $book_data[] = $row['count'];
}
?>
<?php include '../header.php'; ?>
<div class="container mt-5 min-vh-80 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bolder mb-1">Admin Dashboard</h2>
            <p class="text-muted">Manage your properties and analyze system usage</p>
        </div>
        <a href="add_property.php" class="btn btn-primary-custom"><i class="bi bi-plus-lg me-2"></i>Add New Property</a>
    </div>

    <!-- Top Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card-custom p-4 text-center h-100 d-flex flex-column">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto text-primary" style="width: 60px; height: 60px;">
                    <i class="bi bi-buildings fs-3"></i>
                </div>
                <h3 class="fw-bolder"><?php echo $total_properties; ?></h3>
                <p class="text-muted mb-4">Total Properties</p>
                <div class="mt-auto">
                    <a href="manage_properties.php" class="btn btn-outline-primary btn-sm px-4 rounded-pill">Manage Properties</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom p-4 text-center h-100 d-flex flex-column">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto text-success" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
                <h3 class="fw-bolder"><?php echo $total_bookings; ?></h3>
                <p class="text-muted mb-4">Total Bookings</p>
                <div class="mt-auto">
                    <a href="manage_bookings.php" class="btn btn-outline-success btn-sm px-4 rounded-pill">Manage Bookings</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom p-4 text-center h-100 d-flex flex-column">
                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto text-info" style="width: 60px; height: 60px;">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <h3 class="fw-bolder"><?php echo $total_users; ?></h3>
                <p class="text-muted mb-0">Registered Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-custom p-4 text-center h-100 d-flex flex-column">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto text-warning" style="width: 60px; height: 60px;">
                    <i class="bi bi-envelope fs-3"></i>
                </div>
                <h3 class="fw-bolder"><?php echo $total_unread; ?></h3>
                <p class="text-muted mb-4">Unread Messages</p>
                <div class="mt-auto">
                    <a href="messages.php" class="btn btn-outline-warning btn-sm px-4 rounded-pill">View Messages</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card-custom p-4 d-flex align-items-center bg-gradient-success text-white" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                <div class="bg-white bg-opacity-25 rounded p-3 me-4">
                    <i class="bi bi-cash-stack fs-1"></i>
                </div>
                <div>
                    <p class="mb-1 fw-medium text-white-50">Estimated Revenue</p>
                    <h2 class="fw-bolder mb-0">₹<?php echo number_format($revenue); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom p-4 d-flex align-items-center bg-gradient-primary text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                <div class="bg-white bg-opacity-25 rounded p-3 me-4">
                    <i class="bi bi-star-fill fs-1 text-warning"></i>
                </div>
                <div class="w-100">
                    <p class="mb-1 fw-medium text-white-50">Most Booked Property</p>
                    <h4 class="fw-bold mb-1 text-truncate" style="max-width: 90%;"><?php echo htmlspecialchars($most_booked_title); ?></h4>
                    <p class="mb-0 text-white-50 small"><i class="bi bi-bookmark-check me-1"></i> <?php echo $most_booked_count; ?> Bookings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-4">Bookings Overview (Last 7 Days)</h5>
                <canvas id="bookingsChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-4">Properties by Category</h5>
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Theme-aware Chart Defaults
    Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#64748b';
    Chart.defaults.borderColor = 'rgba(100, 116, 139, 0.2)';

    // Bookings Line Chart
    const bookCtx = document.getElementById('bookingsChart').getContext('2d');
    const bookingsChart = new Chart(bookCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($book_labels); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($book_data); ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Categories Doughnut Chart
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($cat_data); ?>,
                backgroundColor: ['#4f46e5', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Re-render charts on theme change
    document.getElementById('darkModeToggle').addEventListener('click', () => {
        setTimeout(() => {
            const newColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#64748b';
            Chart.defaults.color = newColor;
            bookingsChart.update();
            categoryChart.update();
        }, 50);
    });
</script>
<?php include '../footer.php'; ?>
