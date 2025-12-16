<?php
session_start();
require "db_connect.php";

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT UserID, Username, Password, PhoneNumber FROM Users WHERE Username=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["Password"])) {
            $_SESSION["UserID"] = $user["UserID"];
            $_SESSION["Username"] = $user["Username"];
            $_SESSION["Phone"] = $user["PhoneNumber"];

            header("Location: home_main.php");
            exit;
        } else {
            $error_message = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paws Connect | Login</title>
  <link href="StyleCSS.css" rel="stylesheet">
</head>
<body>

<header>
  <div class="logo">
    <img src="img/PawLogo.png" alt="Logo" class="logo-image">
    <span class="logo-text">Paws Connect</span>
  </div>
  <nav>
    <ul class="nav">
        <li><a href="home_main.php" class="home-btn">Home</a></li> 
      <li><a href="adoptable-cats.php">Adopt Cats</a></li>
      <li><a href="lost-cats.php">Lost Cats</a></li>
      <li><a href="sick.php">Sick Cats</a></li>
    </ul>
  </nav>
</header>

<main class="page-background">
  <div class="form-card">
    <form class="login-form" method="POST" action="login.php">
      <h2>Welcome Back!</h2>

      <div class="cat-container">
        <img id="cat-img" src="img/open-eyes.jpg" alt="Cat" class="auth-cat-img">
      </div>

      <?php if (!empty($error_message)): ?>
        <p style="color: #f1642e; font-weight: bold; text-align: center; margin-bottom: 15px;">
          <?php echo htmlspecialchars($error_message); ?>
        </p>
      <?php endif; ?>

      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Enter your username"
          value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
          required
        >
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Password"
          required
        >
      </div>

      <button type="submit" class="auth-submit-btn">Login</button>

      <p class="auth-footer">
        Don't have an account? <a href="signup.php">Create one</a>
      </p>
    </form>
  </div>
</main>

<footer>
  <div class="footer-content">
    <div class="footer-left"><span>Paws Connect 2025 ©</span></div>
    <div class="footer-right">
      <span>Connect With Us</span>
      <div class="social">
         <img src="img/instaLogo.png" alt="IG">
         <img src="img/XLogo.png" alt="X">
         <img src="img/FacebookLogo.png" alt="FB">
      </div>
    </div>
  </div>
</footer>

<script>
const passwordField = document.getElementById("password");
const catImg = document.getElementById("cat-img");

if(passwordField && catImg){
    passwordField.addEventListener("focus", () => { 
        catImg.src = "img/closed-eyes.jpg"; 
    });
    passwordField.addEventListener("blur", () => { 
        catImg.src = "img/open-eyes.jpg"; 
    });
}
</script>

</body>
</html>


