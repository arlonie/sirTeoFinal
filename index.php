<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - CodeCrafters</title>

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

        .card-header {
            background-color: var(--secondary-amber);
            color: white;
        }

        .btn-primary {
            background-color: var(--secondary-amber);
            border-color: var(--secondary-amber);
        }

        .btn-primary:hover {
            background-color: #D97706;
            border-color: #D97706;
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
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contactus.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div</div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1>Welcome to CodeCrafters</h1>
            <p>Your innovative, seamless web experience.</p>
        </div>
    </header>

    <!-- Content Section -->
    <main class="py-5">
        <div class="container">
            <?php
            if (isset($_SESSION['status'])) {
                echo "<div class='alert alert-success'>" . $_SESSION['status'] . "</div>";
                unset($_SESSION['status']);
            }
            ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-header">
                            <h4>Discover CodeCrafters
                                <a href="register.php" class="btn btn-primary float-end">Get Started</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Experience the power of innovative web solutions tailored for your success.</p>
                        </div>
                    </div>
                </div>
            </div>
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

