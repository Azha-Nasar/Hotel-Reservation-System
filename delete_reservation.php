<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Unauthorized access.");
}

$guest_id = $_SESSION['user_id'];
$res_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ? AND user_id = ?");
$stmt->bind_param("ii", $res_id, $guest_id);
$stmt->execute();

header("Location: reservation_history.php?deleted=1");
exit();
?>
