<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];
    $datum_uclanjenja = $_POST['datum_uclanjenja'];
    $clanarina_do = $_POST['clanarina_do'];

    $stmt = $conn->prepare("INSERT INTO members (ime, prezime, email, telefon, datum_uclanjenja, clanarina_do) VALUES (?, ?, ?, ?, ?, ?)");
    
    if(!$stmt){

        echo "❌ Greška u upitu: " . $conn->error;
        exit;
    }


    $stmt->bind_param("ssssss", $ime, $prezime, $email, $telefon, $datum_uclanjenja, $clanarina_do);

    if($stmt->execute()){
        echo "✅ Član uspešno dodat!";
    } else {
        echo "❌ Greška prilikom dodavanja člana: " . $stmt->error;
    }

    $stmt->close();
}
?>
