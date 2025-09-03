<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) exit("Niste ulogovani!");
if (!isset($_GET['id'])) exit("ID nije prosleđen!");

$id = (int)$_GET['id'];

$delete = $conn->query("DELETE FROM members WHERE id=$id");

if ($delete) {
    echo "Član uspešno obrisan!";
} else {
    echo "Greška pri brisanju: " . $conn->error;
}
