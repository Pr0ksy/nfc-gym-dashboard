<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) exit("Niste ulogovani!");
if (!isset($_GET['id'])) exit("ID člana nije prosleđen!");

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM members WHERE id=$id");
if($result->num_rows == 0) exit("Član nije pronađen!");

$member = $result->fetch_assoc();
$clanarina_date = $member['clanarina_do'];
$status = (strtotime($clanarina_date) >= time()) ? "✅ Aktivna" : "❌ Istekla";
?>

<h3><?php echo htmlspecialchars($member['ime'].' '.$member['prezime']); ?></h3>
<p>Email: <?php echo htmlspecialchars($member['email']); ?></p>
<p>Telefon: <?php echo htmlspecialchars($member['telefon']); ?></p>
<p>Članarina do: <span id="memberDate"><?php echo $clanarina_date; ?></span> 
(<span id="memberStatus"><?php echo $status; ?></span>)</p>
<br>
<hr>
<br>
<h4>Obnovi članarinu</h4>
<form id="renewForm">
    <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
    <label>Broj meseci:</label>
    <input type="number" name="months" value="1" min="1" required><br><br>
    <button type="submit">Obnovi</button>
</form>
<div id="renewMessage" style="color:green;margin-top:10px;"></div>
<br>
<hr>
<br>
<h4>Akcije</h4>
<br>
<button id="deleteBtn" style="background:red;color:white;padding:5px 10px;cursor:pointer;">Obriši člana</button>
