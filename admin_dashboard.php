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
   ADMIN LOGIN LOGIC
-------------------------------- */
$login_error = "";
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $login_error = "Invalid username or password!";
    }
}

/* -------------------------------
   LOGOUT
-------------------------------- */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dashboard.php");
    exit();
}

/* -------------------------------
   DELETE BOOKING
-------------------------------- */
if (isset($_GET['delete_id']) && isset($_SESSION['admin'])) {
    $id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $msg = "❌ Booking deleted successfully";
}

/* -------------------------------
   FETCH BOOKINGS
-------------------------------- */
if (isset($_SESSION['admin'])) {
    $result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Dazzel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">

<style>
*{
  box-sizing:border-box;
}

body{
  margin:0;
  font-family:'Poppins',sans-serif;
  background: linear-gradient(to right, rgba(194,24,91,0.75), rgba(0,0,0,0.6)),
  url('https://images.unsplash.com/photo-1522337360788-8b13dee7a37e') center/cover;
  min-height:100vh;
  padding:40px;
}

/* CONTAINER */
.container{
  background:#fff;
  padding:30px;
  border-radius:30px;
  box-shadow:0 30px 60px rgba(0,0,0,0.35);
  max-width:1000px;
  width:100%;
  margin:auto;
}

/* HEADINGS */
h2{
  text-align:center;
  font-family:'Playfair Display',serif;
  color:#c2185b;
}

/* MESSAGE */
.msg{
  text-align:center;
  color:red;
  font-weight:600;
  margin-bottom:15px;
}

/* BACK BUTTON */
.hehe{
  border-radius:30px;
  background:rgb(168, 16, 85);
  width:100%;
  max-width:200px;
  height:40px;
  color:#fff;
  border:none;
  cursor:pointer;
  display:block;
  margin:10px auto;
}

/* LOGOUT */
.logout{
  background:#c2185b;
  color:#fff;
  padding:8px 18px;
  border-radius:20px;
  text-decoration:none;
  float:right;
}

/* TABLE WRAPPER */
.table-wrapper{
  width:100%;
  overflow-x:auto;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
  margin-top:25px;
  min-width:700px;
}

th,td{
  padding:12px;
  text-align:center;
  border-bottom:1px solid #ddd;
  white-space:nowrap;
}

th{
  background:#c2185b;
  color:#fff;
}

/* DELETE BUTTON */
.delete-btn{
  padding:6px 14px;
  background:#c2185b;
  color:#fff;
  border-radius:20px;
  text-decoration:none;
  font-size:14px;
}

.delete-btn:hover{
  background:#a3154a;
}

/* EMPTY */
.empty{
  text-align:center;
  padding:20px;
  font-weight:600;
}

/* FORMS */
form{
  max-width:400px;
  margin:auto;
}

input[type=text],
input[type=password],
input[type=submit]{
  width:100%;
  padding:12px;
  margin:8px 0;
  border-radius:10px;
  border:1px solid #ccc;
}

input[type=submit]{
  background:#c2185b;
  color:#fff;
  border:none;
  font-size:16px;
  cursor:pointer;
}

input[type=submit]:hover{
  background:#a3154a;
}

/* ERROR */
.error{
  color:red;
  text-align:center;
  margin-bottom:15px;
}

/* MOBILE FIX */
@media (max-width:768px){
  body{
    padding:15px;
  }

  .container{
    padding:20px;
    border-radius:20px;
  }

  .logout{
    float:none;
    display:block;
    text-align:center;
    margin-bottom:15px;
  }

  h2{
    font-size:22px;
  }
}
</style>
</head>

<body>

<div class="container">

<?php if(!isset($_SESSION['admin'])): ?>

  <h2>Admin Login</h2>
  <?php if($login_error!="") echo "<p class='error'>$login_error</p>"; ?>

  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
  </form>

  <br>
  <button onclick="window.location.href='login.php'" class="hehe">Back</button>

<?php else: ?>

  <a class="logout" href="?logout=1">Logout</a>
  <h2>Admin Booking Dashboard</h2>

  <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

  <div class="table-wrapper">
    <table>
      <tr>
        <th>Name</th>
        <th>Contact</th>
        <th>Email</th>
        <th>Service</th>
        <th>Date</th>
        <th>Time</th>
        <th>Action</th>
      </tr>

      <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td><?= htmlspecialchars($row['contact_number']) ?></td>
        <td><?= htmlspecialchars($row['user_email']) ?></td>
        <td><?= htmlspecialchars($row['service_name']) ?></td>
        <td><?= $row['booking_date'] ?></td>
        <td><?= $row['booking_time'] ?></td>
        <td>
          <a class="delete-btn"
             href="?delete_id=<?= $row['id'] ?>"
             onclick="return confirm('Delete this booking permanently?');">
             Delete
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
      <?php else: ?>
      <tr>
        <td colspan="7" class="empty">No bookings found</td>
      </tr>
      <?php endif; ?>
    </table>
  </div>

<?php endif; ?>

</div>
</body>
</html>
