<?php
session_start();
include 'config.php'; // DB connection

$showModal = false;
$modalTitle = '';
$modalMessage = '';

if (!isset($_SESSION['user_id'])) {
    die("Please login to make a reservation.");
}

// Only process POST if last reservation not set
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['last_reservation'])) {
    $user_id       = $_SESSION['user_id'];
    $customer_name = mysqli_real_escape_string($conn, $_POST['first_name'] . ' ' . $_POST['last_name']);
    $room_type     = mysqli_real_escape_string($conn, $_POST['roomType']);
    $num_rooms     = intval($_POST['num_rooms']);
    $adults        = intval($_POST['adults']);
    $children      = intval($_POST['children']);
    $id_number     = mysqli_real_escape_string($conn, $_POST['id_number']);
    $check_in      = mysqli_real_escape_string($conn, $_POST['arrival']);
    $check_out     = mysqli_real_escape_string($conn, $_POST['departure']);
    $amount        = floatval($_POST['amount']);
    $payment_method= $_POST['payment_method'];
    $room_id       = 1;

    $status = ($payment_method === "credit_card") ? 'confirmed' : 'booked';

    $sql = "INSERT INTO reservations 
        (user_id, room_id, customer_name, room_type, num_rooms, adults, children, id_number, check_in_date, check_out_date, status, amount) 
        VALUES 
        ('$user_id', '$room_id', '$customer_name', '$room_type', '$num_rooms', '$adults', '$children', '$id_number', '$check_in', '$check_out', '$status', '$amount')";

    if (mysqli_query($conn, $sql)) {
        $reservation_id = mysqli_insert_id($conn);

        $payment_status = ($payment_method === "credit_card") ? 'completed' : 'pending';
        $payment_sql = "INSERT INTO payments (reservation_id, amount, payment_method, status) 
                        VALUES ('$reservation_id', '$amount', '$payment_method', '$payment_status')";
        mysqli_query($conn, $payment_sql);

        // Store reservation info in session to prevent duplicate and pass payment method
        $_SESSION['last_reservation'] = $reservation_id;
        $_SESSION['last_payment_method'] = $payment_method;

        // Redirect to show modal
        header("Location: reservation.php?success=1");
        exit;
    } else {
        $modalTitle = "Reservation Failed";
        $modalMessage = "Error: " . mysqli_error($conn);
        $showModal = true;
    }
}

// Show modal if redirected after success
if (isset($_GET['success']) && isset($_SESSION['last_reservation'])) {
    $reservation_id = $_SESSION['last_reservation'];
    $payment_method = $_SESSION['last_payment_method'] ?? 'cash';
    $customer_name = $_SESSION['user_name'] ?? ''; // optional
    $modalTitle = "Reservation Successful";
    $modalMessage = "Thank you, $customer_name! Your reservation is confirmed.<br>
                     <strong>Reservation ID:</strong> $reservation_id<br>
                     <strong>Payment Method:</strong> " . ucfirst(str_replace('_',' ',$payment_method));
    $showModal = true;

    // Unset session to prevent duplicate modal on refresh
    unset($_SESSION['last_reservation']);
    unset($_SESSION['last_payment_method']);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotel Reservation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'travel_agency'): ?>
            <li class="nav-item"><a class="btn btn-success" href="agency_dashboard.php">Bulk Booking</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="btn btn-primary" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main class="container my-5">
  <h2 class="mb-4">Make a Reservation</h2>
  <div class="row">
    <div class="col-lg-8">
      <form method="POST" action="reservation.php">
        <!-- Reservation Details -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Reservation Details</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Room Type</label>
                <select id="roomType" name="roomType" class="form-select" required>
                  <option value="">Select a room type</option>
                  <option value="Single">Single</option>
                  <option value="Double">Double</option>
                  <option value="Suite">Suite - Per Night</option>
                  <option value="Residential_Weekly">Residential Suite - Weekly</option>
                  <option value="Residential_Monthly">Residential Suite - Monthly</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Number of Rooms</label>
                <input type="number" name="num_rooms" id="num_rooms" class="form-control" min="1" value="1" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Adults</label>
                <input type="number" name="adults" id="adults" class="form-control" min="1" value="1" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Children</label>
                <input type="number" name="children" id="children" class="form-control" min="0" value="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Check-in Date</label>
                <input type="date" name="arrival" id="arrival" class="form-control" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Check-out Date</label>
                <input type="date" name="departure" id="departure" class="form-control" required />
              </div>
            </div>
          </div>
        </div>

        <!-- Guest Information -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Guest Information</h5>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">ID/Passport Number</label><input type="text" name="id_number" class="form-control" required></div>
            </div>
          </div>
        </div>

        <!-- Additional Services -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Additional Services</h5>
            <?php
            $service_options = [
              'restaurant' => 'Restaurant Package',
              'room_service' => 'Room Service',
              'laundry' => 'Laundry Service',
              'telephone' => 'Telephone Service',
              'club' => 'Club Facility'
            ];
            foreach ($service_options as $key => $label): ?>
              <div class="form-check">
                <input class="form-check-input service-checkbox" type="checkbox" name="services[]" value="<?= $key ?>" id="service<?= $key ?>">
                <label class="form-check-label" for="service<?= $key ?>"><?= $label ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Payment Information</h5>
            <div class="row g-4">
              <div class="col-md-6"><label class="form-label">Name on Card</label><input type="text" name="card_name" class="form-control"></div>
              <div class="col-12"><label class="form-label">Card Number</label><input type="text" name="card_number" class="form-control" placeholder="XXXX XXXX XXXX XXXX"></div>
              <div class="col-md-4"><label class="form-label">Expiry Date</label><input type="text" name="expiry_date" class="form-control" placeholder="MM/YY"></div>
              <div class="col-md-4"><label class="form-label">CVV</label><input type="text" name="cvv" class="form-control" placeholder="XXX"></div>
              <div class="col-md-6">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" required>
                  <option value="">Select Payment Method</option>
                  <option value="credit_card">Credit Card</option>
                  <option value="cash">Cash</option>
                </select>
              </div>
              <div class="col-12 form-check mt-2">
                <input type="checkbox" class="form-check-input" name="terms" required>
                <label class="form-check-label">I agree to the terms and conditions</label>
              </div>
            </div>
          </div>
        </div>

        <input type="hidden" name="amount" id="calculatedAmount" />
        <button type="submit" class="btn btn-primary w-100 mb-4">Complete Reservation</button>
      </form>
    </div>

    <div class="col-lg-4">
      <div class="d-grid mb-3">
        <a href="reservation_history.php" class="btn btn-outline-primary">View Reservation History</a>
      </div>

      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Reservation Summary</div>
        <div class="card-body" id="summary">
          <p>Please select room and dates to see summary</p>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg custom-modal-length">
    <div class="modal-content border-0 shadow-lg rounded-3">

      <div class="modal-header bg-success text-white text-center">
        <h5 class="modal-title w-100 fw-bold"><?= $modalTitle ?></h5>
      </div>

      <div class="modal-body text-center p-4">
        
        <!-- Only show the prepared modal message once -->
        <p class="fs-5 mb-3"><?= $modalMessage ?></p>

        <!-- Show warning only if NOT credit card -->
        <?php if (!isset($payment_method) || strtolower($payment_method) !== "credit_card"): ?>
          <div class="alert alert-warning border-start border-4 border-warning text-start mt-3">
            <small>
              <strong>Note:</strong> No credit card provided.  
              Your reservation will be cancelled at <strong>7 PM</strong> on the arrival date if not confirmed.
            </small>
          </div>
        <?php endif; ?>

      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>


