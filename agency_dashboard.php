<?php
session_start();
include 'config.php';

// Access control
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'travel_agency'){
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

// Fetch total bookings
$stmt = $conn->prepare("SELECT COUNT(*) AS total_bookings FROM bulk_reservations WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalBookings = $stmt->get_result()->fetch_assoc()['total_bookings'];

// Fetch upcoming check-ins (next 30 days)
$stmt = $conn->prepare("SELECT * FROM bulk_reservations WHERE user_id=? AND check_in_date >= CURDATE() ORDER BY check_in_date ASC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcomingCheckins = $stmt->get_result();

// Fetch total revenue
$stmt = $conn->prepare("SELECT SUM(total_amount) AS total_revenue FROM bulk_reservations WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalRevenue = $stmt->get_result()->fetch_assoc()['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Agency Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="position-sticky top-0 z-2">
  <nav class="navbar navbar-expand-lg bg-secondary">
    <div class="container-fluid">
      <img src="Hotel reservation\assets\image\luxury-hotel-crown-key-letter-h-monogram-logo-laurel-elegant-beautiful-round-vector-emblem-sign-royalty-restaurant-97215514.webp" alt="Hotel Logo" class="img-fluid" width="60" />
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto gap-3">
          <li class="nav-item"><a class="btn btn-primary" href="home.php">Home</a></li>
          <li class="nav-item"><a class="btn btn-primary" href="room.php">Room</a></li>
          <li class="nav-item"><a class="btn btn-primary" href="reservation.php">Reservation</a></li>
          <li class="nav-item"><a class="btn btn-success" href="agency_dashboard.php">Bulk Booking</a></li>
          <li class="nav-item"><a class="btn btn-primary" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<div class="container mt-4">
    <h2>Agency Dashboard</h2>
    
    <div class="row mb-4">
        <!-- Total Bookings -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings</h5>
                    <p class="card-text fs-3"><?= $totalBookings ?></p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue ($)</h5>
                    <p class="card-text fs-3"><?= $totalRevenue ?: 0 ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <a href="bulk_booking.php" class="btn btn-primary mb-2 w-100">Make Bulk Booking</a>
                    <a href="agency_billing.php" class="btn btn-success w-100">View Billing & Export</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Check-ins -->
    <div class="card">
        <div class="card-header bg-info text-white">Upcoming Check-ins (Next 30 Days)</div>
        <div class="card-body">
            <?php if($upcomingCheckins->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Group Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $upcomingCheckins->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['reservation_id'] ?></td>
                                <td><?= htmlspecialchars($row['group_name']) ?></td>
                                <td><?= $row['check_in_date'] ?></td>
                                <td><?= $row['check_out_date'] ?></td>
                                <td>
                                    <?= $row['num_standard_rooms'] + $row['num_deluxe_rooms'] + $row['num_residential_suites'] ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No upcoming check-ins in the next 30 days.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
