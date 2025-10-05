<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();            // Start the session
session_unset();            // Unset all session variables
session_destroy();          // Destroy the session

// Redirect to login page
//echo "You have been logged out.";
header("Location: login.html");
exit();
?>