<style>
.custom-modal-length { width: 600px; }
.custom-modal-length .modal-content { width: 100%; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const roomType = document.getElementById('roomType');
  const checkIn = document.getElementById('arrival');
  const checkOut = document.getElementById('departure');
  const numRooms = document.getElementById('num_rooms');
  const services = document.querySelectorAll('.service-checkbox');
  const amount = document.getElementById('calculatedAmount');
  const summaryDiv = document.getElementById('summary');

  const rates = { Single: 10000, Double: 15000, Suite: 25000 , Residential_Weekly: 150000, Residential_Monthly: 250000 };
  const serviceCharge = { restaurant:5000, room_service:2000, laundry:1500, telephone:1000, club:3000 };

  function calculateAmount() {
    if (!roomType.value || !checkIn.value || !checkOut.value || !numRooms.value) {
        summaryDiv.innerHTML = '<p>Please select room and dates to see summary</p>';
        amount.value = '';
        return;
    }

    let total = 0;
    const checkinDate = new Date(checkIn.value);
    const checkoutDate = new Date(checkOut.value);

    if (roomType.value === "Residential_Weekly") {
        let diffWeeks = Math.ceil((checkoutDate - checkinDate) / (1000*60*60*24*7));
        total = rates[roomType.value] * diffWeeks * parseInt(numRooms.value);
    } else if (roomType.value === "Residential_Monthly") {
        let diffMonths = Math.ceil((checkoutDate.getFullYear() - checkinDate.getFullYear())*12 + (checkoutDate.getMonth() - checkinDate.getMonth()));
        total = rates[roomType.value] * diffMonths * parseInt(numRooms.value);
    } else {
        const diffDays = (checkoutDate - checkinDate) / (1000*60*60*24);
        if(diffDays <=0){summaryDiv.innerHTML='<p>Check-out date must be after check-in date.</p>'; amount.value=''; return;}
        total = rates[roomType.value] * diffDays * parseInt(numRooms.value);
    }

    let selectedServices = [];
    services.forEach(s => { if(s.checked){ total += serviceCharge[s.value]; selectedServices.push(s.nextElementSibling.innerText); } });

    amount.value = total;
    summaryDiv.innerHTML = `
        <p><strong>Room Type:</strong> ${roomType.options[roomType.selectedIndex].text}</p>
        <p><strong>Check-in:</strong> ${checkIn.value}</p>
        <p><strong>Check-out:</strong> ${checkOut.value}</p>
        <p><strong>Number of Rooms:</strong> ${numRooms.value}</p>
        ${selectedServices.length>0?'<p><strong>Services:</strong> '+selectedServices.join(', ')+'</p>':''}
        <p><strong>Total Amount:</strong> $${total.toFixed(2)}</p>
    `;
  }

  roomType.addEventListener('change', calculateAmount);
  checkIn.addEventListener('change', calculateAmount);
  checkOut.addEventListener('change', calculateAmount);
  numRooms.addEventListener('input', calculateAmount);
  services.forEach(s=>s.addEventListener('change', calculateAmount));
});

<?php if($showModal): ?>
const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
confirmationModal.show();
<?php endif; ?>
</script>
</body>
</html>
