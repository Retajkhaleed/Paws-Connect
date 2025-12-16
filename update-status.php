<?php
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION["UserID"]) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION["UserID"];
$announcementID = $_POST['announcement_id'] ?? null;
$newStatus = $_POST['new_status'] ?? null;

if (!$announcementID || !$newStatus) {
    header("Location: my-announcements.php?error=missing_data");
    exit();
}

$success = false;

$sql_check = "SELECT UserID FROM Announcement WHERE AnnouncementID = ?";
if ($stmt_check = $conn->prepare($sql_check)) {
    $stmt_check->bind_param("i", $announcementID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $announcement_owner = $result_check->fetch_assoc();
    $stmt_check->close();

    if (!$announcement_owner || $announcement_owner['UserID'] != $userID) {
        header("Location: my-announcements.php?error=unauthorized");
        exit();
    }
} else {
    header("Location: my-announcements.php?error=sql_check_failed");
    exit();
}

$sql = "UPDATE Announcement SET Status = ? WHERE AnnouncementID = ?";
$status_message = "status_updated";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("si", $newStatus, $announcementID);
    
    if ($stmt->execute()) {
        $success = true;
    }
    $stmt->close();
}

$conn->close();

if ($success) {
    header("Location: my-announcements.php?success=" . $status_message); 
} else {
    header("Location: my-announcements.php?error=update_failed");
}
exit();
?>