<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION["UserID"])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["UserID"];
$saved_announcements = [];
$error_message = null;

$sql = "SELECT 
            a.AnnouncementID, a.Title, a.Location, a.Type, a.DateCreated, a.Status, a.PhotoURL
        FROM Announcement a
        JOIN SavedAnnouncement s ON a.AnnouncementID = s.AnnouncementID
        WHERE s.UserID = ?
        ORDER BY s.DateSaved DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userID);
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $saved_announcements[] = $row;
        }
    } else {
        $error_message = "Database execution failed: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error_message = "SQL Prepare Failed: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Saved Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="StyleCSS.css" /> 
  </head>
  <body>
    <header>
      <div class="logo">
        <img src="img/PawLogo.png" alt="Paws Connect Logo" class="logo-image" />
        <span class="logo-text">Paws Connect</span>
      </div>

      <nav>
        <ul class="nav">
          <li><a href="home_main.php" class="home-btn">Home</a></li> 
          <li><a href="adoptable-cats.php">Adopt Cats</a></li>
          <li><a href="lost-cats.php">Lost Cats</a></li>
          <li><a href="sick.php">Sick Cats</a></li>

          <li class="dropdown">
            <a href="#"> Account <span class="arrow">▲</span> </a>
            <ul class="dropdown-content">
              <li><a href="account.php">Profile</a></li>
              <li><a href="my-announcements.php">My Announcements</a></li>
              <li><a href="saved-announcements.php">Saved Announcements</a></li>
            </ul>
          </li>
          <li><a href="add.html" class="btn">Add Announcement</a></li>
        </ul>
      </nav>
    </header>
    <h1 class="page-title">Saved Announcements</h1>

    <main>
      <section class="simple-page-container">
        <div id="announcement-list-container">
            <?php if ($error_message): ?>
                <p class="no-announcements" style="color: red;">Database Error: <?php echo $error_message; ?></p>
            <?php elseif (empty($saved_announcements)): ?>
                <p class="no-announcements">You have not saved any announcements yet.</p>
            <?php else: ?>
                <?php foreach ($saved_announcements as $announcement): ?>
                    <?php 
                        $photoURL = $announcement['PhotoURL'] ? 
                                    $announcement['PhotoURL'] : 
                                    'img/default-cat.png';
                        if (!file_exists($photoURL)) $photoURL = 'img/PawLogo.png'; 
                    ?>
                    <a href="view-announcement.php?id=<?php echo $announcement['AnnouncementID']; ?>" 
                       class="list-announcement-item"
                       data-city="<?php echo strtolower($announcement['Location']); ?>">

                        <img src="<?php echo htmlspecialchars($photoURL); ?>" alt="<?php echo htmlspecialchars($announcement['Title']); ?>" />
                        
                        <div class="list-details-content">
                            <h3><?php echo htmlspecialchars($announcement['Title']); ?> (<?php echo $announcement['Type']; ?>)</h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($announcement['Location']); ?></p>
                            <p><strong>Date Posted:</strong> <?php echo date('Y-m-d', strtotime($announcement['DateCreated'])); ?></p>
                            <p><strong>Status:</strong> <span class="status-badge status-<?php echo $announcement['Status']; ?>"><?php echo $announcement['Status']; ?></span></p>

                            <form method="POST" action="toggle-saved-announcement.php" onclick="event.stopPropagation();">
                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['AnnouncementID']; ?>">
                                <input type="hidden" name="action" value="unsave">
                                <button type="submit" class="btn mark-btn btn-danger-unsave">
                                    Unsave Announcement
                                </button>
                            </form>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </section>
    </main>

     <footer>
    <div class="footer-content">
       Paws Connect 2025 ©
    </div>
  </footer>
  
    <?php $conn->close(); ?>
  </body>
</html>
