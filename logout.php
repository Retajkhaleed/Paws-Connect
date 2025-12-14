<?php
session_start();

session_unset();
session_destroy();

header("Location: /my_app/home_main.php");
exit;
?>
