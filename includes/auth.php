<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function checkRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        echo "Nemate dozvolu za pristup ovoj stranici!";
        exit();
    }
}
?>