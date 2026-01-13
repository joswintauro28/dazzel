<?php
session_start();

// Include database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dazzel";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get logged-in user email
if (isset($_SESSION['email'])) {
    $user_email = $_SESSION['email'];
} else {
    $user_email = null;
}

// DELETE ACCOUNT
if (isset($_GET['delete']) && $_GET['delete'] == 'true' && $user_email) {
    // Delete the user from database
    mysqli_query($conn, "DELETE FROM users WHERE email='$user_email'");
    
    // Destroy session after deletion
    session_unset();
    session_destroy();
    
    // Redirect to login with a message
    header("Location: login.php?deleted=1");
    exit();
}

// LOGOUT (default)
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
