<?php
session_start();
include 'config.php'; // DB connection

// Optional: Protect page - only for manager or admin
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reservation Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container my-5">
    <h2 class="mb-4">Reservation Reports</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Guest</th>
          <th>Room Type</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $row['reservation_id'] ?></td>
          <td><?= $row['guest_name'] ?></td>
          <td><?= $row['room_type'] ?></td>
          <td><?= $row['check_in_date'] ?></td>
          <td><?= $row['check_out_date'] ?></td>
          <td>$<?= $row['amount'] ?></td>
          <td><?= ucfirst($row['status']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No reservations found.</p>
    <?php endif; ?>

    <a href="manager_page.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
  </div>
</body>
</html>
