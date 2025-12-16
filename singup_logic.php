<?php
session_start();
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $phone    = trim($_POST["phone"]);
    $password = $_POST["password"];

    // Check username
    $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE Username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header("Location: signup.html?error=username");
        exit;
    }

    // Check phone
    $stmt = mysqli_prepare($conn, "SELECT UserID FROM Users WHERE PhoneNumber=?");
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header("Location: signup.html?error=phone");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO Users (Username, Password, PhoneNumber)
         VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sss", $username, $hashed, $phone);

    if (mysqli_stmt_execute($stmt)) {

    $_SESSION["UserID"] = mysqli_insert_id($conn);
    $_SESSION["Username"] = $username;
    $_SESSION["Phone"] = $phone;

    header("Location: home_main.php");
    exit;

    } else {
        header("Location: signup.html?error=general");
        exit;
    }
}
?>
