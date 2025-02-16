<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}
echo "Welcome, " . htmlspecialchars($_SESSION['username']) . "!";
?>
