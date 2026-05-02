<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

// STATS
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// derniers users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 5");
$latestUsers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
body{
  margin:0;
  font-family:Arial;
  background:#0c0c18;
  color:white;
  display:flex;
}

/* SIDEBAR */
.sidebar{
  width:240px;
  height:100vh;
  background:#14142c;
  padding:20px;
  position:fixed;
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

/* MAIN */
.main{
  margin-left:240px;
  padding:30px;
  width:100%;
}

.cards{
  display:flex;
  gap:15px;
}

.card{
  background:#14142c;
  padding:20px;
  border-radius:10px;
  flex:1;
  border:1px solid rgba(255,255,255,0.08);
}

.card h3{
  color:#a78bfa;
}

.latest{
  margin-top:30px;
  background:#14142c;
  padding:20px;
  border-radius:10px;
}

.user{
  padding:10px;
  border-bottom:1px solid rgba(255,255,255,0.08);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <h2>👑 Admin</h2>
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="admin.php">👤 Users</a>
  <a href="#">💼 Jobs</a>
  <a href="#">🏢 Companies</a>
  <a href="logout.php">🚪 Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Dashboard</h1>

<!-- STATS -->
<div class="cards">
  <div class="card">
    <h3>Total Users</h3>
    <h1><?= $totalUsers ?></h1>
  </div>

  <div class="card">
    <h3>Jobs</h3>
    <h1>0</h1>
  </div>

  <div class="card">
    <h3>Companies</h3>
    <h1>0</h1>
  </div>
</div>

<!-- LATEST USERS -->
<div class="latest">
<h3>Derniers utilisateurs</h3>

<?php foreach($latestUsers as $u): ?>
  <div class="user">
    <?= htmlspecialchars($u['name']) ?> - <?= $u['email'] ?>
  </div>
<?php endforeach; ?>

</div>

</div>

</body>
</html>