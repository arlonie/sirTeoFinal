<?php 
session_start();
include ('dbconfig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['register'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match!';
        $_SESSION['form_data'] = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'company' => $company,
        ];
        header("Location: register.php"); // Redirect back to the register page
        exit;
    }

    // Generate OTP
    $otp = bin2hex(random_bytes(3)); // Generates a 6-character hexadecimal OTP

    // Add OTP expiration time
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Save user data along with OTP
    $postData = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'company' => $company,
        'password' => password_hash($password, PASSWORD_DEFAULT), // Store hashed password
        'otp' => $otp,
        'otp_created_at' => date("Y-m-d H:i:s"), // Optional: To expire OTP
        'otp_expiry' => $otp_expiry,
    ];

    // Send OTP to email
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; //smtp.gmail.com
        $mail->SMTPAuth   = true;
        $mail->Username = 'arlonielockon@gmail.com';
        $mail->Password = 'pcbkrgfgjxgtnuoq'; //D081D994A972FC8F5DE97758AB6F5B2A7EE7 pcbkrgfgjxgtnuoq
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('arlonielockon@gmail.com', 'CodeCrafters');
        $mail->addAddress($email); // Use the email variable here

        $referenceTable = "users";
        $postRef = $database->getReference($referenceTable)->push($postData);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Email Verification';
        $mail->Body = "<p>Dear $firstname,</p>
                       <p>Thank you for registering. Please use the following OTP to verify your email:</p>
                       <h2>$otp</h2>
                       <p>This OTP is valid until " . date("Y-m-d H:i:s", strtotime($otp_expiry)) . ".</p>
                       <p>Regards,<br>CodeCrafters</p>";

        $mail->send();

        // Set the session message for email verification
        $_SESSION['status'] = "Email verification has been sent to your email.";

        // Redirect to OTP verification page with the email
        header("Location: verify_otp.php?email=" . urlencode($email));
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to send OTP. Error: ' . $mail->ErrorInfo;
        header("Location: register.php"); // Redirect back to the register page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Register Form" />

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Include Font Awesome for the eye icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
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

        footer {
            text-align: center;
            margin-top: 20px;
        }

        .alert {
            margin-bottom: 15px;
        }

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
        }

        .position-relative {
            position: relative;
        }

        /* Styles for the back button */
        .btn-back {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            color: #6c757d;
            width: 100%;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
    </style>
</head>

<body>
    <section class="form-section">
        <div class="form-container">
            <h2>Register</h2>

            <!-- Show any session messages (errors or success) -->
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
                    <input type="text" class="form-control" name="firstname" value="<?php echo isset($_SESSION['form_data']['firstname']) ? $_SESSION['form_data']['firstname'] : ''; ?>" placeholder="First Name" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="lastname" value="<?php echo isset($_SESSION['form_data']['lastname']) ? $_SESSION['form_data']['lastname'] : ''; ?>" placeholder="Last Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>" placeholder="Email Address" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="company" value="<?php echo isset($_SESSION['form_data']['company']) ? $_SESSION['form_data']['company'] : ''; ?>" placeholder="Company Name">
                </div>
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <span class="eye-icon" id="toggle-password" onclick="togglePasswordVisibility()"><i class="fas fa-eye-slash"></i></span>
                </div>
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <button class="btn btn-green w-100" type="submit" name="register">Register</button>
            </form>

            <footer>
                <p>Already have an account? <a href="login.php" class="link">Login</a></p>
            </footer>

            <!-- Back Button -->
            <a href="index.php" class="btn btn-back">Back to Home</a>
        </div>
    </section>

    <script>
        // Toggle visibility for both password fields
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const eyeIcon = document.getElementById('toggle-password').querySelector('i');

            // Toggle password visibility
            if (passwordField.type === "password") {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }

        // Clear session data after showing the form
        <?php
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }
        ?>
    </script>
</body>

</html>

