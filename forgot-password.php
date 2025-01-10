<?php 
session_start();
include('dbconfig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    $referenceTable = "users";
    $snapshot = $database->getReference($referenceTable)->orderByChild('email')->equalTo($email)->getSnapshot();

    if ($snapshot->hasChildren()) {
        $userData = $snapshot->getValue();
        $userKey = array_key_first($userData);
        $user = $userData[$userKey];

        // Generate OTP
        $otp = rand(100000, 999999);
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $database->getReference("$referenceTable/$userKey")->update([
            'reset_otp' => $otp,
            'reset_otp_expiry' => $otp_expiry
        ]);

        // Store user key in session for later use
        $_SESSION['reset_user_key'] = $userKey;

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username = 'arlonielockon@gmail.com';
            $mail->Password = 'pcbkrgfgjxgtnuoq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('arlonielockon@gmail.com', 'CodeCrafters');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "<p>Dear User,</p>
                           <p>You requested a password reset. Please use the following OTP to reset your password:</p>
                           <h2>$otp</h2>
                           <p>This OTP is valid for 10 minutes.</p>
                           <p>Regards,<br>CodeCrafters</p>";

            $mail->send();

            $_SESSION['status'] = "OTP sent successfully to $email.";
            header("Location: verify-otp-changepass.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to send OTP. Error: ' . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
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
        .link {
            color: #F59E0B; /* Updated primary color */
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <section class="form-section">
        <div class="form-container">
            <h2>Forgot Password</h2>
            <p class="text-center">Enter your email to receive a password reset OTP.</p>

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

            <form method="post" action="">
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Enter Your Email" required>
                </div>
                <button class="btn btn-green w-100" type="submit" name="send_otp">Send OTP</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="link">Back to Login</a>
            </div>
        </div>
    </section>
</body>
</html>

