<?php
session_start();

/* -------------------------------
   DATABASE CONNECTION
-------------------------------- */
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dazzel";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* -------------------------------
   LOGIN CHECK
-------------------------------- */
if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = "guest@example.com";
}

$user_email = $_SESSION['email'];
$msg = "";

/* -------------------------------
   CANCEL BOOKING
-------------------------------- */
if (isset($_GET['cancel_id'])) {
    $booking_id = intval($_GET['cancel_id']);

    $stmt = $conn->prepare("
        DELETE FROM bookings 
        WHERE id = ? AND user_email = ?
    ");
    $stmt->bind_param("is", $booking_id, $user_email);
    $stmt->execute();

    $msg = "❌ Booking cancelled successfully!";
}

/* -------------------------------
   FETCH BOOKINGS
-------------------------------- */
$stmt = $conn->prepare("
    SELECT id, customer_name, service_name, booking_date, booking_time
    FROM bookings 
    WHERE user_email = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings | Dazzel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">

<style>
body{
  margin:0;
  font-family:'Poppins',sans-serif;
  background:
  linear-gradient(to right, rgba(194,24,91,0.75), rgba(0,0,0,0.45)),
  url('https://images.unsplash.com/photo-1522337360788-8b13dee7a37e') center/cover;
  min-height:100vh;
  padding:40px;
}

.container{
  max-width:900px;
  margin:auto;
  background:#fff;
  padding:30px;
  border-radius:30px;
  box-shadow:0 30px 60px rgba(0,0,0,0.35);
}

h2{
  text-align:center;
  font-family:'Playfair Display',serif;
  color:#c2185b;
}

.msg{
  text-align:center;
  color:red;
  font-weight:600;
  margin-bottom:15px;
}

.table-wrap{
  width:100%;
  overflow-x:auto;
}

table{
  width:100%;
  border-collapse:collapse;
  margin-top:20px;
  min-width:650px;
}

th,td{
  padding:12px;
  text-align:center;
  border-bottom:1px solid #ddd;
}

th{
  background:#c2185b;
  color:#fff;
}

.cancel-btn{
  padding:6px 14px;
  background:#c2185b;
  color:#fff;
  border-radius:20px;
  text-decoration:none;
  font-size:14px;
  white-space:nowrap;
}

.cancel-btn:hover{
  background:#a3154a;
}

.empty{
  text-align:center;
  padding:20px;
  font-weight:600;
}

.back-btn{
  margin-top:20px;
  display:inline-block;
}

/* =============================
   RESPONSIVE FIX (MOBILE/LAPTOP)
   ============================= */
@media (max-width: 768px){
  body{
    padding:15px;
  }

  .container{
    padding:20px;
    border-radius:18px;
  }

  h2{
    font-size:22px;
  }

  table{
    min-width:600px;
  }

  .back-btn{
    display:block;
    width:92%;
    text-align:center;
    margin-top:20px;
  }
}

@media (max-width: 480px){
  table{
    min-width:550px;
  }
}
</style>
</head>

<body>

<div class="container">
<h2>My Bookings</h2>

<?php if($msg!="") echo "<p class='msg'>$msg</p>"; ?>

<div class="table-wrap">
<table>
<tr>
  <th>Name</th>
  <th>Service</th>
  <th>Date</th>
  <th>Time</th>
  <th>Action</th>
</tr>

<?php if ($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['customer_name']) ?></td>
  <td><?= htmlspecialchars($row['service_name']) ?></td>
  <td><?= $row['booking_date'] ?></td>
  <td><?= $row['booking_time'] ?></td>
  <td>
    <a class="cancel-btn"
       href="?cancel_id=<?= $row['id'] ?>"
       onclick="return confirm('Are you sure you want to cancel this booking?');">
       Cancel
    </a>
  </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
  <td colspan="5" class="empty">No bookings found</td>
</tr>
<?php endif; ?>
</table>
</div>

<a href="booking.php" class="cancel-btn back-btn">Back</a>
</div>

</body>
</html>
