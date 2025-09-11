<?php
require('fpdf/fpdf.php'); // Download FPDF from fpdf.org
include 'config.php';

if (!isset($_GET['reservation_id'])) {
    die("Reservation ID is missing.");
}

$reservation_id = intval($_GET['reservation_id']);

// Correct column name in query
$res = mysqli_query($conn, "SELECT * FROM bulk_reservations WHERE reservation_id=$reservation_id");
if (!$res || mysqli_num_rows($res) == 0) {
    die("Reservation not found.");
}

$row = mysqli_fetch_assoc($res);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Hotel header
$pdf->Cell(0,10,'Hotel Reservation Invoice',0,1,'C');
$pdf->Ln(10);

// Details
$pdf->SetFont('Arial','',12);
$pdf->Cell(100,10,'Reservation ID: '.$row['reservation_id'],0,1);
$pdf->Cell(100,10,'Group Name: '.$row['group_name'],0,1);
$pdf->Cell(100,10,'Persons: '.$row['num_persons'],0,1);
$pdf->Cell(100,10,'Check-in: '.$row['check_in_date'],0,1);
$pdf->Cell(100,10,'Check-out: '.$row['check_out_date'],0,1);
$pdf->Cell(100,10,'Status: '.$row['status'],0,1);
$pdf->Cell(100,10,'Total Amount: '.$row['total_amount'],0,1);

$pdf->Output('D','Invoice_'.$row['reservation_id'].'.pdf'); // D = download
?>
