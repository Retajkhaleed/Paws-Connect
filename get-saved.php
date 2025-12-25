<?php
session_start();
include 'db_connect.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION["UserID"])) {
    http_response_code(401);
    echo json_encode(["error" => "Authorization required. Please log in to view saved items."]);
    exit();
}

$userID = $_SESSION["UserID"];
$saved_announcements = [];

$sql = "SELECT 
            a.AnnouncementID, a.Title, a.Location, a.Type, a.DateCreated, a.Status
        FROM Announcement a
        JOIN SavedAnnouncement s ON a.AnnouncementID = s.AnnouncementID
        WHERE s.UserID = ?
        ORDER BY s.DateSaved DESC";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $saved_announcements[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["error" => "SQL Prepare Failed: " . mysqli_error($conn)]);
}

mysqli_close($conn);

echo json_encode(["success" => true, "announcements" => $saved_announcements]);
?>
