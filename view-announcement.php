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
$specific_id_column = "";

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

<?php include 'header.php'; ?>

<main class="container announcement-container">
    <div class="announcement-card">
        <div class="image-area">
            <?php 
            $photos = explode(',', $announcement['PhotoURL']);
            $first_photo = trim($photos[0]);
            if (empty($first_photo)) {
                $first_photo = 'img/PawLogo.png'; // صورة افتراضية
            }
            ?>
            <img src="<?php echo htmlspecialchars($first_photo); ?>" alt="Cat Image" class="announcement-image" id="annImage">
            
            <?php if (!$is_owner && isset($_SESSION['user_id'])): ?>
                <button id="saveBtn" class="cta-button" style="margin-top: 15px;">
                    Save Announcement
                </button>
            <?php endif; ?>
        </div>

        <div class="announcement-details">
            <h2 id="annTitle"><?php echo htmlspecialchars($announcement['Title']); ?></h2>
            <p id="annDescription"><?php echo nl2br(htmlspecialchars($announcement['Description'])); ?></p>
            
            <hr>

            <?php if (!empty($specific_data)): ?>
                <?php foreach ($specific_data as $key => $value): ?>
                    <?php 
                    $label = preg_replace('/(?<!\ )[A-Z]/', ' $0', $key); 
                    $label = str_replace(['Requirements', 'Status'], [' Requirements', ' Status'], $label);
                    ?>
                    <p><strong><?php echo htmlspecialchars($label); ?>:</strong>
<span><?php echo htmlspecialchars($value); ?></span>
                    </p>
                <?php endforeach; ?>
                <hr>
            <?php endif; ?>

            <p><strong>Location:</strong> <span id="annRegion"><?php echo htmlspecialchars($announcement['Location']); ?></span></p>
            <p><strong>Status:</strong> <span id="annStatus" class="status-tag"><?php echo htmlspecialchars($announcement['Status']); ?></span></p>
            <p><strong>Posted By:</strong> <span id="annUser"><?php echo htmlspecialchars($announcement['Username']); ?></span></p>
            <p><strong>Date Posted:</strong> <span><?php echo date("Y-m-d", strtotime($announcement['DateCreated'])); ?></span></p>
            
            <div class="announcement-actions">
                <a href="tel:<?php echo htmlspecialchars($announcement['ContactPhone']); ?>" class="btn contact-btn" id="contactBtn">
                    Contact: <?php echo htmlspecialchars($announcement['ContactPhone']); ?>
                </a>

                <?php if ($is_owner): ?>
                    <a href="edit-announcement.php?id=<?php echo $announcement_id; ?>" class="btn mark-btn">
                        Update / Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <section class="comments-section">
        <h3>Comments</h3>

        <div id="commentsList">
            <?php 
            $sql_comments = "SELECT c.*, u.Username 
                             FROM Comment c 
                             JOIN Users u ON c.UserID = u.UserID
                             WHERE c.AnnouncementID = ? 
                             ORDER BY c.DateCommented DESC";
            $stmt_comments = $conn->prepare($sql_comments);
            $stmt_comments->bind_param("i", $announcement_id);
            $stmt_comments->execute();
            $comments_result = $stmt_comments->get_result();

            if ($comments_result->num_rows > 0) {
                while ($comment = $comments_result->fetch_assoc()):
            ?>
                <div class="comment-item" style="border-bottom: 1px dotted #a3b565; padding: 10px 0;">
                    <p>
                        <strong><?php echo htmlspecialchars($comment['Username']); ?>:</strong> 
                        <?php echo htmlspecialchars($comment['CommentText']); ?>
                    </p>
                    <small style="color: #9aa58d;">
                        <?php echo date("Y-m-d H:i", strtotime($comment['DateCommented'])); ?>
                    </small>
                </div>
            <?php endwhile;
            } else {
                echo '<p>No comments yet. Be the first to comment!</p>';
            }
            ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form class="comment-form" id="commentForm">
                <input type="hidden" name="announcement_id" value="<?php echo $announcement_id; ?>">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                
                <textarea name="comment_text" id="newComment" placeholder="Write a comment..." required></textarea>
                <button type="submit" class="btn">Post Comment</button>
            </form>
        <?php else: ?>
             <p style="text-align: center; color: #f1642e; font-weight: bold;">
                Please <a href="login.html" style="color: #f1642e; text-decoration: underline;">log in</a> to post a comment.
            </p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

<script src="view_announcement_script.js"></script>

</body>
</html>