<?php
session_start();
require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare($conn, "SELECT UserID, Username, Password, PhoneNumber FROM Users WHERE Username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["Password"])) {

        $_SESSION["UserID"] = $user["UserID"];
        $_SESSION["Username"] = $user["Username"];
        $_SESSION["Phone"] = $user["PhoneNumber"];

        header("Location: /my_app/");
        exit;
    } else {
        header("Location: ../login.html?error=invalid");
        exit;
    }
}
?>
