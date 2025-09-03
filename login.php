<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Pogrešna lozinka!";
        }
    } else {
        $error = "❌ Korisnik ne postoji!";
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FITSCOPE</title>
    <link rel="stylesheet" href="css/login.css?v=1.0">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <img src="images/logo.png" alt="Logo">
            </div>
            <h2>Prijava u sistem</h2>

            <?php if(isset($error)) echo '<p class="error">'.$error.'</p>'; ?>

            <form method="POST" action="">
                <input type="text" name="username" placeholder="Korisničko ime" required>
                <input type="password" name="password" placeholder="Lozinka" required>
                <button type="submit">Prijavi se</button>
            </form>
        </div>
    </div>
</body>
</html>

