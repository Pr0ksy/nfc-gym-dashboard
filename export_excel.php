<?php
include("includes/db.php");

// Naslov fajla
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=clanovi.xls");

// Dohvati sve članove
$sql = "SELECT id, ime, prezime, email, telefon, clanarina_do FROM members ORDER BY id ASC";
$result = $conn->query($sql);

// Ispis zaglavlja kolona
echo "ID\tIme\tPrezime\tEmail\tTelefon\tČlanarina do\n";

// Ispis podataka
while($row = $result->fetch_assoc()){
    echo $row['id'] . "\t" . $row['ime'] . "\t" . $row['prezime'] . "\t" . $row['email'] . "\t" . $row['telefon'] . "\t" . $row['clanarina_do'] . "\n";
}
?>