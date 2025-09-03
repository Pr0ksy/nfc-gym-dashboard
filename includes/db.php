<?php
$host = "localhost";
$user = "root";  
$pass = "";      
$db   = "gym_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Konekcija neuspešna: " . $conn->connect_error);
}
?>