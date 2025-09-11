<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hotel reservation</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
    crossorigin="anonymous" />
  <style>
    .par {
      color: rgb(255, 20, 147);
    }

    .par:hover {
      font-size: 58px;
    }
  </style>
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
    <div class="container-fluid bg-success-subtle">
      <div class="row align-items-center">
        <div class="col-md-6 text-center">
          <h1><span class="par">welcome</span> our hotel chain</h1>
          <h1> Welcome, <span> <?= $_SESSION['name']; ?> </span></h1>

          <p>Experience luxury and comfort with our premium accommodations</p>
          <a href="Reservation.php"><button type="button" class="btn btn-primary">
              BOOK NOW
            </button></a>
        </div>
        <div class="col-md-6">
          <div
            id="carouselExampleAutoplaying"
            class="carousel slide"
            data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img
                  src="Hotel reservation/assets/image/leonardo-8529243-184407425-092113.jpg"
                  class="img-fluid"
                  alt="..." />
              </div>
              <div class="carousel-item">
                <img
                  src="Hotel reservation/assets/image/istockphoto-478851724-612x612.jpg"
                  class="img-fluid d-block w-100"
                  alt="..." />
              </div>
              <div class="carousel-item">
                <img
                  src="Hotel reservation/assets/image/the-pool-club.jpg"
                  class="img-fluid"
                  alt="..." />
              </div>
            </div>
            <button
              class="carousel-control-prev"
              type="button"
              data-bs-target="#carouselExampleAutoplaying"
              data-bs-slide="prev">
              <span
                class="carousel-control-prev-icon"
                aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button
              class="carousel-control-next"
              type="button"
              data-bs-target="#carouselExampleAutoplaying"
              data-bs-slide="next">
              <span
                class="carousel-control-next-icon"
                aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mt-5">
      <div class="row g-4">
        <!-- Standard Room -->
        <div class="col-md-4">
          <div class="card h-100">
            <img src="Hotel reservation/assets/image/standadromm.jpeg" class="card-img-top img-fluid" alt="Standard Room" />
            <div class="card-body">
              <h5 class="card-title">Standard Room</h5>
              <p class="card-text">
                The Standard Room is a cozy and budget-friendly option, ideal for solo travelers or couples. It includes a queen-sized bed, free Wi-Fi, a flat-screen TV, air conditioning, and a private bathroom. Perfect for short business trips or leisure stays with essential comforts.
              </p>
              <a href="room.php#standard-room" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>

        <!-- Deluxe Room -->
        <div class="col-md-4">
          <div class="card h-100">
            <img src="Hotel reservation/assets/image/WhatsApp Image 2025-05-31 at 22.05.27.jpeg" class="card-img-top img-fluid" alt="Deluxe Room" />
            <div class="card-body">
              <h5 class="card-title">Deluxe Room</h5>
              <p class="card-text">
                The Deluxe Room offers more space and enhanced amenities for a relaxing experience. It features a king-sized bed, smart TV, mini bar, high-speed Wi-Fi, and complimentary breakfast. Ideal for couples or professionals looking for extra comfort and style.
              </p>
              <a href="room.php#deluxe-room" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>

        <!-- Residential Room -->
        <div class="col-md-4">
          <div class="card h-100">
            <img src="Hotel reservation/assets/image/resindance.jpeg" class="card-img-top img-fluid" alt="Residential Room" />
            <div class="card-body">
              <h5 class="card-title">Residential Room</h5>
              <p class="card-text">
                The Residential Suite is designed for longer stays or guests seeking premium comfort. It includes a separate living area, king-sized bed, bathtub, fast internet, and daily housekeeping. Great for families, executives, or anyone needing a home-like hotel stay.
              </p>
              <a href="room.php#residential-room" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container mt-5">
      <div class="row">
        <div class="col-md-6">
          <h3 class="d-flex justify-content-center">About our hotel</h3>
          <p>
            Welcome to our hotel, where comfort meets elegance in every
            detail. Located in the heart of the city, our hotel offers a
            perfect blend of modern amenities and warm hospitality. Whether
            you're visiting for business or leisure, we provide a range of
            well-appointed rooms, including Standard, Deluxe, and Residential
            Suites, to suit every need. Enjoy exceptional service, delicious
            cuisine, and a relaxing atmosphere designed to make your stay
            truly memorable.
          </p>
          <p>
            At Thompukandam Village Resort, we pride ourselves on offering a
            welcoming and comfortable stay for every guest. From our
            thoughtfully designed rooms to our attentive service, every detail
            is crafted to ensure a relaxing experience. Whether you're here
            for a weekend getaway, a business trip, or an extended stay,
            you'll find everything you need—from modern amenities to a
            peaceful environment—all in one place.
          </p>
        </div>

        <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                24/7 Room service
              </button>
            </h2>

          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Fine dining Resturant
              </button>
            </h2>

          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Laundry service
              </button>
            </h2>

          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapseTwo">
                Club faciliteis
              </button>
            </h2>

          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsefive" aria-expanded="false" aria-controls="collapseTwo">
                Automatic key issurance
              </button>
            </h2>

          </div>
        </div>

        <div class="container mt-5">
          <div class="row">
            <div class="col-md-12">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.2910028938036!2d81.85527737448535!3d7.3211733133812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae51529d66a119b%3A0xa1d4e8729217dafb!2sThompukandam%20Village%20Resort!5e0!3m2!1sen!2slk!4v1748347594194!5m2!1sen!2slk"
                width="100%"
                height="450"
                style="border: 0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
  </main>
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

    <!-- Bootstrap Icons (optional) -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet" />
  </footer>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>
</body>

</html>