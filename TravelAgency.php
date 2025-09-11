<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Travel Agency Bulk Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
     <header class="position-sticky top-0 z-2">
      <nav class="navbar navbar-expand-lg bg-secondary">
        <div class="container-fluid">
          <img
            src="Hotel reservation\assets\image\luxury-hotel-crown-key-letter-h-monogram-logo-laurel-elegant-beautiful-round-vector-emblem-sign-royalty-restaurant-97215514.webp"
            alt=""
            class="img-fluid"
            width="60px"
            height="30px"
          />
          <button 
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
            
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-3">
              <li class="nav-item">
                <a class="btn btn-primary" aria-current="page" href="home.php"
                  >Home</a
                >
              </li>
              <li class="nav-item">
                <a class="btn btn-primary" aria-current="page" href="room.php"
                  >Room</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="btn btn-primary" aria-current="page" href="Reservation.php"
                  >Reservation</a
                >
              </li>

              <li class="nav-item">
                <a class="btn btn-primary" aria-current="page" href="TravelAgency.php"
                  >Bulk booking </a
                >
              </li>

              <li class="nav-item"> 
                <a class="btn btn-primary" aria-current="page" href="logout.php"
                  >Logout</a
                >
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <div class="container mt-4">
        <h2>Travel Agency Bulk Booking</h2>
        <div class="row">
            <!-- Left: Booking Form -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Block Booking Form
                    </div>
                    <div class="card-body">
                        <!-- Booking Dates -->
                        <div class="row mb-3">
                            <div class="col">
                                <label for="arrival">Arrival Date</label>
                                <input type="date" id="arrival" class="form-control" />
                            </div>
                            <div class="col">
                                <label for="departure">Departure Date</label>
                                <input type="date" id="departure" class="form-control" />
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <p>Discounted rates apply for bookings of 3 or more rooms</p>
                        <div class="mb-3">
                            <label>Standard Rooms ($89/night, 10% off)</label>
                            <input type="number" class="form-control w-25" />
                        </div>
                        <div class="mb-3">
                            <label>Deluxe Rooms ($129/night, 15% off)</label>
                            <input type="number" class="form-control w-25" />
                        </div>
                        <div class="mb-3">
                            <label>Residential Suites ($529/week, 12% off)</label>
                            <input type="number" class="form-control w-25" />
                        </div>

                        <!-- Guest Information -->
                        <h5>Guest Information</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Group/Tour Name</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="col">
                                <label>Primary Contact</label>
                                <input type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Contact Phone</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="col">
                                <label>Contact Email</label>
                                <input type="email" class="form-control" />
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <h5>Special Requests</h5>
                        <textarea class="form-control mb-3"
                            placeholder="E.g. early check-in, airport transfers, etc."></textarea>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="welcomePacket" />
                            <label class="form-check-label" for="welcomePacket">Add welcome packet for each room
                                (+$15/room)</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="dailyBreakfast" />
                            <label class="form-check-label" for="dailyBreakfast">Include daily breakfast
                                (+$12/person/day)</label>
                        </div>

                        <!-- Payment Info -->
                        <h5>Payment Information</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" checked />
                            <label class="form-check-label">Direct Billing to Agency Account (50% deposit now, 50% on
                                arrival)</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" />
                            <label class="form-check-label">I agree to the terms and cancellation policy</label>
                        </div>

                        <button class="btn btn-primary">Complete Booking</button>
                        <button class="btn btn-secondary ms-2">Reset Form</button>
                    </div>
                </div>

                <!-- Past Bookings -->
                <h5>Your Past Bookings</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Group Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BLK-2023-042</td>
                            <td>European Explorers</td>
                            <td>Apr 10, 2023</td>
                            <td>Apr 15, 2023</td>
                            <td>12</td>
                        </tr>
                        <tr>
                            <td>BLK-2023-043</td>
                            <td>Business Conference 2023</td>
                            <td>Mar 22, 2023</td>
                            <td>Mar 24, 2023</td>
                            <td>8</td>
                        </tr>
                        <tr>
                            <td>BLK-2023-045</td>
                            <td>Senior Tours Group</td>
                            <td>Feb 15, 2023</td>
                            <td>Feb 20, 2023</td>
                            <td>6</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right: Booking Summary -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">Booking Summary</div>
                    <div class="card-body">
                        <p>Select dates and rooms to see booking summary.</p>
                    </div>
                </div>

                <!-- Cancellation Policy -->
                <div class="card mb-3">
                    <div class="card-header">Cancellation Policy</div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>+35 days before arrival: Full refund</li>
                            <li>15-34 days before arrival: 75% refund</li>
                            <li>7-14 days before arrival: 50% refund</li>
                            <li>3-6 days before arrival: 25% refund</li>
                            <li>0-2 days before arrival: No refund</li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Box -->

            </div>
        </div>
    </div>

     <footer>
      <footer class="bg-dark text-white pt-4 mt-5">
        <div class="container text-center text-md-start">
          <div class="row">
            <!-- About Section -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
              <h5 class="text-uppercase fw-bold"> AFA Team</h5>
              <p>
                Providing reliable services since 1990. Your satisfaction is our
                priority.
              </p>
            </div>

            <!-- Links -->
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <h6 class="text-uppercase fw-bold">Links</h6>
              <ul class="list-unstyled">
                <li>
                  <a href="home.html" class="text-white text-decoration-none">Home</a>
                </li>
                <li>
                  <a href="" class="text-white text-decoration-none">About</a>
                </li>
                <li>
                  <a href="#" class="text-white text-decoration-none">Services</a
                  >
                </li>
                <li>
                  <a href="#" class="text-white text-decoration-none"
                    >Contact</a
                  >
                </li>
              </ul>
            </div>
            <!-- Contact -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
              <h6 class="text-uppercase fw-bold">Contact</h6>
              <p><i class="bi bi-house-door me-2"></i> 123 Main Street, City</p>
              <p><i class="bi bi-envelope me-2"></i> info@example.com</p>
              <p><i class="bi bi-phone me-2"></i> +123 456 7890</p>
            </div>
          </div>
        </div>

        <!-- Copyright -->
        <div class="text-center p-3 bg-secondary mt-4"> 
          © 2025 HRS. All rights reserved by our Team.
        </div>
      </footer>
</body>

</html>