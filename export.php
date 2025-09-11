<?php
require(__DIR__ . '/fpdf/fpdf.php');
include 'config.php';

// Protect page - only logged in users
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch reservations
$query = "SELECT r.reservation_id, g.name AS guest_name, r.room_type, r.check_in_date, r.check_out_date, r.amount, r.status 
          FROM reservations r 
          JOIN guest g ON r.user_id = g.user_id 
          ORDER BY r.check_in_date DESC";
$result = mysqli_query($conn, $query);

// Initialize PDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode for wide table
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Title
$pdf->Cell(0,10,'Reservation Report',0,1,'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial','B',11);
$pdf->Cell(20,10,'ID',1,0,'C');
$pdf->Cell(40,10,'Guest',1,0,'C');
$pdf->Cell(35,10,'Room Type',1,0,'C');
$pdf->Cell(35,10,'Check-in',1,0,'C');
$pdf->Cell(35,10,'Check-out',1,0,'C');
$pdf->Cell(35,10,'Amount ($)',1,0,'C');
$pdf->Cell(40,10,'Status',1,1,'C');

// Table Data
$pdf->SetFont('Arial','',10);
while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(20,10,$row['reservation_id'],1,0,'C');
    $pdf->Cell(40,10,$row['guest_name'],1,0,'C');
    $pdf->Cell(35,10,$row['room_type'],1,0,'C');
    $pdf->Cell(35,10,$row['check_in_date'],1,0,'C');
    $pdf->Cell(35,10,$row['check_out_date'],1,0,'C');
    $pdf->Cell(35,10,number_format($row['amount'],2),1,0,'C');
    $pdf->Cell(40,10,ucfirst($row['status']),1,1,'C');
}

// Output PDF
$pdf->Output('D','reservation_report.pdf'); // Force download