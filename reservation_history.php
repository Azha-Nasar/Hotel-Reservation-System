<?php
session_start();
include 'config.php';

// Fetch user ID from session
$guest_id = $_SESSION['user_id'] ?? null;

if (!$guest_id) {
    die("User not logged in.");
}

// Handle reservation update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_reservation'])) {
    $res_id = $_POST['reservation_id'];
    $room_type = $_POST['room_type'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];
    $customer_name = $_POST['customer_name'];
    $amount = $_POST['amount'];

    $update = $conn->prepare("UPDATE reservations SET room_type=?, check_in_date=?, check_out_date=?, customer_name=?, amount=? WHERE reservation_id=? AND user_id=?");
    $update->bind_param("ssssiii", $room_type, $check_in, $check_out, $customer_name, $amount, $res_id, $guest_id);
    $update->execute();

    header("Location: reservation_history.php?updated=1");
    exit();
}

// Fetch reservations with payment info
$sql = "
SELECT 
    r.*, 
    p.payment_method, 
    p.payment_date, 
    p.status AS payment_status
FROM reservations r
LEFT JOIN payments p ON r.reservation_id = p.reservation_id
WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $guest_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reservation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            line-height: 1.5;
            padding: 1.5rem;
        }

        .table th {
            background-color: #d3d3d3 !important;
            font-size: 1.5rem;
        }

        .table tbody tr {
            background-color: #f0f0f0;
        }

        .table td,
        .table th {
            padding: 0.8rem;
            font-size: 1.09rem;
        }

        .btn-custom-edit {
            background-color: #28a745;
            color: white;
            font-size: 1rem;
            padding: 0.6rem 1.1rem;
        }

        .btn-custom-edit:hover {
            background-color: #218838;
        }

        .btn-custom-delete {
            font-size: 1rem;
            padding: 0.6rem 1.1rem;
        }
    </style>
</head>


<body>

    <div class="container mt-5">

        <!-- Back Button -->
        <a href="reservation.php" class="btn btn-primary mb-3"> Back </a>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>Success!</strong> Reservation updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>Deleted!</strong> Reservation deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!--Reservation Table -->
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">My Reservations</h4>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reservation ID</th>
                                    <th>Room Type</th>
                                    <th>Arrival</th>
                                    <th>Departure</th>
                                    <th>Guests</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Billing Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['reservation_id'] ?></td>
                                        <td><?= htmlspecialchars($row['room_type']) ?></td>
                                        <td><?= $row['check_in_date'] ?></td>
                                        <td><?= $row['check_out_date'] ?></td>
                                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                        <td><?= number_format($row['amount']) ?></td>
                                        <td><?= $row['payment_method'] ?? '<span class="text-danger">Not Provided</span>' ?></td>
                                        <td>
                                            <?= ($row['status'] === 'cancelled')
                                                ? '<span class="text-danger">Cancelled</span>'
                                                : ucfirst($row['status']) ?>
                                        </td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button class="btn btn-custom-edit btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                onclick='editReservation(<?= json_encode($row) ?>)'>
                                                Edit
                                            </button>

                                            <!-- Delete Button -->
                                            <a href="delete_reservation.php?id=<?= $row['reservation_id'] ?>"
                                                class="btn btn-danger btn-sm btn-custom-delete"
                                                onclick="return confirm('Are you sure you want to delete this reservation?');">
                                                Delete
                                            </a>

                                            <!-- Download Invoice Button -->
                                            <a href="Cinvoice.php?reservation_id=<?= $row['reservation_id'] ?>"
                                                class="btn btn-outline-primary btn-l"
                                                target="_blank">
                                                Download Invoice
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">You have no reservations yet....</div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="editModalLabel">Edit Reservation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row g-3 px-4 py-3">
                        <input type="hidden" name="reservation_id" id="edit_reservation_id">
                        <div class="col-md-6">
                            <label>Room Type</label>
                            <input type="text" name="room_type" id="edit_room_type" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Guest Name</label>
                            <input type="text" name="customer_name" id="edit_customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Check-in Date</label>
                            <input type="date" name="check_in_date" id="edit_check_in" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Check-out Date</label>
                            <input type="date" name="check_out_date" id="edit_check_out" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Amount</label>
                            <input type="number" name="amount" id="edit_amount" class="form-control" readonly>
                        </div>

                    </div>
                    <div class="modal-footer px-4">
                        <button type="submit" name="edit_reservation" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editReservation(data) {
            document.getElementById('edit_reservation_id').value = data.reservation_id;
            document.getElementById('edit_room_type').value = data.room_type;
            document.getElementById('edit_customer_name').value = data.customer_name;
            document.getElementById('edit_check_in').value = data.check_in_date;
            document.getElementById('edit_check_out').value = data.check_out_date;
            document.getElementById('edit_amount').value = data.amount;
        }
    </script>

    <script>
        // Auto-dismiss alerts after 3 seconds
        window.onload = function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 3000);
            });
        };
    </script>

    <script src="calculate_amount.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>