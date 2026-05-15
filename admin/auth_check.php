<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
        header("Location: ../index.php");
        exit();
    }
?>
