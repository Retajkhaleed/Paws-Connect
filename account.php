<?php
session_start();

require "db_connect.php";

if (!isset($_SESSION["UserID"])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["UserID"];
$stmt = mysqli_prepare($conn, "SELECT Username, PhoneNumber, ProfilePhotoURL FROM Users LEFT JOIN Profile ON Users.UserID = Profile.UserID WHERE Users.UserID=?");
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$profilePhoto = "img/Pawlogo.png"; 
if (!empty($user["ProfilePhotoURL"])) {
    $profilePhoto = $user["ProfilePhotoURL"];
}

$message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = '<p class="message success">Profile successfully updated!</p>';
} elseif (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'upload':
            $message = '<p class="message error">Error uploading profile photo. Please try again.</p>';
            break;
        case 'update':
            $message = '<p class="message error">Error updating profile. Please try again.</p>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws Connect | My Account</title>
    <link href="StyleCSS.css" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">
        <img src="img/PawLogo.png" alt="Paws Connect Logo" class="logo-image"> 
        <span class="logo-text">Paws Connect</span>
    </div>
    <nav>
        <ul class="nav menu"> 
            <li><a href="home_main.php">Home</a></li>
            <li><a href="adoptable-cats.php">Adopt Cats</a></li>
            <li><a href="lost-cats.php">Lost Cats</a></li>
            <li><a href="sick.php">Sick Cats</a></li>
            <li class="dropdown">
                <a href="#"> Account <span class="arrow">▲</span> </a>
                <ul class="dropdown-content">
                    <li><a href="my-announcements.html">My Announcements</a></li>
                    <li><a href="saved-announcements.html">Saved Announcements</a></li>
                </ul>
            </li>
            <li><a href="add.html" class="btn">Add Announcement</a></li>
        </ul>
    </nav>
</header>

<main class="account-page">
    <?php echo $message; ?>
    <section class="profile-header">
        <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Picture" class="profile-pic-large" onerror="this.src='img/Pawlogo.png'">
        <h2 class="profile-name"><?php echo htmlspecialchars($user["Username"]); ?></h2>
        <p class="profile-info"><?php echo htmlspecialchars($user["PhoneNumber"]); ?></p>
        <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
    </section>

    <section class="profile-details">
    </section>

    <section class="profile-links">
        <h3>My Activity</h3>
        <a href="my-announcements.html" class="profile-link">My Announcements</a>
        <a href="saved-announcements.html" class="profile-link">Saved Announcements</a>
        <a href="add.html" class="profile-link add">+ Add New Announcement</a>
        <br>
        <a href="logout.php" class="logout-link">Log out</a>
    </section>
</main>

<footer>
    <div class="footer-content">
        <div class="footer-left"><span>Paws Connect 2025 ©</span></div>
        <div class="footer-right">
            <span>Connect With Us</span>
            <div class="social">
                <img src="img/instaLogo.png" alt="Instagram">
                <img src="img/XLogo.png" alt="X App">
                <img src="img/FacebookLogo.png" alt="Facebook">
            </div>
        </div>
    </div>
</footer>

</body>
</html>
