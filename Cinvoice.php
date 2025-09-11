<?php
require 'vendor/autoload.php';
include 'config.php';

use Dompdf\Dompdf;

if (!isset($_GET['reservation_id'])) {
    die("Invalid reservation ID");
}

$res_id = intval($_GET['reservation_id']);

// Fetch reservation details
$sql = "SELECT r.*, p.payment_method, p.payment_date, p.status AS payment_status 
        FROM reservations r
        LEFT JOIN payments p ON r.reservation_id = p.reservation_id
        WHERE r.reservation_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $res_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if (!$data) {
    die("Reservation not found.");
}

// Generate invoice HTML
$html = "
    <h2>Hotel Invoice</h2>
    <p><b>Reservation ID:</b> {$data['reservation_id']}</p>
    <p><b>Guest:</b> {$data['customer_name']}</p>
    <p><b>Room Type:</b> {$data['room_type']}</p>
    <p><b>Check-in:</b> {$data['check_in_date']}</p>
    <p><b>Check-out:</b> {$data['check_out_date']}</p>
    <p><b>Amount:</b> Rs. ".number_format($data['amount'])."</p>
    <p><b>Payment Method:</b> ".($data['payment_method'] ?? 'Not Provided')."</p>
    <p><b>Billing Status:</b> ".ucfirst($data['payment_status'] ?? 'Pending')."</p>
    <hr>
    <p>Thank you for booking with us!</p>
";

// Init Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// Force download
$dompdf->stream("Invoice_{$data['reservation_id']}.pdf", ["Attachment" => true]);
