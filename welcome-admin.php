<?php
session_start();
include('dbconfig.php'); // Include Firebase config

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in and is the admin
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'codecrafters@gmail.com') {
    header("Location: login.php");
    exit;
}

// Function to send email
function sendDeleteNotificationEmail($email, $firstname) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'arlonielockon@gmail.com'; // Your Gmail address
        $mail->Password = 'pcbkrgfgjxgtnuoq'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('arlonielockon@gmail.com', 'CodeCrafters Admin');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Account Deletion Notification';
        $mail->Body = "
            <h2>Account Deletion Notification</h2>
            <p>Dear $firstname,</p>
            <p>We regret to inform you that your account has been deleted from our system.</p>
            <p>If you believe this is an error or have any questions, please contact our support team.</p>
            <p>Best regards,<br>CodeCrafters Team</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle delete user action
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $user_email = $_POST['user_email'];
    $user_firstname = $_POST['user_firstname'];

    // Delete user from Firebase
    $database->getReference('users/' . $user_id)->remove();

    // Send email notification
    if (sendDeleteNotificationEmail($user_email, $user_firstname)) {
        $_SESSION['status'] = "User deleted successfully and notification email sent.";
    } else {
        $_SESSION['status'] = "User deleted successfully but failed to send notification email.";
    }

    header("Location: welcome-admin.php");
    exit();
}

// Fetch all users from Firebase
$referenceTable = "users";
$snapshot = $database->getReference($referenceTable)->getValue();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome Admin - CodeCrafters</title>

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .table-container {
            margin-top: 30px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>

<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="welcome-admin.php">CodeCrafters</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="welcome-admin.php">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1>Welcome Admin</h1>
            <p>Here are the registered users:</p>
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
            <div class="table-container">
                <?php if ($snapshot): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $index = 1;
                            foreach ($snapshot as $key => $user): ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td><?= htmlspecialchars($user['firstname']) ?></td>
                                    <td><?= htmlspecialchars($user['lastname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?= empty($user['otp']) ? 'Verified' : 'Not Verified' ?>
                                    </td>
                                    <td>
                                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $key; ?>">
                                            <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                            <input type="hidden" name="user_firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php 
                                $index++;
                            endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
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


<?php
session_start();
include('dbconfig.php'); // Include Firebase config

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in and is the admin
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'codecrafters@gmail.com') {
    header("Location: login.php");
    exit;
}

// Function to send email
function sendDeleteNotificationEmail($email, $firstname) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'arlonielockon@gmail.com'; // Your Gmail address
        $mail->Password = 'pcbkrgfgjxgtnuoq'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('arlonielockon@gmail.com', 'CodeCrafters Admin');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Account Deletion Notification';
        $mail->Body = "
            <h2>Account Deletion Notification</h2>
            <p>Dear $firstname,</p>
            <p>We regret to inform you that your account has been deleted from our system.</p>
            <p>If you believe this is an error or have any questions, please contact our support team.</p>
            <p>Best regards,<br>CodeCrafters Team</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle delete user action
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $user_email = $_POST['user_email'];
    $user_firstname = $_POST['user_firstname'];

    // Delete user from Firebase
    $database->getReference('users/' . $user_id)->remove();

    // Send email notification
    if (sendDeleteNotificationEmail($user_email, $user_firstname)) {
        $_SESSION['status'] = "User deleted successfully and notification email sent.";
    } else {
        $_SESSION['status'] = "User deleted successfully but failed to send notification email.";
    }

    header("Location: welcome-admin.php");
    exit();
}

// Fetch all users from Firebase
$referenceTable = "users";
$snapshot = $database->getReference($referenceTable)->getValue();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome Admin - CodeCrafters</title>

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .table-container {
            margin-top: 30px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>

<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="welcome-admin.php">CodeCrafters</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="welcome-admin.php">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1>Welcome Admin</h1>
            <p>Here are the registered users:</p>
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
            <div class="table-container">
                <?php if ($snapshot): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $index = 1;
                            foreach ($snapshot as $key => $user): ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td><?= htmlspecialchars($user['firstname']) ?></td>
                                    <td><?= htmlspecialchars($user['lastname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?= empty($user['otp']) ? 'Verified' : 'Not Verified' ?>
                                    </td>
                                    <td>
                                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $key; ?>">
                                            <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                            <input type="hidden" name="user_firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php 
                                $index++;
                            endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
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

