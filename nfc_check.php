<?php
include("includes/db.php");

$uid = $_GET['uid'] ?? null;

$expire_time = date('Y-m-d H:i:s', time() - 5 * 3600);
$conn->query("DELETE FROM attendance WHERE dolazak < '$expire_time'");

$message = '';
if($uid) {

    $stmt = $conn->prepare("SELECT id, ime, prezime FROM members WHERE nfc_uid=? LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $member = $result->fetch_assoc();
        $member_id = $member['id'];
        $now = date('Y-m-d H:i:s');

        $stmt2 = $conn->prepare("INSERT INTO attendance (member_id, dolazak) VALUES (?, ?)");
        $stmt2->bind_param("is", $member_id, $now);
        $stmt2->execute();

        $message = "<p class='success'>✅ Član: {$member['ime']} {$member['prezime']} | Dolazak zabeležen: $now</p>";
    } else {
        $message = "<p class='error'>❌ NFC UID nije prepoznat!</p>";
    }
}

echo <<<STYLE
<style>
body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    padding: 20px;
}

h2 {
    color: #333;
    margin-bottom: 10px;
}

.success {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.error {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

table {
    border-collapse: collapse;
    width: 100%;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

th, td {
    text-align: left;
    padding: 12px 15px;
}

th {
    background-color: #007bff;
    color: white;
    text-transform: uppercase;
    font-size: 14px;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #e9ecef;
    cursor: pointer;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 15px;
    transition: 0.2s;
}

.back-btn:hover {
    background-color: #0056b3;
}
</style>
STYLE;

echo '<a href="dashboard.php" class="back-btn">⬅ Nazad na Dashboard</a>';
echo $message;

echo "<h2>Dolasci danas</h2>";
echo "<table>";
echo "<tr><th>Član</th><th>Dolazak</th></tr>";

$today = date('Y-m-d');
$stmt3 = $conn->prepare("
    SELECT m.ime, m.prezime, a.dolazak 
    FROM attendance a
    JOIN members m ON a.member_id = m.id
    WHERE DATE(a.dolazak) = ?
    ORDER BY a.dolazak ASC
");
$stmt3->bind_param("s", $today);
$stmt3->execute();
$res3 = $stmt3->get_result();

while($row = $res3->fetch_assoc()){
    echo "<tr>";
    echo "<td>{$row['ime']} {$row['prezime']}</td>";
    echo "<td>{$row['dolazak']}</td>";
    echo "</tr>";
}

echo "</table>";
?>

