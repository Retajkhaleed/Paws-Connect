<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["UserID"])) {
    header("Location: ../login.html");
    exit;
}

// Fetch current user info
$userID = $_SESSION["UserID"];
$stmt = mysqli_prepare($conn, "SELECT Username, PhoneNumber, ProfilePhotoURL FROM Users LEFT JOIN Profile ON Users.UserID = Profile.UserID WHERE Users.UserID=?");
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Default profile photo
$profilePhoto = $user["ProfilePhotoURL"] ?? "images/default-profile.png";

// Error message
$message = '';
if (isset($_GET['error']) && $_GET['error'] == 'duplicate') {
    $message = '<p class="message error">Username or Phone Number already in use!</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paws Connect | Edit Account</title>
    <link href="StyleCSS.css" rel="stylesheet">
    <style>
        .message {
            padding: 10px;
            margin: 20px auto;
            border-radius: 5px;
            text-align: center;
            max-width: 500px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-container {
            padding-top: 120px; 
            padding-bottom: 80px;
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 40px; 
        }

        .edit-container h1 {
            font-size: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .photo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
        }

        .photo-section img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .edit-container .form-group {
             margin-bottom: 25px;
        }
        
        .edit-container .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .edit-container .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding-top: 20px;
        }
        
        .cancel-btn {
            background-color: #e0e0e0 !important;
            color: #504e76 !important;
            border: 1px solid #ccc;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-submit-btn {
            background: #f1642e;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="../images/PawLogo.png" alt="Paws Connect Logo" class="logo-image"> 
    <span class="logo-text">Paws Connect</span>
  </div>
  <nav>
    <ul class="nav menu"> 
      <li><a href="../home_main.php">Home</a></li>
      <li><a href="../adoptable-cats.html">Adopt Cats</a></li>
      <li><a href="../lost-cats.html">Lost Cats</a></li>
      <li><a href="../sick.html">Sick Cats</a></li>
      <li class="dropdown">
        <a href="#"> Account <span class="arrow">▲</span> </a>
        <ul class="dropdown-content">
          <li><a href="account.php">Profile</a></li>
          <li><a href="../my-announcements.html">My Announcements</a></li>
          <li><a href="../saved-announcements.html">Saved Announcements</a></li>
        </ul>
      </li>
      <li><a href="../add.html" class="btn">Add Announcement</a></li>
    </ul>
  </nav>
</header>

<main class="page-background">
    <?php echo $message; ?>
    <div class="edit-container">
        <h1>Edit profile</h1>
        
        <form method="POST" action="account_logic.php" enctype="multipart/form-data">
            
            <div class="photo-section">
                <img src="../<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Picture">
                <div>
                    <label>Change Photo</label>
                    <input type="file" name="profilePhoto" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user["Username"]); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user["PhoneNumber"]); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" placeholder="••••••••">
            </div>
            
            <div class="edit-actions">
                <a href="account.php" class="cancel-btn">Cancel</a>
                <button type="submit" class="auth-submit-btn">Save Changes</button>
            </div>
        </form>
    </div>
</main>

<footer>
  <div class="footer-content">
    <div class="footer-left"><span>Paws Connect 2025 ©</span></div>
    <div class="footer-right">
      <span>Connect With Us</span>
      <div class="social">
         <img src="../images/instaLogo.png" alt="Instagram">
         <img src="../images/XLogo.png" alt="X App">
         <img src="../images/FacebookLogo.png" alt="Facebook">
      </div>
    </div>
  </div>
</footer>

</body>
</html>