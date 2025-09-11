<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'travel_agency'){
    die("Access Denied");
}

if(isset($_GET['id'])){
    $reservation_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM bulk_reservations WHERE reservation_id=? AND user_id=?");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();

    header("Location: agency_billing.php?deleted=1");
    exit();
}
?>
