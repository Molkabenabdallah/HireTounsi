<?php
session_start();
include "config.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

/* =========================
   SEARCH USERS
========================= */
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if ($search != "") {
    $stmt = $pdo->prepare("SELECT * FROM users 
        WHERE name LIKE ? OR email LIKE ? 
        ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}

$users = $stmt->fetchAll();

/* =========================
   TALENTS PENDING
========================= */
$talents = $pdo->query("SELECT * FROM talents WHERE status='pending' ORDER BY id DESC")->fetchAll();

/* =========================
   STATS
========================= */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalTalents = $pdo->query("SELECT COUNT(*) FROM talents")->fetchColumn();
$pendingTalents = $pdo->query("SELECT COUNT(*) FROM talents WHERE status='pending'")->fetchColumn();
$approvedTalents = $pdo->query("SELECT COUNT(*) FROM talents WHERE status='approved'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - HireTounsi</title>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:Arial;
}

body{
  background:#0c0c18;
  color:white;
  display:flex;
}

/* ================= SIDEBAR ================= */
.sidebar{
  width:240px;
  height:100vh;
  background:#14142c;
  padding:20px;
  position:fixed;
  border-right:1px solid rgba(255,255,255,0.08);
}

.sidebar h2{
  color:#a78bfa;
  margin-bottom:20px;
}

.sidebar a{
  display:block;
  padding:10px;
  color:#aaa;
  text-decoration:none;
  border-radius:8px;
  margin-bottom:8px;
}

.sidebar a:hover{
  background:#1a1a38;
  color:white;
}

/* ================= MAIN ================= */
.main{
  margin-left:240px;
  padding:30px;
  width:100%;
}

/* ================= STATS ================= */
.stats{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:15px;
  margin-bottom:30px;
}

.stat{
  background:#14142c;
  padding:15px;
  border-radius:10px;
  border:1px solid rgba(255,255,255,0.08);
}

.stat h4{
  color:#a78bfa;
  font-size:13px;
}

.stat p{
  font-size:22px;
  font-weight:bold;
}

/* ================= TABLE ================= */
table{
  width:100%;
  border-collapse:collapse;
  background:#14142c;
  border-radius:10px;
  overflow:hidden;
  margin-bottom:30px;
}

th, td{
  padding:12px;
  border-bottom:1px solid rgba(255,255,255,0.08);
}

th{
  color:#a78bfa;
  text-align:left;
}

/* ================= BUTTONS ================= */
.btn{
  padding:6px 10px;
  border-radius:6px;
  text-decoration:none;
  font-size:12px;
  color:white;
}

.approve{ background:#22c55e; }
.reject{ background:#ef4444; }
.edit{ background:#3b82f6; }

/* ================= SEARCH ================= */
.search input{
  padding:10px;
  width:300px;
  background:#14142c;
  border:none;
  border-radius:8px;
  color:white;
  margin-bottom:20px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <h2>👑 Admin Panel</h2>

  <a href="admin.php">🏠 Dashboard</a>
  <a href="#users">👤 Users</a>
  <a href="#talents">🎯 Talents</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Dashboard Admin</h1>

<!-- STATS -->
<div class="stats">
  <div class="stat">
    <h4>Total Users</h4>
    <p><?= $totalUsers ?></p>
  </div>

  <div class="stat">
    <h4>Total Talents</h4>
    <p><?= $totalTalents ?></p>
  </div>

  <div class="stat">
    <h4>Pending</h4>
    <p><?= $pendingTalents ?></p>
  </div>

  <div class="stat">
    <h4>Approved</h4>
    <p><?= $approvedTalents ?></p>
  </div>
</div>

<!-- ================= USERS ================= -->
<h2 id="users">👤 Users</h2>

<form method="GET" class="search">
  <input type="text" name="search" placeholder="Rechercher user..." value="<?= htmlspecialchars($search) ?>">
</form>

<table>
<tr>
  <th>ID</th>
  <th>Nom</th>
  <th>Email</th>
  <th>Role</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
  <td><?= $u["id"] ?></td>
  <td><?= htmlspecialchars($u["name"]) ?></td>
  <td><?= htmlspecialchars($u["email"]) ?></td>
  <td><?= $u["role"] ?></td>
</tr>
<?php endforeach; ?>
</table>

<!-- ================= TALENTS ================= -->
<h2 id="talents">🎯 Talents en attente</h2>

<table>
<tr>
  <th>Nom</th>
  <th>Skill</th>
  <th>Action</th>
</tr>

<?php foreach($talents as $t): ?>
<tr>
  <td><?= htmlspecialchars($t["name"]) ?></td>
  <td><?= htmlspecialchars($t["skill"]) ?></td>

  <td>
    <a class="btn approve" href="approve.php?id=<?= $t['id'] ?>">Approve</a>
    <a class="btn reject" href="reject.php?id=<?= $t['id'] ?>">Reject</a>
  </td>
</tr>
<?php endforeach; ?>
</table>

</div>

</body>
</html>