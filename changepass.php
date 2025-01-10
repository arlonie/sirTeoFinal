<?php
session_start();
include('dbconfig.php');

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_key'])) {
    $_SESSION['error'] = "Please verify your OTP first.";
    header("Location: forgot-password.php");
    exit;
}

if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (!isPasswordStrong($new_password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.";
    } else {
        $user_key = $_SESSION['reset_user_key'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            $user = $database->getReference("users/$user_key")->getValue();
            
            // Check password history
            $password_history = $user['password_history'] ?? [];
            foreach ($password_history as $old_password) {
                if (password_verify($new_password, $old_password)) {
                    $_SESSION['error'] = "You cannot use a previously used password.";
                    break;
                }
            }

            if (!isset($_SESSION['error'])) {
                // Update password and history
                array_unshift($password_history, $hashed_password);
                $password_history = array_slice($password_history, 0, 5);

                $database->getReference("users/$user_key")->update([
                    'password' => $hashed_password,
                    'password_history' => $password_history,
                    'reset_otp' => null,
                    'reset_otp_expiry' => null
                ]);

                $_SESSION['status'] = "Password changed successfully.";
                unset($_SESSION['otp_verified'], $_SESSION['reset_user_key']);
                header("Location: login.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating password. Please try again.";
        }
    }
}

function isPasswordStrong($password) {
    return (strlen($password) >= 8 &&
            preg_match("/[A-Z]/", $password) &&
            preg_match("/[a-z]/", $password) &&
            preg_match("/[0-9]/", $password) &&
            preg_match("/[^A-Za-z0-9]/", $password));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
        }

        .position-relative {
            position: relative;
        }
    </style>
</head>
<body>
    <section class="form-section">
        <div class="form-container">
            <h2>Change Password</h2>

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
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter New Password" required>
                    <span class="eye-icon" id="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                </div>
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>

                </div>
                <button class="btn btn-green w-100" type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </section>

    <script>
        function togglePasswordVisibility() {
            const newPasswordField = document.getElementById('new_password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const eyeIcon = document.getElementById('toggle-password').querySelector('i');

            if (newPasswordField.type === "password" || confirmPasswordField.type === "password") {
                newPasswordField.type = "text";
                confirmPasswordField.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                newPasswordField.type = "password";
                confirmPasswordField.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
</html>

