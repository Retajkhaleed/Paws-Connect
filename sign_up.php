<?php
session_start();
require_once "db_connect.php"; // Connect to database

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = htmlspecialchars($_POST["username"] ?? '');
    $phone    = htmlspecialchars($_POST["phone"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm  = $_POST["confirm"] ?? '';

    if ($username === "" || $phone === "" || $password === "" || $confirm === "") {
        echo "<h2 style='color:red;'>Please fill in all fields.</h2>";
        echo "<p><a href='signup.html'>Back to Sign Up</a></p>";
        exit;
    }

    // التحقق من تطابق كلمة المرور
    if ($password !== $confirm) {
        echo "<h2 style='color:red;'>Passwords do not match.</h2>";
        echo "<p><a href='signup.html'>Back to Sign Up</a></p>";
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<h2 style='color:red;'>Username already exists.</h2>";
        echo "<p><a href='signup.html'>Back to Sign Up</a></p>";
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (Username, Password, PhoneNumber) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $passwordHash, $phone);

    if ($stmt->execute()) {
        echo "<h2>✅ Registration successful! Welcome, " . htmlspecialchars($username) . ".</h2>";
        echo "<p><a href='login.html'>Go to Login</a></p>";
    } else {
        echo "<h2 style='color:red;'>Error: Could not register user.</h2>";
        echo "<p>Details: " . $conn->error . "</p>";
        echo "<p><a href='signup.html'>Back to Sign Up</a></p>";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "<h2>This page expects form data.</h2>";
    echo "<p><a href='signup.html'>Go to Sign Up Page</a></p>";
}
?>
