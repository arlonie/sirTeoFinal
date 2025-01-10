<?php
session_start();

// Check if the user is logged in and OTP is verified
if (!isset($_SESSION['email'])) {
    $_SESSION['status'] = "Please log in to access the About Us.";
    header("Location: login.php");
    exit();
}

// Check if the user's email is verified
if (!empty($_SESSION['otp'])) {
    $_SESSION['status'] = "Please verify your email before accessing the About Us.";
    header("Location: verify_otp.php?email=" . urlencode($_SESSION['email']));
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us - CodeCrafters</title>

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --primary-blue: #1E40AF;
            --secondary-amber: #F59E0B;
            --light-blue: #EEF2FF;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-blue);
        }

        .navbar {
            background-color: var(--primary-blue);
        }

        .navbar-brand {
            font-weight: 600;
        }

        .navbar .nav-link {
            color: #fff;
        }

        .navbar .nav-link:hover {
            color: var(--secondary-amber);
        }

        .hero {
            background-color: var(--primary-blue);
            color: white;
            text-align: center;
            padding: 50px 15px;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
        }

        footer {
            background-color: var(--primary-blue);
            color: white;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CodeCrafters</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="welcome-user.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="aboutus.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contactus.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1>About Us</h1>
            <p>Learn more about our company, our mission, and how we help our customers.</p>
        </div>
    </header>

    <!-- Content Section -->
    <main class="py-5">
        <div class="container">
            <h3>Our Mission</h3>
            <p>At CodeCrafters, we aim to deliver innovative, seamless web solutions that provide exceptional value and experience to our users.</p>
            
            <h3>Our Vision</h3>
            <p>Our vision is to build cutting-edge web applications that simplify life and empower businesses to thrive in the digital age.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; 2023 CodeCrafters App. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

