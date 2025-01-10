<?php
session_start();
include('dbconfig.php');

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);
    $referenceTable = "users";
    $userRef = $database->getReference($referenceTable)->orderByChild('email')->equalTo($email)->getSnapshot();

    if ($userRef->exists()) {
        $userData = $userRef->getValue();
        $user = reset($userData);
        $otpStored = $user['otp'];
        $otpExpiry = $user['otp_expiry'];

        if (strtotime($otpExpiry) < time()) {
            $_SESSION['error'] = "OTP has expired. Please request a new one.";
            header("Location: forgot-password.php");
            exit;
        }
    } else {
        echo "User not found!";
        exit;
    }
}

if (isset($_POST['verify_otp'])) {
    $otpEntered = $_POST['otp'];

    if ($otpEntered == $otpStored) {
        $userKey = key($userData);

        $database->getReference("users/{$userKey}")
                 ->update([
                     'otp' => null, 
                     'otp_created_at' => null,
                     'otp_expiry' => null,
                     'email_verified' => true
                 ]);

        $_SESSION['email_verified'] = true;
        $_SESSION['status'] = "Your email has been verified successfully!";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Incorrect OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>OTP Verification</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #EEF2FF; /* Updated background color */
        }

        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #EEF2FF; /* Updated background color */
        }

        .form-container {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
        }

        .form-container h2 {
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            color: #1E40AF; /* Updated primary color */
        }

        .btn-green {
            background-color: #1E40AF; /* Updated primary color */
            border: none;
            color: #ffffff;
            font-weight: bold;
            border-radius: 5px;
        }

        .btn-green:hover {
            background-color: #1E40AF/90; /* Updated primary color */
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #1E40AF; /* Updated primary color */
        }

        .alert {
            margin-top: 15px;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #555;
        }

        .footer-text {
            color: #F59E0B; /* Updated secondary color */
        }

        .link {
            color: #F59E0B; /* Updated secondary color */
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container form-section">
        <div class="form-container">
            <h2>OTP Verification</h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['status'])) {
                echo "<div class='alert alert-success'>" . $_SESSION['status'] . "</div>";
                unset($_SESSION['status']);
            }
            ?>

            <form action="" method="post" class="mt-3">
                <div class="mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" class="form-control" name="otp" id="otp" required>
                </div>
                <button type="submit" name="verify_otp" class="btn btn-green btn-block">Verify OTP</button>
            </form>

            <footer>
                <p class="footer-text">Powered by CodeCrafeters</p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

