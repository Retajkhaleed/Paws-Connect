<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["UserID"])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["UserID"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $phone    = trim($_POST["phone"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE (Username=? OR PhoneNumber=?) AND UserID != ?");
    mysqli_stmt_bind_param($stmt, "ssi", $username, $phone, $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header("Location: edit_profile.php?error=duplicate");
        exit;
    }

    $updatePhoto = false;
    $profilePhotoPath = null;
    
    if (isset($_FILES["profilePhoto"]) && $_FILES["profilePhoto"]["error"] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES["profilePhoto"]["tmp_name"];
        $ext = strtolower(pathinfo($_FILES["profilePhoto"]["name"], PATHINFO_EXTENSION));
        $newName = "images/profile_" . $userID . "." . $ext; 
        $targetPath = "../" . $newName; 
        
        if (move_uploaded_file($tmpName, $targetPath)) {
            $profilePhotoPath = $newName; 
            $updatePhoto = true;
        }
    }
    
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO Profile (UserID) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE Users SET Username=?, PhoneNumber=?, Password=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "sssi", $username, $phone, $hashed, $userID);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE Users SET Username=?, PhoneNumber=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "ssi", $username, $phone, $userID);
    }
    mysqli_stmt_execute($stmt);

    if ($updatePhoto) {
        $stmt = mysqli_prepare($conn, "UPDATE Profile SET ProfilePhotoURL=? WHERE UserID=?");
        mysqli_stmt_bind_param($stmt, "si", $profilePhotoPath, $userID);
        mysqli_stmt_execute($stmt);
    }

    $_SESSION["Username"] = $username;
    $_SESSION["Phone"] = $phone;

    header("Location: account.php?success=1");
    exit;
}
?>
