<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'travel_agency'){
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="agency_bulk_bookings.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Reservation ID', 'Group Name', 'Check-in', 'Check-out', 'Standard Rooms', 'Deluxe Rooms', 'Suites', 'Total Amount']);

$sql = "SELECT * FROM bulk_reservations WHERE user_id=? ORDER BY check_in_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();

while($row = $reservations->fetch_assoc()){
    fputcsv($output, [
        $row['reservation_id'],
        $row['group_name'],
        $row['check_in_date'],
        $row['check_out_date'],
        $row['num_standard_rooms'],
        $row['num_deluxe_rooms'],
        $row['num_residential_suites'],
        number_format($row['total_amount'],2)
    ]);
}

fclose($output);
exit();
?>
