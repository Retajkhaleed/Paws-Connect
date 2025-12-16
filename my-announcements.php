<?php
session_start();

include 'db_connect.php'; 

if (!isset($_SESSION["UserID"])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION["UserID"];
$my_announcements = [];
$error_message = null;

$final_statuses = [
    'CatAdoption' => ['Adopted'],
    'LostCat' => ['Found'],
    'SickCat' => ['Recovered'],
];

$sql = "SELECT 
            AnnouncementID, Title, Location, Type, DateCreated, Status, PhotoURL
        FROM Announcement
        WHERE UserID = ?
        ORDER BY DateCreated DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userID);
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $my_announcements[] = $row;
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
    <title>My Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet" />
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

    <h1 class="page-title">My Announcements</h1>
    <main>
      <section class="simple-page-container">
        <div id="announcement-list-container">
            <?php if ($error_message): ?>
                <p class="no-announcements" style="color: red;">Database Error: <?php echo $error_message; ?></p>
            <?php elseif (empty($my_announcements)): ?>
                <p class="no-announcements">You have not posted any announcements yet.</p>
            <?php else: ?>
                <?php foreach ($my_announcements as $announcement): ?>
                    <?php 
                        $photoURL = $announcement['PhotoURL'] ? $announcement['PhotoURL'] : 'img/default-cat.png';
                        $currentType = $announcement['Type'];
                        $currentStatus = $announcement['Status'];
                        $possibleFinalStatus = $final_statuses[$currentType][0] ?? null;
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

                            <form class="status-action-form" id="form-<?php echo $announcement['AnnouncementID']; ?>">
                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['AnnouncementID']; ?>">
                                
                                <select name="new_status" required 
                                        id="select-<?php echo $announcement['AnnouncementID']; ?>"> 
                                    <option value="" disabled selected>Change Status</option>
                                    
                                    <?php 
                                    if ($currentStatus != 'Active'): 
                                    ?>
                                        <option value="Active">Active</option>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    if ($currentStatus == 'Active' && $possibleFinalStatus): 
                                    ?>
                                        <option value="<?php echo $possibleFinalStatus; ?>">
                                            <?php echo $possibleFinalStatus; ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                                
                                <button type="button" class="btn-save-status" 
                                        id="save-btn-<?php echo $announcement['AnnouncementID']; ?>" 
                                        disabled>Save</button>
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
        <div class="footer-left">
            <span>Paws Connect 2025 ©</span>
        </div>

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

    <script>
        function toggleSaveButton(announcementId) {
            const selectElement = document.getElementById('select-' + announcementId);
            const saveButton = document.getElementById('save-btn-' + announcementId);
            
            if (selectElement.value && selectElement.value !== '') {
                saveButton.disabled = false;
            } else {
                saveButton.disabled = true;
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.status-action-form select').forEach(select => {
                const id = select.id.split('-')[1];
                const button = document.getElementById('save-btn-' + id);
                
                select.addEventListener('change', () => toggleSaveButton(id));
                
                if (button) {
                    button.disabled = true;
                }
            });

            document.querySelectorAll('.status-action-form button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); 
                    
                    const form = this.closest('.status-action-form');
                    const announcementId = form.querySelector('input[name="announcement_id"]').value;
                    const newStatus = form.querySelector('select[name="new_status"]').value;
                    
                    if (!newStatus) return;

                    const formData = new URLSearchParams();
                    formData.append('announcement_id', announcementId);
                    formData.append('new_status', newStatus);

                    fetch('update-status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(() => {
                        window.location.reload(); 
                    })
                    .catch(error => {
                        alert('Failed to update status.');
                        console.error('Error:', error);
                    });
                });
            });
        });
    </script>
    <?php 
    $conn->close();
    ?>
  </body>
</html>