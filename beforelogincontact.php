<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dazzel";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create table for storing contact messages if it doesn't exist
$table_query = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sent_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table_query);

$message_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_email = filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject']);
    $body = htmlspecialchars($_POST['message']);
    $sent_by = $_SESSION['email']; // Logged-in user

    if ($customer_email && $subject && $body) {
        // Insert message into database
        $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (customer_email, subject, message, sent_by) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $customer_email, $subject, $body, $sent_by);
        $inserted = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Send email
        $headers = "From: $sent_by";
        $mail_sent = mail($customer_email, $subject, $body, $headers);

        if ($inserted && $mail_sent) {
            $message_status = "✅ Message stored in database and email sent successfully to $customer_email!";
        } elseif ($inserted) {
            $message_status = " Message stored in database";
        } else {
            $message_status = "❌ Failed to store message in database.";
        }
    } else {
        $message_status = "❌ Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Customer | Dazzel Beauty Parlour</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background: #ffd7d7; color: #333; }

header {
    position: fixed; top:0; width:100%; padding:20px 60px; display:flex; justify-content:space-between; align-items:center;
    background: rgba(0,0,0,0.9); color:#fff; z-index:1000; box-shadow:0 5px 20px rgba(0,0,0,0.05);
}
header h1 { font-family:'Playfair Display', serif; }
nav a { margin-left:30px; text-decoration:none; color:#fff; font-weight:500; }
nav a:hover { color:#da0303; }

h1{
     color: #f0d8d8;
}

.container {
    max-width: 600px;
    margin: 120px auto 50px;
    background: #fbcdcd;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 30px hsla(0, 0%, 0%, 0.10);
}

h2 { text-align:center; color:; margin-bottom: 30px; font-family:'Playfair Display', serif; }

.input-group { margin-bottom: 18px; }
.input-group input, .input-group textarea {
    width:100%; padding:12px 15px; border-radius:15px; border:1px solid #ffdcdc; outline:none; font-size:14px;
}
textarea { resize:none; height:120px; }

.send-btn {
    width:100%; padding:14px; border:none; border-radius:25px; background:#c2185b; color:#fff; font-size:16px; cursor:pointer;
}
.send-btn:hover { background:#a3154a; }

.message { text-align:center; margin-bottom:15px; color:green; font-weight:500; }

@media(max-width:768px){ header { padding:15px 30px; } }
</style>
</head>

<body>
<header>
    <h1>Dazzel Beauty Parlour</h1>
    <nav>
        <a href="afterlogin.php">Dashboard</a>
        <a href="contact.php">Contact</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Contact Customer</h2>

    <?php if($message_status): ?>
        <div class="message"><?= $message_status ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="customer_email" placeholder="Customer Email" required>
        </div>
        <div class="input-group">
            <input type="text" name="subject" placeholder="Subject" required>
        </div>
        <div class="input-group">
            <textarea name="message" placeholder="Write your message..." required></textarea>
        </div>
        <button type="submit" class="send-btn">Send Email</button>
    </form>
</div>

</body>
</html>
