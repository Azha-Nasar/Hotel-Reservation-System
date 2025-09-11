<?php
session_start();
include 'config.php';

// Access control
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'travel_agency'){
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

// Fetch bulk reservations with payment info
$sql = "
SELECT b.*, p.payment_method, p.status AS payment_status
FROM bulk_reservations b
LEFT JOIN bulk_payments p ON b.reservation_id = p.reservation_id
WHERE b.user_id=?
ORDER BY b.check_in_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agency Billing & Past Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 1.5rem; }
        .table th { background-color: #d3d3d3 !important; font-size: 1.2rem; }
        .table td, .table th { padding: 0.8rem; font-size: 1rem; }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="agency_dashboard.php" class="btn btn-primary"><- Back</a>
    </div>

    <!-- Heading with Export Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Agency Past Bookings & Billing</h2>
        <a href="export_agency_billing.php" class="btn btn-success">Export All to CSV</a>
    </div>

    <!-- Alert for deletion -->
    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success text-center alert-dismissible fade show">
            Reservation deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mt-3">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0">All Bulk Reservations</h4>
        </div>
        <div class="card-body">
            <?php if($reservations->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Group Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Rooms</th>
                            <th>Total Amount ($)</th>
                            <th>Payment Method</th>
                            <th>Billing Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['reservation_id'] ?></td>
                            <td><?= htmlspecialchars($row['group_name']) ?></td>
                            <td><?= $row['check_in_date'] ?></td>
                            <td><?= $row['check_out_date'] ?></td>
                            <td><?= $row['num_standard_rooms'] + $row['num_deluxe_rooms'] + $row['num_residential_suites'] ?></td>
                            <td><?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= $row['payment_method'] ?? '<span class="text-danger">Not Provided</span>' ?></td>
                            <td><?= $row['payment_status'] ?? '<span class="text-warning">Pending</span>' ?></td>
                            <td>
                                <a href="invoice.php?reservation_id=<?= $row['reservation_id'] ?>" 
                                    class="btn btn-primary btn-sm" target="_blank">Invoice</a>

                                <a href="delete_bulk_reservation.php?id=<?= $row['reservation_id'] ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this reservation?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info">No reservations found for your agency.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
