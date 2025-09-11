<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Room</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <style></style>
</head>

<body>
  <!-- Navbar -->
  <header class="position-sticky top-0 z-2">
    <nav class="navbar navbar-expand-lg bg-secondary">
      <div class="container-fluid">
        <img
          src="Hotel reservation/assets/image/luxury-hotel-crown-key-letter-h-monogram-logo-laurel-elegant-beautiful-round-vector-emblem-sign-royalty-restaurant-97215514.webp"
          alt=""
          class="img-fluid"
          width="60px"
          height="30px" />
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-3">
            <li class="nav-item"><a class="btn btn-primary" href="home.php">Home</a></li>
            <li class="nav-item"><a class="btn btn-primary" href="room.php">Room</a></li>
            <li class="nav-item"><a class="btn btn-primary" href="Reservation.php">Reservation</a></li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'travel_agency'): ?>
              <li class="nav-item"><a class="btn btn-success" href="agency_dashboard.php">Bulk Booking</a></li>
            <?php endif; ?>

            <li class="nav-item"><a class="btn btn-primary" href="logout.php">Logout</a></li>
          </ul>

        </div>
      </div>
    </nav>
  </header>

  <main>
    <section class="container my-5">
      <h2 class="mb-4 text-center">Our Accommodations</h2>
      <p class="text-center mb-5">
        Discover our range of rooms designed to meet your needs, from standard
        rooms to luxury suites for extended stays.
      </p>

      <!-- Room Type Template -->
      <div class="row mb-5">
        <div class="col-md-6">
          <img
            src="Hotel reservation/assets/image/standadromm.jpeg"
            class="img-fluid"
            alt="Standard Room"
            width="640px"
            height="620px" />
        </div>
        <div class="col-md-6" id="standard-room">
          <h4>Standard Room</h4>
          <p>Starting from $99 per night</p>
          <ul class="list-group mb-3">
            <li class="list-group-item">1–2 Guests</li>
            <li class="list-group-item">Queen Bed</li>
            <li class="list-group-item">Free Wi-Fi</li>
            <li class="list-group-item">TV with Cable</li>
            <li class="list-group-item">Air Conditioning</li>
            <li class="list-group-item">Private Bathroom</li>
          </ul>
          <p>
            Our standard rooms provide comfortable accommodations with all the
            essential amenities for a pleasant stay. Perfect for business
            travelers or couples looking for a cozy retreat.
          </p>
          <a href="Reservation.php" class="btn btn-primary">Reserve Now</a>
        </div>
      </div>

      <!-- Deluxe Room -->
      <div class="row mb-5">
        <div class="col-md-6">
          <img
            src="Hotel reservation/assets/image/337-WaldRooms_402_0662_JA.jpg"
            class="img-fluid"
            alt="Deluxe Room"
            width="640px"
            height="620px" />
        </div>
        <div class="col-md-6" id="Deluxe-Room">
          <h4>Deluxe Room</h4>
          <p>Starting from $149 per night</p>
          <ul class="list-group mb-3">
            <li class="list-group-item">Up to 3 Guests</li>
            <li class="list-group-item">King Bed</li>
            <li class="list-group-item">High-speed Wi-Fi</li>
            <li class="list-group-item">Smart TV</li>

            <li class="list-group-item">Luxury Bathroom with Tub</li>
            <li class="list-group-item">Complimentary Breakfast</li>
          </ul>
          <p>
            Our deluxe rooms offer premium comfort with additional space and
            upgraded amenities. Enjoy luxury bedding, enhanced bathroom
            facilities, and complimentary breakfast to start your day right.
          </p>
          <a href="Reservation.php" class="btn btn-primary">Reserve Now</a>
        </div>
      </div>

      <!-- Residential Suite -->
      <div class="row mb-5">
        <div class="col-md-6">
          <img
            src="Hotel reservation/assets/image/resindance.jpeg"
            class="img-fluid"
            alt="Residential Suite"
            width="640px"
            height="620px" />
        </div>
        <div class="col-md-6" id="Residential-Suite">
          <h4>Residential Suite</h4>
          <p>Starting from $199 weekly or $1099 monthly</p>
          <ul class="list-group mb-3">
            <li class="list-group-item">Up to 3 Guests</li>
            <li class="list-group-item">King Bed</li>
            <li class="list-group-item">High-speed Wi-Fi</li>
            <li class="list-group-item">Smart TV</li>

            <li class="list-group-item">Luxury Bathroom with Tub</li>
            <li class="list-group-item">Complimentary Breakfast</li>
            <li class="list-group-item">Daily Housekeeping</li>
          </ul>
          <p>
            Our residential suites are ideal for extended stays, offering the
            comfort of home with hotel services. Book weekly or monthly and
            enjoy significantly discounted rates compared to nightly stays.
          </p>
          <a href="Reservation.php" class="btn btn-primary">Reserve Now</a>
        </div>
      </div>

      <!-- Special Rates Section -->
      <div class="bg-light p-5 text-center my-5">
        <h5>Special Rates for Travel Companies</h5>
        <p>
          We offer discounted rates for travel companies booking 3 or more
          rooms. Contact our sales team for more information on our corporate
          packages.
        </p>
        <a href="Reservation.php" class="btn btn-outline-primary">Contact Sales</a>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer>
    <footer class="bg-dark text-white pt-4 mt-5">
      <div class="container text-center text-md-start">
        <div class="row">
          <!-- About Section -->
          <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <h5 class="text-uppercase fw-bold">AFA Team</h5>
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
                <a href="#" class="text-white text-decoration-none">Services</a>
              </li>
              <li>
                <a href="#" class="text-white text-decoration-none">Contact</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>