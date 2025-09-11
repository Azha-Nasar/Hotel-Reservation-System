<?php
session_start();
include 'config.php'; // DB connection

$showModal = false;
$modalTitle = '';
$modalMessage = '';

if (!isset($_SESSION['user_id'])) {
    die("Please login to make a bulk reservation.");
}

// Process POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId      = $_SESSION['user_id'];
    $groupName   = mysqli_real_escape_string($conn, $_POST['group_name']);
    $numPersons  = intval($_POST['num_persons']);
    $contact     = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);

    $standard    = intval($_POST['num_standard_rooms']);
    $deluxe      = intval($_POST['num_deluxe_rooms']);
    $residential = intval($_POST['num_residential_suites']);

    $checkIn     = mysqli_real_escape_string($conn, $_POST['check_in_date']);
    $checkOut    = mysqli_real_escape_string($conn, $_POST['check_out_date']);
    $paymentPlan = mysqli_real_escape_string($conn, $_POST['payment_plan']);
    $totalAmount = floatval($_POST['total_amount']);

    // Handle additional services
    $dailyBreakfast = isset($_POST['daily_breakfast']) ? 1 : 0;
    $welcomePacket = isset($_POST['welcome_packet']) ? 1 : 0;

    // Determine reservation status
    $status = ($paymentPlan === "full_credit") ? 'confirmed' : 'booked';

    // Check for duplicate reservation
    $checkSql = "SELECT * FROM bulk_reservations 
                 WHERE group_name='$groupName' 
                   AND check_in_date='$checkIn' 
                   AND check_out_date='$checkOut'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        $modalTitle = "Duplicate Reservation";
        $modalMessage = "A reservation for this group and dates already exists.";
        $showModal = true;
    } else {
        // Insert bulk reservation with additional services
        $sql = "INSERT INTO bulk_reservations 
                (user_id, group_name, num_persons, contact_person, email, phone,
                 num_standard_rooms, num_deluxe_rooms, num_residential_suites,
                 check_in_date, check_out_date, payment_plan, status, total_amount,
                 daily_breakfast, welcome_packet)
                VALUES 
                ('$userId','$groupName','$numPersons','$contact','$email','$phone',
                 '$standard','$deluxe','$residential','$checkIn','$checkOut',
                 '$paymentPlan','$status','$totalAmount','$dailyBreakfast','$welcomePacket')";

        if (mysqli_query($conn, $sql)) {
            $reservationId = mysqli_insert_id($conn);

            // Determine payment_type, method, and status
            switch ($paymentPlan) {
                case 'full_credit':
                    $paymentType = 'full';
                    $paymentMethod = 'credit_card';
                    $paymentStatus = 'completed';
                    break;
                case 'partial':
                    $paymentType = 'partial';
                    $paymentMethod = 'credit_card';
                    $paymentStatus = 'pending';
                    break;
                case 'on_arrival':
                default:
                    $paymentType = 'arrival';
                    $paymentMethod = 'cash';
                    $paymentStatus = 'pending';
                    break;
            }

            // Insert into bulk_payments
            $paySql = "INSERT INTO bulk_payments 
                       (reservation_id, amount, payment_method, payment_type, status)
                       VALUES ('$reservationId', '$totalAmount', '$paymentMethod', '$paymentType', '$paymentStatus')";
            
            if (mysqli_query($conn, $paySql)) {
                $modalTitle = "Bulk Reservation Successful";
                $modalMessage = "Thank you, <strong>$groupName</strong>! Your bulk reservation is confirmed.<br>
                                 <strong>Reservation ID:</strong> $reservationId<br>
                                 <strong>Payment Method:</strong> " . ucfirst(str_replace('_', ' ', $paymentMethod));
                $showModal = true;
            } else {
                $modalTitle = "Payment Record Failed";
                $modalMessage = "Reservation saved but payment record failed: " . mysqli_error($conn);
                $showModal = true;
            }
        } else {
            $modalTitle = "Reservation Failed";
            $modalMessage = "Error: " . mysqli_error($conn);
            $showModal = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bulk Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="position-sticky top-0 z-2">
        <nav class="navbar navbar-expand-lg bg-secondary">
            <div class="container-fluid">
                <img src="Hotel reservation/assets/image/luxury-hotel-crown-key-letter-h-monogram-logo-laurel-elegant-beautiful-round-vector-emblem-sign-royalty-restaurant-97215514.webp" alt="Hotel Logo" class="img-fluid" width="60" />
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto gap-3">
                        <li class="nav-item"><a class="btn btn-primary" href="home.php">Home</a></li>
                        <li class="nav-item"><a class="btn btn-primary" href="room.php">Room</a></li>
                        <li class="nav-item"><a class="btn btn-primary" href="reservation.php">Reservation</a></li>
                        <li class="nav-item"><a class="btn btn-success" href="bulk_booking.php">Bulk Booking</a></li>
                        <li class="nav-item"><a class="btn btn-primary" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <!-- Back Button -->
        <a href="agency_dashboard.php" class="btn btn-primary mb-3">Back</a>
        <h2 class="mb-4">Bulk Reservation</h2>
        <div class="row">
            <!-- LEFT FORM -->
            <div class="col-lg-8">
                <form method="POST" id="bulkBookingForm" novalidate>
                    <!-- Step 1: Group Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Group Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Group/Tour Name *</label>
                                    <input type="text" name="group_name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Number of Persons *</label>
                                    <input type="number" name="num_persons" id="num_persons" class="form-control" min="1" value="1" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Primary Contact *</label>
                                    <input type="text" name="contact_person" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="tel" name="phone" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Reservation Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Reservation Details</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Standard Rooms ($100/night)</label>
                                    <input type="number" name="num_standard_rooms" id="num_standard" class="form-control" min="0" value="0" data-price="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Deluxe Rooms ($150/night)</label>
                                    <input type="number" name="num_deluxe_rooms" id="num_deluxe" class="form-control" min="0" value="0" data-price="150">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Residential Suites ($250/week)</label>
                                    <input type="number" name="num_residential_suites" id="num_residential" class="form-control" min="0" value="0" data-price="250">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Arrival Date *</label>
                                    <input type="date" name="check_in_date" id="checkin" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Departure Date *</label>
                                    <input type="date" name="check_out_date" id="checkout" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Additional Services -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Additional Services</h5>

                            <div class="form-check">
                                <input class="form-check-input service-checkbox" type="checkbox" name="daily_breakfast" value="1" data-price="5" id="svc_breakfast">
                                <label class="form-check-label" for="svc_breakfast">Daily Breakfast (per person) - $5</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input service-checkbox" type="checkbox" name="welcome_packet" value="1" data-price="30" id="svc_welcome">
                                <label class="form-check-label" for="svc_welcome">Welcome Packet / Airport Pickup (one-time) - $30</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input service-checkbox" type="checkbox" name="extra_bed" value="1" data-price="20" id="svc_extrabed">
                                <label class="form-check-label" for="svc_extrabed">Extra Bed (per night) - $20</label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Coupon -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Discount / Coupon</h5>
                            <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Enter coupon code">
                            <div id="couponMessage" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- Step 5: Payment Options -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Payment Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Payment Plan *</label>
                                    <select name="payment_plan" id="payment_plan" class="form-select" required>
                                        <option value="">Select Payment Plan</option>
                                        <option value="full_credit">Full Payment (Credit Card)</option>
                                        <option value="partial">Partial Payment (50% card now, 50% cash later)</option>
                                        <option value="on_arrival">On Arrival (Cash)</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="cardNameDiv" style="display: none;">
                                    <label class="form-label">Name on Card</label>
                                    <input type="text" name="card_name" class="form-control">
                                </div>
                                <div class="col-md-6" id="cardNumberDiv" style="display: none;">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" name="card_number" class="form-control" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="col-md-3" id="expiryDiv" style="display: none;">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY">
                                </div>
                                <div class="col-md-3" id="cvvDiv" style="display: none;">
                                    <label class="form-label">CVV</label>
                                    <input type="text" name="cvv" class="form-control" placeholder="XXX">
                                </div>
                                <div class="col-12 form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" required  for="terms">I agree to the terms, conditions and policy *</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="total_amount" id="calculatedAmount" />
                    <button type="submit" class="btn btn-success w-100">Confirm Bulk Booking</button>
                </form>
            </div>

            <!-- RIGHT SUMMARY -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Reservation Summary</div>
                    <div class="card-body" id="summary">
                        <p>Please select rooms and dates to see summary</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-danger text-white">Cancellation Policy</div>
                    <div class="card-body">
                        <ul>
                            <li>+35 days before arrival: Full refund</li>
                            <li>15-34 days: 75% refund</li>
                            <li>7-14 days: 50% refund</li>
                            <li>3-6 days: 25% refund</li>
                            <li>0-2 days: No refund</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-success text-white text-center">
                    <h5 class="modal-title w-100 fw-bold"><?php echo $modalTitle; ?></h5>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="fs-5 mb-3"><?php echo $modalMessage; ?></p>

                    <?php if (isset($paymentMethod) && strtolower($paymentMethod) !== "credit_card"): ?>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Show modal if needed -->
    <?php if ($showModal): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                confirmationModal.show();
            });
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const numPersons = document.getElementById('num_persons');
            const checkIn = document.getElementById('checkin');
            const checkOut = document.getElementById('checkout');
            const numStandard = document.getElementById('num_standard');
            const numDeluxe = document.getElementById('num_deluxe');
            const numResidential = document.getElementById('num_residential');
            const services = document.querySelectorAll('.service-checkbox');
            const amount = document.getElementById('calculatedAmount');
            const summaryDiv = document.getElementById('summary');
            const couponInput = document.getElementById('coupon_code');
            const couponMessage = document.getElementById('couponMessage');
            const paymentPlan = document.getElementById('payment_plan');
            const cardNameDiv = document.getElementById('cardNameDiv');
            const cardNumberDiv = document.getElementById('cardNumberDiv');
            const expiryDiv = document.getElementById('expiryDiv');
            const cvvDiv = document.getElementById('cvvDiv');

            const rates = {
                standard: 10000,
                deluxe: 15000,
                residential: 25000
            };

            const servicePrices = {
                'daily_breakfast': 500,
                'welcome_packet': 3000,
                'extra_bed': 2000
            };

            const coupons = {
                'SUMMER5': 5,
                'WELCOME10': 10
            };

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            checkIn.min = today;
            checkOut.min = today;

            // Update checkout minimum when checkin changes
            checkIn.addEventListener('change', () => {
                checkOut.min = checkIn.value;
                if (checkOut.value && checkOut.value <= checkIn.value) {
                    checkOut.value = '';
                }
                calculateAmount();
            });

            function calculateAmount() {
                if (!checkIn.value || !checkOut.value) {
                    summaryDiv.innerHTML = '<p>Please select rooms and dates to see summary</p>';
                    amount.value = '';
                    return;
                }

                const checkinDate = new Date(checkIn.value);
                const checkoutDate = new Date(checkOut.value);
                let nights = (checkoutDate - checkinDate) / (1000 * 60 * 60 * 24);
                
                if (nights <= 0) {
                    summaryDiv.innerHTML = '<p class="text-danger">Check-out must be after check-in.</p>';
                    return;
                }

                let total = 0;
                let details = '';
                let totalRooms = (parseInt(numStandard.value || 0) + parseInt(numDeluxe.value || 0) + parseInt(numResidential.value || 0));

                // Calculate room costs
                if (numStandard.value > 0) {
                    const cost = numStandard.value * rates.standard * nights;
                    total += cost;
                    details += `${numStandard.value} x Standard Room (${nights} nights): ₹${cost.toLocaleString()}<br>`;
                }
                if (numDeluxe.value > 0) {
                    const cost = numDeluxe.value * rates.deluxe * nights;
                    total += cost;
                    details += `${numDeluxe.value} x Deluxe Room (${nights} nights): ₹${cost.toLocaleString()}<br>`;
                }
                if (numResidential.value > 0) {
                    let weeks = Math.ceil(nights / 7);
                    const cost = numResidential.value * rates.residential * weeks;
                    total += cost;
                    details += `${numResidential.value} x Residential Suite (${weeks} weeks): ₹${cost.toLocaleString()}<br>`;
                }

                // Calculate service costs
                let selectedServices = [];
                services.forEach(s => {
                    if (s.checked) {
                        let serviceCost = 0;
                        const serviceName = s.name;
                        
                        if (serviceName === 'daily_breakfast') {
                            serviceCost = servicePrices[serviceName] * parseInt(numPersons.value || 1) * nights;
                            selectedServices.push(`Daily Breakfast: ₹${serviceCost.toLocaleString()}`);
                        } else if (serviceName === 'extra_bed') {
                            serviceCost = servicePrices[serviceName] * nights;
                            selectedServices.push(`Extra Bed: ₹${serviceCost.toLocaleString()}`);
                        } else if (serviceName === 'welcome_packet') {
                            serviceCost = servicePrices[serviceName];
                            selectedServices.push(`Welcome Packet: ₹${serviceCost.toLocaleString()}`);
                        }
                        
                        total += serviceCost;
                    }
                });

                // Apply coupon discount
                let discountPercent = 0;
                const code = couponInput.value.trim().toUpperCase();
                if (code && coupons[code]) {
                    discountPercent = coupons[code];
                    couponMessage.innerHTML = `<span class="text-success">Coupon applied: ${discountPercent}% off</span>`;
                } else if (code) {
                    couponMessage.innerHTML = `<span class="text-danger">Invalid Coupon Code</span>`;
                } else {
                    couponMessage.innerHTML = '';
                }

                // Travel company discount for 3+ rooms
                let travelDiscount = totalRooms >= 3 ? 10 : 0;
                let totalDiscountPercent = discountPercent + travelDiscount;
                let discountAmount = total * totalDiscountPercent / 100;
                let finalAmount = total - discountAmount;

                amount.value = finalAmount.toFixed(2);

                // Build summary
                let summaryHTML = `
                    <strong>Booking Details:</strong><br>
                    ${details}
                `;

                if (selectedServices.length > 0) {
                    summaryHTML += `<strong>Additional Services:</strong><br>${selectedServices.join('<br>')}<br>`;
                }

                summaryHTML += `<hr><strong>Subtotal:</strong> ₹${total.toLocaleString()}<br>`;

                if (totalDiscountPercent > 0) {
                    summaryHTML += `<strong>Discount (${totalDiscountPercent}%):</strong> -₹${discountAmount.toFixed(0)}<br>`;
                }

                summaryHTML += `<strong class="text-success">Total Amount:</strong> ₹${finalAmount.toFixed(0)}`;

                summaryDiv.innerHTML = summaryHTML;
            }

            // Event listeners
            [numStandard, numDeluxe, numResidential, numPersons, checkIn, checkOut].forEach(el => 
                el.addEventListener('input', calculateAmount)
            );
            services.forEach(s => s.addEventListener('change', calculateAmount));
            couponInput.addEventListener('input', calculateAmount);

            // Payment plan change handler
            paymentPlan.addEventListener('change', () => {
                const method = paymentPlan.value;
                const showCard = (method === 'full_credit' || method === 'partial');
                
                cardNameDiv.style.display = showCard ? 'block' : 'none';
                cardNumberDiv.style.display = showCard ? 'block' : 'none';
                expiryDiv.style.display = showCard ? 'block' : 'none';
                cvvDiv.style.display = showCard ? 'block' : 'none';
                
                calculateAmount();
            });

            // Form validation
            document.getElementById('bulkBookingForm').addEventListener('submit', function(e) {
                const groupName = document.querySelector('[name="group_name"]').value;
                const contactPerson = document.querySelector('[name="contact_person"]').value;
                const terms = document.getElementById('terms').checked;
                const totalAmount = parseFloat(amount.value || 0);

                if (!groupName || !contactPerson || !checkIn.value || !checkOut.value || !paymentPlan.value || !terms || totalAmount <= 0) {
                    e.preventDefault();
                    alert('Please fill all required fields and accept terms & conditions.');
                    return false;
                }

                // Additional validation for room selection
                const totalRooms = parseInt(numStandard.value || 0) + parseInt(numDeluxe.value || 0) + parseInt(numResidential.value || 0);
                if (totalRooms === 0) {
                    e.preventDefault();
                    alert('Please select at least one room.');
                    return false;
                }
            });

            // Initial calculation
            calculateAmount();
        });
    </script>
</body>
</html>