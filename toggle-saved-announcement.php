<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION["UserID"]) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["UserID"];
$announcementID = $_POST['announcement_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$announcementID || !in_array($action, ['save', 'unsave'])) {
    header("Location: view-announcement.php?id=$announcementID&error=invalid_request");
    exit();
}

$success = false;

if ($action === 'unsave') {
    $sql = "DELETE FROM SavedAnnouncement WHERE UserID = ? AND AnnouncementID = ?";
    $status_message = "unsaved";
} elseif ($action === 'save') {
    $sql = "INSERT INTO SavedAnnouncement (UserID, AnnouncementID, DateSaved) 
            SELECT ?, ?, NOW() 
            WHERE NOT EXISTS (SELECT 1 FROM SavedAnnouncement WHERE UserID = ? AND AnnouncementID = ?)";
    $status_message = "saved";
}

if ($stmt = $conn->prepare($sql)) {
    if ($action === 'save') {
        $stmt->bind_param("iiii", $userID, $announcementID, $userID, $announcementID);
    } else {
        $stmt->bind_param("ii", $userID, $announcementID);
    }
    
    if ($stmt->execute()) {
        $success = true;
    }
    $stmt->close();
}

$conn->close();

if ($success) {
    header("Location: view-announcement.php?id=$announcementID&success=" . $status_message);
} else {
    header("Location: view-announcement.php?id=$announcementID&error=" . $status_message . "_failed");
}
exit();
?> 
