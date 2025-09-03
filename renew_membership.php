<?php
session_start();
include("includes/db.php");


if (!isset($_SESSION['user_id'])) exit("Niste ulogovani!");

$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$months = isset($_POST['months']) ? (int)$_POST['months'] : (isset($_GET['months']) ? (int)$_GET['months'] : 0);

if ($id <= 0 || $months <= 0) exit("Nedostaju ili su neispravni podaci!");

$result = $conn->query("SELECT clanarina_do FROM members WHERE id=$id");
if ($result->num_rows == 0) exit("Član nije pronađen!");

$row = $result->fetch_assoc();
$current_date = $row['clanarina_do'];

$base_date = (strtotime($current_date) >= time()) ? $current_date : date('Y-m-d');

$new_date = date('Y-m-d', strtotime($base_date . " +$months month"));

$update = $conn->query("UPDATE members SET clanarina_do='$new_date' WHERE id=$id");

if ($update) {
    echo "Članarina uspešno obnovljena do $new_date";
} else {
    echo "Greška pri obnovi članarine: " . $conn->error;
}
