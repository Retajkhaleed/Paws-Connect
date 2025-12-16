<?php
session_start();
include 'db_connect.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: adoptable-cats.php");
    exit();
}

$announcement_id = $_GET['id'];
$is_owner = false; 

$sql = "SELECT a.*, u.Username 
        FROM Announcement a
        JOIN Users u ON a.UserID = u.UserID
        WHERE a.AnnouncementID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Announcement not found.");
}

$announcement = $result->fetch_assoc();
$announcement_type = $announcement['Type'];
$announcement_user_id = $announcement['UserID'];

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $announcement_user_id) {
    $is_owner = true;
}

$specific_data = [];
$specific_fields = "";
$specific_table = "";

if ($announcement_type == 'LostCat') {
    $specific_fields = "DateLost, LastSeenLocation, RewardOffered, DistinctFeatures";
    $specific_table = "LostCat";
} elseif ($announcement_type == 'CatAdoption') {
    $specific_fields = "Age, Gender, Vaccinated, Neutered, AdoptionRequirements, AdoptionStatus";
    $specific_table = "CatAdoption";
} elseif ($announcement_type == 'SickCat') {
    $specific_fields = "Symptoms, Urgency, DateNoticed, FoundLocation, Needs";
    $specific_table = "SickCat";
}

if ($specific_table) {
    $sql_specific = "SELECT $specific_fields FROM $specific_table WHERE AnnouncementID = ?";
    $stmt_specific = $conn->prepare($sql_specific);
    $stmt_specific->bind_param("i", $announcement_id);
    $stmt_specific->execute();
    $result_specific = $stmt_specific->get_result();
    $specific_data = $result_specific->fetch_assoc();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paws Connect | <?php echo htmlspecialchars($announcement['Title']); ?></title>
<link rel="stylesheet" href="StyleCSS.css">
</head>

<body>

<header>
  <div class="logo"><img src="img/logo.png" class="logo-image"><span class="logo-text">Paws Connect</span></div>
  <nav>
    <ul class="nav menu">
      <li><a href="Home.html">Home</a></li>
      <li><a href="adoptable-cats.php">Adopt Cats</a></li>
      <li><a href="lost-cats.php">Lost Cats</a></li>
      <li><a href="sick.php">Sick Cats</a></li>
      <li><a href="login.html">Log In</a></li>
      <li class="dropdown"><a href="#">Account <span class="arrow">▼</span></a>
        <ul class="dropdown-content">
          <li><a href="account.html">Profile</a></li>
          <li><a href="my-announcements.html">My Announcements</a></li>
          <li><a href="saved-announcements.html">Saved Announcements</a></li>
        </ul>
      </li>
      <li><a href="add.html" class="btn">Add Announcement</a></li>
    </ul>
  </nav>
</header>

<main class="announcement-container">
  <div class="announcement-card">
    <?php 
      $photos = explode(',', $announcement['PhotoURL']);
      $first_photo = trim($photos[0]);
      if (empty($first_photo)) $first_photo = 'img/PawLogo.png';
    ?>
    <img src="<?php echo htmlspecialchars($first_photo); ?>" class="announcement-image">
    <div class="announcement-details">
      <h2><?php echo htmlspecialchars($announcement['Title']); ?></h2>
      <p><?php echo nl2br(htmlspecialchars($announcement['Description'])); ?></p>
      <hr>
      <?php if(!empty($specific_data)): foreach($specific_data as $key => $value): 
        $label = preg_replace('/(?<!\ )[A-Z]/', ' $0', $key); 
        $label = str_replace(['Requirements', 'Status'], [' Requirements', ' Status'], $label);
      ?>
      <p><strong><?php echo htmlspecialchars($label); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
      <?php endforeach; endif; ?>
      <hr>
      <p><strong>Location:</strong> <?php echo htmlspecialchars($announcement['Location']); ?></p>
      <p><strong>Status:</strong> <span class="status-tag"><?php echo htmlspecialchars($announcement['Status']); ?></span></p>
      <p><strong>Posted By:</strong> <?php echo htmlspecialchars($announcement['Username']); ?></p>
      <p><strong>Date Posted:</strong> <?php echo date("Y-m-d", strtotime($announcement['DateCreated'])); ?></p>
      <a href="tel:<?php echo htmlspecialchars($announcement['ContactPhone']); ?>" class="btn contact-btn">Contact: <?php echo htmlspecialchars($announcement['ContactPhone']); ?></a>
      <?php if($is_owner): ?>
      <a href="edit-announcement.php?id=<?php echo $announcement_id; ?>" class="btn mark-btn">Update / Edit</a>
      <?php endif; ?>
    </div>
  </div>

 
</main>

<footer>
  <div class="footer-content">
    <div>Paws Connect 2025 ©</div>
    <div>Connect With Us
      <img src="img/instaLogo.png" style="width:22px;height:22px;">
      <img src="img/XLogo.png" style="width:22px;height:22px;">
      <img src="img/FacebookLogo.png" style="width:22px;height:22px;">
    </div>
  </div>
</footer>

</body>
</html>
