session_start(); 
require_once "db_connect.php";  

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    if ($username === "" || $password === "") {
        echo "<h2 style='color:red;'>Please enter both username and password.</h2>";
        echo "<p><a href='login.html'>Back to Login Page</a></p>";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        
        if ($user['password'] === $password) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];

            header("Location: account.php");
            exit;
        } else {
            echo "<h2 style='color:red;'>Incorrect password.</h2>";
            echo "<p><a href='login.html'>Back to Login Page</a></p>";
        }


    } else {
        echo "<h2 style='color:red;'>Username not found.</h2>";
        echo "<p><a href='login.html'>Back to Login Page</a></p>";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "<h2>This page expects form data.</h2>";
    echo "<p><a href='login.html'>Go to Login Page</a></p>";
}

?>
