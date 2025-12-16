<?php
session_start();
include 'db_connect.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: adoptable-cats.php");
    exit();
}

$announcement_id = $_GET['id'];
$is_owner = false;
$is_logged_in = isset($_SESSION['UserID']);
$is_saved = false;

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

if ($is_logged_in) {
    $stmt_saved = $conn->prepare("SELECT SavedID FROM SavedAnnouncement WHERE UserID = ? AND AnnouncementID = ?");
    $stmt_saved->bind_param("ii", $_SESSION['UserID'], $announcement_id);
    $stmt_saved->execute();
    $result_saved = $stmt_saved->get_result();
    if ($result_saved->num_rows > 0) {
        $is_saved = true;
    }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    if (!isset($_SESSION['UserID'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in']);
        exit();
    }

    $comment_text = trim($_POST['comment_text']);
    $user_id = $_SESSION['UserID'];

    $stmt_insert = $conn->prepare("INSERT INTO Comment (AnnouncementID, UserID, CommentText, DateCommented) VALUES (?, ?, ?, NOW())");
    $stmt_insert->bind_param("iis", $announcement_id, $user_id, $comment_text);
    if ($stmt_insert->execute()) {
        $stmt_user = $conn->prepare("SELECT Username FROM Users WHERE UserID=?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $res_user = $stmt_user->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'username' => $res_user['Username'], 'comment' => htmlspecialchars($comment_text), 'date' => date("Y-m-d H:i")]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to post comment']);
    }
    exit();
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
      <li><a href="home_main.php">Home</a></li>
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
      //else $first_photo = '/Paws-Connect/' . $first_photo;
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

      <div class="announcement-actions">
        <a href="tel:<?php echo htmlspecialchars($announcement['ContactPhone']); ?>" class="btn contact-btn">Contact: <?php echo htmlspecialchars($announcement['ContactPhone']); ?></a>      
        
        <?php if($is_owner): ?>
          <?php elseif($is_logged_in): ?>
            <button id="saveButton" data-announcement-id="<?php echo $announcement_id; ?>" 
                    class="btn mark-btn <?php echo $is_saved ? 'saved' : 'unsaved'; ?>">
              <?php echo $is_saved ? 'Unsave Announcement' : 'Save Announcement'; ?>
            </button>
          <?php endif; ?>
        </div>
    </div>
  </div>

  <section class="comments-section">
    <h3>Comments</h3>
    <div id="commentsList">
      <?php 
      $stmt_comments = $conn->prepare("SELECT c.*, u.Username FROM Comment c JOIN Users u ON c.UserID=u.UserID WHERE c.AnnouncementID=? ORDER BY c.DateCommented DESC");
      $stmt_comments->bind_param("i",$announcement_id);
      $stmt_comments->execute();
      $res_comments = $stmt_comments->get_result();
      if($res_comments->num_rows>0){
        while($c=$res_comments->fetch_assoc()){
          echo "<div style='border-bottom:1px dotted #a3b565;padding:10px 0;'><strong>".htmlspecialchars($c['Username']).":</strong> ".htmlspecialchars($c['CommentText'])."<br><small style='color:#9aa58d;'>".date("Y-m-d H:i",strtotime($c['DateCommented']))."</small></div>";
        }
      } else { echo "<p>No comments yet.</p>"; }
      ?>
    </div>

    <?php if($is_logged_in): ?>
      <form class="comment-form" id="commentForm">
        <textarea name="comment_text" id="newComment" placeholder="Write a comment..." required></textarea>
        <button type="submit" class="btn">Post Comment</button>
      </form>
      <?php else: ?>
        <p style="text-align:center;color:#f1642e;font-weight:bold;">
          Please <a href="/Paws-Connect/login.html" style="color:#f1642e;">log in</a> to post a comment.</p>
      <?php endif; ?>

  </section>
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

<script src="view-announcement.js"></script>

</body>
</html>


