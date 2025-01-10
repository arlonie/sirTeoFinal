<?php
session_start();
if (isset($_POST['login'])) {
    $_SESSION['login_email'] = $_POST['email'];
}
include('dbconfig.php'); // Include Firebase config

// Fixed admin credentials
$adminEmail = "codecrafters@gmail.com";
$adminPassword = "codecrafters@2023";

function isAccountLocked($user) {
    if (isset($user['login_attempts']) && $user['login_attempts'] >= 5) {
        $lockout_time = strtotime($user['last_attempt_time']) + (15 * 60); // 15 minutes lockout
        if (time() < $lockout_time) {
            return true;
        }
    }
    return false;
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for admin credentials first
    if ($email === $adminEmail && $password === $adminPassword) {
        // Set session for admin
        $_SESSION['role'] = 'admin';
        $_SESSION['email'] = $adminEmail;
        $_SESSION['firstname'] = 'Admin';
        $_SESSION['lastname'] = 'User';

        // Redirect to admin dashboard
        header("Location: welcome-admin.php");
        exit;
    }

    // Firebase Reference for regular users
    $referenceTable = "users";
    $snapshot = $database->getReference($referenceTable)->getValue();

    if ($snapshot) {
        $userFound = false;

        foreach ($snapshot as $key => $user) {
            if ($user['email'] === $email) {
                $userFound = true;

                if (isAccountLocked($user)) {
                    $_SESSION['error'] = "Account is locked. Please try again later or reset your password.";
                    header("Location: login.php");
                    exit;
                }

                if (password_verify($password, $user['password'])) {
                    // Reset login attempts on successful login
                    $database->getReference("$referenceTable/$key")->update([
                        'login_attempts' => 0,
                        'last_attempt_time' => null
                    ]);

                    $_SESSION['user_id'] = $key;
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['firstname'] = $user['firstname'];
                    $_SESSION['lastname'] = $user['lastname'];
                    $_SESSION['email_verified'] = $user['email_verified'] ?? false;

                    // Redirect based on email verification status
                    if ($_SESSION['email_verified']) {
                        header("Location: welcome-user.php");
                    } else {
                        $_SESSION['status'] = "Please verify your email before accessing the welcome page.";
                        header("Location: verify_otp.php?email=" . urlencode($email));
                    }
                    exit;
                } else {
                    // Increment login attempts
                    $login_attempts = isset($user['login_attempts']) ? $user['login_attempts'] + 1 : 1;
                    $database->getReference("$referenceTable/$key")->update([
                        'login_attempts' => $login_attempts,
                        'last_attempt_time' => date("Y-m-d H:i:s")
                    ]);

                    $_SESSION['error'] = "Invalid password. Attempts remaining: " . (5 - $login_attempts);
                }
                break;
            }
        }

        if (!$userFound) {
            $_SESSION['error'] = "No account found with that email.";
        }
    } else {
        $_SESSION['error'] = "Error fetching users from the database.";
    }

    if (isset($_SESSION['error'])) {
        header("Location: login.php");
        exit;
    }
}

// Clear login email from session when successfully logged in
if (isset($_SESSION['email'])) {
    unset($_SESSION['login_email']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
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

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 12px;
            cursor: pointer;
        }

        .position-relative {
            position: relative;
        }

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
            <h2>Login</h2>

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
                    <input type="email" class="form-control" name="email" placeholder="Enter Your Email" required value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>">
                </div>
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Your Password" required>
                    <span class="eye-icon" id="toggle-password" onclick="togglePasswordVisibility()"><i class="fas fa-eye-slash"></i></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="forgot-password.php" class="link">Forgot Password?</a>
                </div>

                <button class="btn btn-green w-100" type="submit" name="login">Login</button>
            </form>
            <footer>
                <p>Don't have an account? <a href="register.php" class="link">Register</a></p>
            </footer>

            <a href="index.php" class="btn btn-back">Back to Home</a>
        </div>
    </section>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('toggle-password').querySelector('i');

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
</html>

