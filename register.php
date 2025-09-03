<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = "staff"; // svi novi korisnici će biti staff, admina možeš ručno dodati 1 put

    if (!empty($username) && !empty($email) && !empty($password)) {
        // hash lozinke
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // priprema SQL upita
        $query = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $username, $email, $hashedPassword, $role);

        if ($query->execute()) {
            echo "✅ Registracija uspešna! Možete se <a href='login.php'>ulogovati</a>.";
        } else {
            echo "❌ Greška: " . $conn->error;
        }
    } else {
        echo "⚠️ Popunite sva polja!";
    }
}
?>

<h2>Registracija</h2>
<form method="POST">
  <input type="text" name="username" placeholder="Korisničko ime" required><br><br>
  <input type="email" name="email" placeholder="Email" required><br><br>
  <input type="password" name="password" placeholder="Lozinka" required><br><br>
  <button type="submit">Registruj se</button>
</form>
