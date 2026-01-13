<?php
session_start();

/* ===============================
   DATABASE CONFIG
================================ */
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dazzel";

/* ===============================
   CREATE DATABASE
================================ */
$conn = mysqli_connect($host, $user, $pass);
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $dbname");

/* ===============================
   CONNECT TO DATABASE
================================ */
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ===============================
   CREATE USERS TABLE
================================ */
$table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table);

/* ===============================
   LOGIN LOGIC
================================ */
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) == 1) {

        $user = mysqli_fetch_assoc($check);

        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            header("Location: afterlogin.php");
            exit();
        } else {
            $message = "❌ Invalid Password";
        }

    } else {
        $message = "❌ User Not Found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Dazzel beauty parlour</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(to right, rgba(194,24,91,0.7), rgba(0,0,0,0.5)),
      url("https://images.unsplash.com/photo-1522337360788-8b13dee7a37e") center/cover;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px; /* ✅ MOBILE FIX */
    }

    .login-container {
      background: #ffb5ee;
      width: 400px;
      max-width: 100%; /* ✅ MOBILE FIX */
      padding: 40px;
      border-radius: 40px;
      box-shadow: 0 30px 40px rgba(255, 255, 255, 0.2);
      animation: fadeIn 1s ease;
    }

    h2 {
      text-align: center;
      margin-bottom: 15px;
      font-family: 'Playfair Display', serif;
      color: #c2185b;
    }

    h5 {
      text-align: center;
      color: #000;
      font-weight: 400;
    }

    .input-group {
      margin-bottom: 18px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px;
      border-radius: 30px;
      border: 1px solid #ddd;
      outline: none;
    }

    .forgot {
      text-align: right;
      margin-bottom: 15px;
    }

    .forgot a {
      text-decoration: none;
      font-size: 13px;
      color: #c2185b;
    }

    .login-btn,
    .back-btn {
      width: 100%;
      padding: 12px;
      background: #c2185b;
      color: #fff;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      cursor: pointer;
      margin-bottom: 10px; /* ✅ MOBILE FIX */
    }

    .login-btn:hover,
    .back-btn:hover {
      background: #a3154a;
    }

    .signup {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
      color: #000;
    }

    .signup a {
      color: #c2185b;
      text-decoration: none;
    }

    .error {
      text-align: center;
      color: red;
      margin-bottom: 10px;
      font-size: 14px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===============================
       ✅ MOBILE RESPONSIVE ONLY
       Desktop remains unchanged
    ================================ */
    @media (max-width: 480px) {
      .login-container {
        padding: 25px;
        border-radius: 25px;
      }

      h2 {
        font-size: 24px;
      }

      h5 {
        font-size: 13px;
      }

      .login-btn,
      .back-btn {
        font-size: 15px;
      }
    }
  </style>
</head>

<body>

  <div class="login-container">
    <h2>Welcome</h2>
    <h5>Join us today and indulge in the ultimate beauty experience!</h5><br>

    <?php if ($message): ?>
      <div class="error"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <input type="email" name="email" placeholder="Email address" required>
      </div>

      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="forgot">
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="login-btn">Login</button>
      <button type="button" onclick="window.location.href='index.html'" class="back-btn">Back</button>
      <button type="button" onclick="window.location.href='admin_dashboard.php'" class="back-btn">Login as admin?</button>
    </form>

    <div class="signup">
      Don’t have an account?
      <a href="signup.php">Sign up</a>
      <br><br>
      <h5>It's free and only takes a minute.</h5>
      <h5>Kindly login to contact us.</h5>
    </div>
  </div>

</body>
</html>
