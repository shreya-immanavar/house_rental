<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
    header("Location: ../login.php");
    exit;
}
include '../config/config.php';
require '../libs/fpdf.php';

if(!isset($_GET['booking_id'])){
    header("Location: my_bookings.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$booking_id = intval($_GET['booking_id']);

// Fetch booking details securely
$sql = "SELECT b.*, p.title, p.location, p.price, p.category, u.name as user_name, u.email as user_email 
        FROM bookings b 
        JOIN properties p ON b.property_id = p.id 
        JOIN users u ON b.user_id = u.id 
        WHERE b.id = $booking_id AND b.user_id = $user_id";
$res = $conn->query($sql);

if($res->num_rows == 0){
    die("Booking not found or unauthorized.");
}
$b = $res->fetch_assoc();

if($b['status'] != 'approved'){
    die("You can only download a receipt for approved bookings.");
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Brand / Header
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(41, 128, 185); // Primary color
$pdf->Cell(0, 10, 'LuxeRent', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 8, 'Official Booking Receipt', 0, 1, 'C');
$pdf->Ln(10);

// Line
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(10, 35, 200, 35);
$pdf->Ln(5);

// Booking Meta
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(50, 8, 'Receipt No:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, '#LR-'.str_pad($b['id'], 5, '0', STR_PAD_LEFT), 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Date Generated:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, date('F d, Y'), 0, 1);
$pdf->Ln(5);

// Customer Info
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Customer Details', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Name:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $b['user_name'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Email:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $b['user_email'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Tenant Phone:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $b['tenant_phone'], 0, 1);
$pdf->Ln(5);

// Booking Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Booking Details', 0, 1);

// Table Header
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Property Title', 1, 0, 'L', true);
$pdf->Cell(40, 10, 'Category', 1, 0, 'L', true);
$pdf->Cell(50, 10, 'Duration', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Monthly Rent', 1, 1, 'R', true);

// Table Row
$pdf->SetFont('Arial', '', 11);
$duration = ($b['move_in_date'] ? date('M d', strtotime($b['move_in_date'])) : '') . ' - ' . ($b['end_date'] ? date('M d', strtotime($b['end_date'])) : '');

$pdf->Cell(60, 10, substr($b['title'], 0, 25).'...', 1);
$pdf->Cell(40, 10, $b['category'], 1);
$pdf->Cell(50, 10, $duration, 1, 0, 'C');
$pdf->Cell(40, 10, 'Rs. '.number_format($b['price']), 1, 1, 'R');

$pdf->Ln(15);

// Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 10, 'Thank you for choosing LuxeRent. This is a system generated receipt.', 0, 1, 'C');

$pdf->Output('D', 'LuxeRent_Receipt_'.$b['id'].'.pdf');
?>
