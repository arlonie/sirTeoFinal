<?php
session_start();
include('dbconfig.php');

if (!isset($_SESSION['reset_user_key'])) {
    $_SESSION['error'] = "Please request a new OTP.";
    header("Location: forgot-password.php");
    exit;
}

if (isset($_POST['verify_otp'])) {
    $inputOtp = trim($_POST['otp']);
    
    if (empty($inputOtp)) {
        $_SESSION['error'] = "Please enter the OTP.";
    } else {
        $userKey = $_SESSION['reset_user_key'];
        $user = $database->getReference("users/$userKey")->getValue();

        if ($user && isset($user['reset_otp']) && isset($user['reset_otp_expiry'])) {
            if (time() < strtotime($user['reset_otp_expiry'])) {
                if ($inputOtp == $user['reset_otp']) {
                    // OTP is correct, allow password change
                    $_SESSION['otp_verified'] = true;
                    header("Location: changepass.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Invalid OTP. Please try again.";
                }
            } else {
                $_SESSION['error'] = "OTP has expired. Please request a new one.";
                unset($_SESSION['reset_user_key']);
                header("Location: forgot-password.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid reset attempt. Please try again.";
            unset($_SESSION['reset_user_key']);
            header("Location: forgot-password.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
            border-radius: 10px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
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
        }
        .btn-green:hover {
            background-color: #1E40AF/90; /* Updated primary color */
        }
    </style>
</head>
<body>
    <section class="form-section">
        <div class="form-container">
            <h2>Verify OTP</h2>

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

            <form action="" method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" name="otp" placeholder="Enter Your OTP" required>
                </div>
                <button class="btn btn-green w-100" type="submit" name="verify_otp">Verify OTP</button>
            </form>
        </div>
    </section>
</body>
</html>

