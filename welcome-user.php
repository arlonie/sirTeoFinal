<?php
session_start();
// Second ni Sir na kay na double na

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['status'] = "Please log in to access the welcome page.";
    header("Location: login.php");
    exit();
}

// Check if the user's email is verified
if (!isset($_SESSION['email_verified']) || $_SESSION['email_verified'] !== true) {
    $_SESSION['status'] = "Please verify your email before accessing the welcome page.";
    header("Location: verify_otp.php?email=" . urlencode($_SESSION['email']));
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - CodeCrafters</title>

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #EEF2FF;
        }

        .navbar {
            background-color: #1E40AF;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .navbar .nav-link {
            color: #fff;
        }

        .hero {
            background-color: #1E40AF;
            color: white;
            text-align: center;
            padding: 50px 15px;
        }

        footer {
            background-color: #1E40AF;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .btn-primary {
            background-color: #F59E0B;
            border-color: #F59E0B;
        }

        .btn-primary:hover {
            background-color: #D97706;
            border-color: #D97706;
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
                        <a class="nav-link active" href="welcome-user.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About Us</a>
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
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['firstname']) . ' ' . htmlspecialchars($_SESSION['lastname']); ?>!</h1>
            <p>Your personalized CodeCrafters experience starts here.</p>
        </div>
    </header>

    <!-- Content Section -->
    <main class="py-5">
        <div class="container">
            <div class="alert alert-success">
                <p>Thank you for logging in, <?php echo htmlspecialchars($_SESSION['email']); ?>.</p>
            </div>
            <p class="text-muted">Feel free to explore the features available to you. Use the navigation bar to move around the site.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2023 CodeCrafters. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

