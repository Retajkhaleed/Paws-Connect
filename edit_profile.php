<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["UserID"])) {
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['api'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    header("Location: login.html");
    exit;
}

$userID = $_SESSION["UserID"];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['api'])) {
    $stmt = mysqli_prepare($conn, "SELECT Username, PhoneNumber, ProfilePhotoURL FROM Users LEFT JOIN Profile ON Users.UserID = Profile.UserID WHERE Users.UserID=?");
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $profilePhoto = $user["ProfilePhotoURL"] ?? "img/Pawlogo.png";
    if (!empty($user["ProfilePhotoURL"]) && strpos($profilePhoto, 'img/') !== 0) {
        $profilePhoto = $user["ProfilePhotoURL"];
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'username' => $user["Username"],
            'phone' => $user["PhoneNumber"],
            'profilePhoto' => $profilePhoto
        ]
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $phone = trim($_POST["phone"]);
    $newPassword = $_POST["password"] ?? "";
    
    $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE (Username=? OR PhoneNumber=?) AND UserID != ?");
    mysqli_stmt_bind_param($stmt, "ssi", $username, $phone, $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username or phone number already in use']);
        exit;
    }

    $updatePhoto = false;
    $profilePhotoPath = null;
    
    if (isset($_FILES["profilePhoto"]) && $_FILES["profilePhoto"]["error"] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES["profilePhoto"]["tmp_name"];
        $ext = strtolower(pathinfo($_FILES["profilePhoto"]["name"], PATHINFO_EXTENSION));
        $newName = "images/profile_" . $userID . "." . $ext;
        $targetPath = $newName;
        
        if (!file_exists("images")) {
            mkdir("images", 0777, true);
        }
        
        if (move_uploaded_file($tmpName, $targetPath)) {
            $profilePhotoPath = $newName;
            $updatePhoto = true;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile photo']);
            exit;
        }
    }
    
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO Profile (UserID) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);

    if (!empty($newPassword)) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE Users SET Username=?, PhoneNumber=?, Password=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "sssi", $username, $phone, $hashed, $userID);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE Users SET Username=?, PhoneNumber=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "ssi", $username, $phone, $userID);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        exit;
    }

    if ($updatePhoto) {
        $stmt = mysqli_prepare($conn, "UPDATE Profile SET ProfilePhotoURL=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "si", $profilePhotoPath, $userID);
        mysqli_stmt_execute($stmt);
    }

    $_SESSION["Username"] = $username;
    $_SESSION["Phone"] = $phone;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    exit;
}

\header("Location: edit_profile.html");
exit;
?>
