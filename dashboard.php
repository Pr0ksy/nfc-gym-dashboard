<?php
session_start();
include("includes/db.php");

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'obrisan') {
        $message = "<p class='alert alert-red'>Član je uspešno obrisan!</p>";
    } elseif ($_GET['msg'] == 'obnovljena') {
        $message = "<p class='alert alert-green'>✅ Članarina je uspešno obnovljena!</p>";
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = '';
$sort_by = 'id';
$order = 'ASC';
$status_filter = 'all';

$sql = "SELECT * FROM members";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $search_safe = $conn->real_escape_string($search);
    $sql .= " WHERE ime LIKE '%$search_safe%' OR prezime LIKE '%$search_safe%' OR email LIKE '%$search_safe%'";
}

if (isset($_GET['status_filter'])) $status_filter = $_GET['status_filter'];
if ($status_filter == 'active') {
    $sql .= (strpos($sql,'WHERE') !== false) ? " AND clanarina_do >= CURDATE()" : " WHERE clanarina_do >= CURDATE()";
} elseif ($status_filter == 'expired') {
    $sql .= (strpos($sql,'WHERE') !== false) ? " AND clanarina_do < CURDATE()" : " WHERE clanarina_do < CURDATE()";
}

$allowed_sort = ['id', 'ime', 'prezime', 'clanarina_do'];
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort)) $sort_by = $_GET['sort'];
if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC','DESC'])) $order = strtoupper($_GET['order']);
$sql .= " ORDER BY $sort_by $order";

$result = $conn->query($sql);


$total = $conn->query("SELECT COUNT(*) as total FROM members")->fetch_assoc()['total'];
$active_count = $conn->query("SELECT COUNT(*) as active FROM members WHERE clanarina_do >= CURDATE()")->fetch_assoc()['active'];
$expired_count = $conn->query("SELECT COUNT(*) as expired FROM members WHERE clanarina_do < CURDATE()")->fetch_assoc()['expired'];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FITSCOPE Dashboard</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css?v=2.0">
</head>
<body>
<div class="dashboard-wrapper">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="images/logo.png" alt="Logo">
            <h2>FITSCOPE</h2>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
                <li><a href="nfc_check.php">🏋️‍♂️ Dolasci</a></li>
                <li><a href="#" id="addMemberBtn">➕ Dodaj člana</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <h1>Dobrodošao, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <?php if($message) echo $message; ?>

        <div class="stats">
            <div class="stat-card total">📊 Ukupno članova: <strong><?php echo $total; ?></strong></div>
            <div class="stat-card active">✅ Aktivni: <strong><?php echo $active_count; ?></strong></div>
            <div class="stat-card expired">❌ Istekli: <strong><?php echo $expired_count; ?></strong></div>
        </div>

        <hr>
        <br>

        <div class="filters">
            <form method="GET" action="dashboard.php" class="search-form">
                <input type="text" name="search" placeholder="Unesi ime, prezime ili email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Pretraži</button>
                <a href="dashboard.php" class="reset-btn">Reset</a>
            </form>

            <form method="GET" action="dashboard.php" class="sort-form">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <label>Sortiraj po:</label>
                <select name="sort">
                    <option value="id" <?php if($sort_by=='id') echo 'selected'; ?>>ID</option>
                    <option value="ime" <?php if($sort_by=='ime') echo 'selected'; ?>>Ime</option>
                    <option value="prezime" <?php if($sort_by=='prezime') echo 'selected'; ?>>Prezime</option>
                    <option value="clanarina_do" <?php if($sort_by=='clanarina_do') echo 'selected'; ?>>Članarina do</option>
                </select>
                <select name="order">
                    <option value="ASC" <?php if($order=='ASC') echo 'selected'; ?>>Rastuće</option>
                    <option value="DESC" <?php if($order=='DESC') echo 'selected'; ?>>Opadajuće</option>
                </select>
                <button type="submit">Sortiraj</button>
            </form>

            <form method="GET" action="dashboard.php" class="filter-form">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                <select name="status_filter">
                    <option value="all" <?php if($status_filter=='all') echo 'selected'; ?>>Svi</option>
                    <option value="active" <?php if($status_filter=='active') echo 'selected'; ?>>✅ Aktivni</option>
                    <option value="expired" <?php if($status_filter=='expired') echo 'selected'; ?>>❌ Istekli</option>
                </select>
                <button type="submit">Filtriraj</button>
            </form>
        </div>

        <hr>

        <br>
        <h3>Lista članova</h3>
        <br>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Članarina do</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="member-row" data-id="<?php echo $row['id']; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['ime']); ?></td>
                        <td><?php echo htmlspecialchars($row['prezime']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefon']); ?></td>
                        <td><?php echo $row['clanarina_do']; ?></td>
                        <td>
                        <?php
                        $clanarina = strtotime($row['clanarina_do']);
                        $now = time();
                        $days_left = ($clanarina - $now) / (60*60*24);

                        if($days_left < 0){
                            echo "❌ Istekla";
                        } elseif($days_left <= 7){
                            echo "⚠️ Uskoro ističe";
                        } else {
                            echo "✅ Aktivna";
                        }
                        ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <br>
            <br>
            <a href="export_excel.php" class="export-btn">📥 Exportuj u Excel</a>
        </div>
    </main>
</div>

<div id="addMemberModal" class="modal">
    <div class="modal-content">
        <span id="closeAddModal" class="close">&times;</span>
        <h3>Dodaj novog člana</h3>
        <form id="addMemberForm">
            <input type="text" name="ime" placeholder="Ime" required>
            <input type="text" name="prezime" placeholder="Prezime" required>
            <input type="email" name="email" placeholder="Email">
            <input type="text" name="telefon" placeholder="Telefon">
            <input type="date" name="datum_uclanjenja" required>
            <input type="date" name="clanarina_do" required>
            <button type="submit">Dodaj člana</button>
        </form>
        <div id="addMemberMessage"></div>
    </div>
</div>


<div id="memberModal" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="close">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

<script src="js/dashboard.js"></script>
</body>
</html>


