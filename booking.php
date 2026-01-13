<?php
session_start();

/* -------------------------------
   1. DATABASE CONNECTION
-------------------------------- */
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dazzel";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* -------------------------------
   2. CREATE DATABASE
-------------------------------- */
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

/* -------------------------------
   3. CREATE BOOKINGS TABLE
-------------------------------- */
$conn->query("
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time VARCHAR(20) NOT NULL,
    address VARCHAR(255),
    location VARCHAR(100),
    pincode VARCHAR(10),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

/* -------------------------------
   4. SESSION EMAIL
-------------------------------- */
if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = "guest@example.com"; 
}

$msg = "";

/* -------------------------------
   5. HANDLE BOOKING
-------------------------------- */
if (isset($_POST['book'])) {

    $name            = $_POST['name'];
    $phone           = $_POST['phone'];
    $email           = $_POST['email'];
    $service         = $_POST['service'];
    $date            = $_POST['date'];
    $time            = $_POST['time'];
    $address         = $_POST['address'];
    $location        = $_POST['location'];
    $pincode         = $_POST['pincode'];
    $additional_info = $_POST['additional_info'];

    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (customer_name, contact_number, user_email, service_name, booking_date, booking_time, address, location, pincode, additional_info)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssss",
        $name,
        $phone,
        $email,
        $service,
        $date,
        $time,
        $address,
        $location,
        $pincode,
        $additional_info
    );

    $stmt->execute();
    $msg = "✅ Booking Successful!";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment | Dazzel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:'Poppins',sans-serif;
  background:
  linear-gradient(to right, rgba(194,24,91,0.75), rgba(0,0,0,0.45)),
  url('https://images.unsplash.com/photo-1522337360788-8b13dee7a37e') center/cover;
  height:150vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
.box{
  width:500px;
  background:#fff;
  padding:35px;
  border-radius:30px;
  box-shadow:0 30px 60px rgba(0,0,0,0.35);
  animation:fade 1s ease;
}
h2{
  text-align:center;
  font-family:'Playfair Display',serif;
  color:#c2185b;
}
input,select,textarea,button{
  width:100%;
  padding:12px;
  margin-top:14px;
  border-radius:25px;
  border:1px solid #ddd;
  font-size:14px;
}
textarea{
  resize:none;
}
button{
  background:#c2185b;
  color:#fff;
  border:none;
  font-size:16px;
  cursor:pointer;
}
button:hover{background:#a3154a;}
.msg{
  text-align:center;
  color:green;
  margin-top:10px;
  font-weight:600;
}
@keyframes fade{
  from{opacity:0;transform:translateY(25px);}
  to{opacity:1;transform:translateY(0);}
}
</style>
</head>

<body>

<div class="box">
<h2>Book Your Service</h2>

<?php if($msg!="") echo "<p class='msg'>$msg</p>"; ?>

<form method="POST">

<input type="text" name="name" placeholder="Your Full Name" required>
<input type="tel" name="phone" placeholder="Contact Number" pattern="[0-9]{10}" required>
<input type="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>

<select name="service" required>
  <option value="">Select Service</option>
  <option>Hair Cut</option>
  <option>Facial</option>
  <option>Bridal Makeup</option>
  <option>Manicure</option>
  <option>Pedicure</option>
  <option>Hair Spa</option>
  <option>Waxing</option>
  <option>Threading</option>
  <option>Spa Treatment</option>
  <option>other</option>


</select>



<input type="date" name="date" required>
<input type="time" name="time" required>

<!-- New Fields for In-house Appointment -->
<input type="text" name="address" placeholder="Full Address for Service" required>
<input type="text" name="location" placeholder="City / Location" required>
<input type="text" name="pincode" placeholder="Pincode" pattern="[0-9]{6}" required>
<textarea name="additional_info" placeholder="Additional Information (optional)" rows="3"></textarea>

<button type="submit" name="book">Confirm Booking</button>
<button type="button" onclick="window.location.href='cancelbooking.php'">Cancel Bookings</button>
<button type="button" onclick="window.location.href='afterlogin.php'">Back to Dashboard</button>

</form>
</div>

</body>
</html>
