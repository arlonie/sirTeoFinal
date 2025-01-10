<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page with a logout success message
$_SESSION['status'] = "You have been logged out successfully.";
header("Location: index.php");
exit();
?>
