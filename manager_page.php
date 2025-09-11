<?php
session_start();
include 'config.php';

// Logged-in manager's name
$name = $_SESSION['username'] ?? 'Manager';

// Dates
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

/* -------------------------
   1. Yesterday's Revenue (billing table, only paid)
------------------------- */
$revenueQuery = "SELECT SUM(amount) AS revenue 
                 FROM billing 
                 WHERE DATE(created_at) = '$yesterday' 
                 AND status = 'paid'";
$revenueResult = mysqli_query($conn, $revenueQuery);
$yesterday_revenue = ($revenueResult && mysqli_num_rows($revenueResult) > 0) 
                     ? mysqli_fetch_assoc($revenueResult)['revenue'] 
                     : 0;

/* -------------------------
   2. Today's New Bookings (reservations table)
------------------------- */
$bookingsQuery = "SELECT COUNT(*) AS new_bookings 
                  FROM reservations 
                  WHERE DATE(created_at) = '$today'";
$bookingsResult = mysqli_query($conn, $bookingsQuery);
$new_bookings = ($bookingsResult && mysqli_num_rows($bookingsResult) > 0) 
                ? mysqli_fetch_assoc($bookingsResult)['new_bookings'] 
                : 0;

/* -------------------------
   3. Yesterday's No-Shows (billing table)
------------------------- */
$noShowQuery = "SELECT COUNT(*) AS no_shows 
                FROM billing 
                WHERE billing_type = 'no-show' 
                AND DATE(created_at) = '$yesterday'";
$noShowResult = mysqli_query($conn, $noShowQuery);
$no_shows = ($noShowResult && mysqli_num_rows($noShowResult) > 0) 
            ? mysqli_fetch_assoc($noShowResult)['no_shows'] 
            : 0;

/* -------------------------
   4. Current Occupancy
------------------------- */
// total rooms
$totalRoomsQuery = "SELECT COUNT(*) AS total FROM rooms";
$totalRoomsResult = mysqli_query($conn, $totalRoomsQuery);
$totalRooms = ($totalRoomsResult && mysqli_num_rows($totalRoomsResult) > 0) 
              ? mysqli_fetch_assoc($totalRoomsResult)['total'] 
              : 1; // prevent /0

// occupied rooms (booked/confirmed)
$occupiedRoomsQuery = "SELECT COUNT(DISTINCT room_id) AS occupied 
                       FROM reservations 
                       WHERE status IN ('booked','confirmed')";
$occupiedRoomsResult = mysqli_query($conn, $occupiedRoomsQuery);
$occupiedRooms = ($occupiedRoomsResult && mysqli_num_rows($occupiedRoomsResult) > 0) 
                 ? mysqli_fetch_assoc($occupiedRoomsResult)['occupied'] 
                 : 0;

$current_occupancy = round(($occupiedRooms / $totalRooms) * 100, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manager Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .card-stats {
      text-align: center;
      padding: 20px;
      color: white;
      font-size: 1.2rem;
      border-radius: 10px;
    }
    .card-blue { background-color: #007bff; }
    .card-green { background-color: #28a745; }
    .card-yellow { background-color: #ffc107; color: black; }
    .card-cyan { background-color: #17a2b8; }
  </style>
</head>
<body>

<!-- Navbar -->
<header class="position-sticky top-0 z-2">
  <nav class="navbar navbar-expand-lg bg-secondary">
    <div class="container-fluid">
      <img src="assets/image/luxury-hotel-crown-key-letter-h-monogram-logo.webp" alt="Logo" width="60" height="30" />
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-3">
          <li class="nav-item"><a class="btn btn-primary" href="manager_page.php">Dashboard</a></li>
          <li class="nav-item"><a class="btn btn-primary" href="report.php">Reports</a></li>
          <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- Main Content -->
<div class="container mt-4">
  <h2>Manager Dashboard</h2>

  <div class="box mb-3 p-3 bg-light border rounded">
    <h4>Welcome, <span><?= htmlspecialchars($name); ?></span></h4>
    <p>This is a <span class="text-primary fw-bold">manager</span> page</p>

    <!-- Stats Row -->
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card card-stats card-blue">
          <?= $current_occupancy ?>%<br>Current Occupancy
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-stats card-green">
          $<?= number_format($yesterday_revenue, 2) ?><br>Yesterday's Revenue
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-stats card-yellow">
          <?= $new_bookings ?><br>New Bookings Today
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-stats card-cyan">
          <?= $no_shows ?><br>No-Shows Yesterday
        </div>
      </div>
    </div>

    <!-- Forecast & Availability -->
    <div class="row g-3">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Occupancy Forecast (Next 14 Days)</div>
          <div class="card-body">Occupancy chart will be displayed here</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">Room Availability</div>
          <div class="card-body">
            <p>Standard Rooms: <span class="badge bg-success">Available</span></p>
            <p>Deluxe Rooms: <span class="badge bg-warning text-dark">Few Left</span></p>
            <p>Suites: <span class="badge bg-danger">Full</span></p>
            <a href="manage_rooms.php" class="btn btn-outline-primary btn-sm mt-2">Manage Rooms</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity & Alerts -->
    <div class="row g-3 mt-3">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Recent Activity</div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item">New Reservation #1099 (3 mins ago)</li>
            <li class="list-group-item">Customer Checkout #1095 (28 mins ago)</li>
            <li class="list-group-item">Payment Received #1093 (45 mins ago)</li>
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Alerts & Notifications</div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item text-danger">No-Show Alert: Reservation #1072</li>
            <li class="list-group-item text-success">System Report Generated</li>
            <li class="list-group-item text-info">Bulk Booking Confirmed</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-4">
      <div class="card">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
          <a href="export.php" class="btn btn-primary">Generate Reports</a>
          <button class="btn btn-secondary">Send Promotion Email</button>
          <button class="btn btn-outline-dark">System Settings</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 w-100">
  <p class="mb-0">Hotel Reservation System<br />Your premium hotel booking solution</p>
  <small>© 2025 HotelEase. All rights reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
