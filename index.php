<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: view/alumni/dashboard.php");
} else {
    header("Location: view/auth/login.php");
}
exit();
