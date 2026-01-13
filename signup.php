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
   CONNECT TO DATABASE
================================ */
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ===============================
   CREATE USERS TABLE IF NOT EXISTS
================================ */
$table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table);

/* ===============================
   SIGNUP LOGIC
================================ */
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "❌ Email already exists!";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO users (email, password) VALUES ('$email', '$hashedPassword')");
            if ($insert) {
                $message = "✅ Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $message = "❌ Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up | Glow Beauty</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

  <style>
    * {margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif;}

    body {
      height:100vh;
      background: linear-gradient(to right, rgba(194,24,91,0.7), rgba(0,0,0,0.5)),
      url("https://images.unsplash.com/photo-1522337360788-8b13dee7a37e") center/cover;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .signup-container {
      background:#ffb5ee;
      width:400px;
      padding:40px;
      border-radius:40px;
      box-shadow:0 30px 40px rgba(255,255,255,0.2);
      animation: fadeIn 1s ease;
    }

    h2 {
      text-align:center;
      margin-bottom:25px;
      font-family:'Playfair Display', serif;
      color:#c2185b;
    }

    .input-group {margin-bottom:18px;}
    .input-group input {
      width:100%;
      padding:12px 15px;
      border-radius:30px;
      border:1px solid #ddd;
      outline:none;
    }

    .signup-btn {
      width:100%;
      padding:12px;
      background:#c2185b;
      color:#fff;
      border:none;
      border-radius:30px;
      font-size:16px;
      cursor:pointer;
    }

    .signup-btn:hover {background:#a3154a;}

    .login-link {text-align:center; margin-top:15px; font-size:14px;}
    .login-link a {color:#c2185b; text-decoration:none;}

    .message {text-align:center; color:red; margin-bottom:10px; font-size:14px;}

    @keyframes fadeIn {from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);}}
  </style>
</head>

<body>

  <div class="signup-container">
    <h2>Create Account</h2>

    <?php if ($message): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <input type="email" name="email" placeholder="Email address" required>
      </div>

      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="input-group">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </div>

      <button type="submit" class="signup-btn">Sign Up</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>

</body>
</html>
