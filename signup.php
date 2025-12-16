<?php
session_start();
require "db_connect.php";

$error_message = '';
$username = '';
$phone = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if (empty($username) || empty($phone) || empty($password) || empty($confirm)) {
        $error_message = "Please fill in all fields!";
    } elseif ($password !== $confirm) {
        $error_message = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE Username=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error_message = "Username already taken!";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE PhoneNumber=?");
            mysqli_stmt_bind_param($stmt, "s", $phone);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error_message = "Phone number already registered!";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "INSERT INTO Users (Username, Password, PhoneNumber) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sss", $username, $hashed, $phone);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION["UserID"] = mysqli_insert_id($conn);
                    $_SESSION["Username"] = $username;
                    $_SESSION["Phone"] = $phone;

                    header("Location: home_main.php");
                    exit;
                } else {
                    $error_message = "An error occurred. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Connect | Sign Up</title>
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
            <li><a href="home_main.php">Home</a></li>
            <li><a href="adoptable-cats.php">Adopt Cats</a></li>
            <li><a href="lost-cats.php">Lost Cats</a></li>
            <li><a href="sick.php">Sick Cats</a></li>
            <li><a href="login.php">Log In</a></li>
        </ul>
    </nav>
</header>

<main class="page-background"> 
    <div class="form-card">
        <form class="signup-form" method="POST" action="signup.php">
            <h2>Join Our Community!</h2> 
            
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
                    placeholder="Choose a username" 
                    value="<?php echo htmlspecialchars($username); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    placeholder="05XXXXXXXX" 
                    value="<?php echo htmlspecialchars($phone); ?>"
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
                    minlength="8" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm" 
                    name="confirm"
                    placeholder="Re-enter password" 
                    required
                >
            </div>
            
            <button type="submit" class="auth-submit-btn">Sign Up</button>
            
            <p class="auth-footer">
                Already have an account? <a href="login.php">Log in</a>
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
const confirmField = document.getElementById("confirm");
const catImg = document.getElementById("cat-img");

if(passwordField && catImg){
    passwordField.addEventListener("focus", () => { 
        catImg.src = "img/closed-eyes.jpg"; 
    });
    passwordField.addEventListener("blur", () => { 
        catImg.src = "img/open-eyes.jpg"; 
    });
}

confirmField.addEventListener("input", function() {
    if (confirmField.value !== passwordField.value) {
        confirmField.setCustomValidity("Passwords do not match.");
    } else {
        confirmField.setCustomValidity("");
    }
});

passwordField.addEventListener("input", function() {
    if (confirmField.value !== "" && confirmField.value !== passwordField.value) {
        confirmField.setCustomValidity("Passwords do not match.");
    } else {
        confirmField.setCustomValidity("");
    }
});
</script>

</body>
</html>

