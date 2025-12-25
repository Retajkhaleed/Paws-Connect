<?php
session_start();

session_unset();
session_destroy();

header("Location: /Paws-Connect/home_main.php");
exit;
?>
