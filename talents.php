<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// seulement acceptés
$stmt = $pdo->query("SELECT * FROM talents WHERE status = 'approved' ORDER BY id DESC");
$talents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Talents Tunisiens</title>

<style>
body{
  font-family: Arial;
  background:#0c0c18;
  color:white;
  padding:30px;
}

h1{
  margin-bottom:20px;
}

.container{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:20px;
}

.card{
  background:#14142c;
  padding:20px;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.08);
}

.btn{
  display:inline-block;
  margin-top:10px;
  padding:8px 12px;
  border-radius:8px;
  background:#7c3aed;
  color:white;
  text-decoration:none;
}
</style>
</head>

<body>

<h1>🇹🇳 Talents Tunisiens</h1>

<a href="add_profile.php" class="btn">+ Ajouter profil</a>

<br><br>

<div class="container">

<?php foreach($talents as $t): ?>
  <div class="card">
    <h3><?= htmlspecialchars($t['name']) ?></h3>
    <p><?= htmlspecialchars($t['skill']) ?></p>
    <p><?= htmlspecialchars($t['description']) ?></p>
  </div>
<?php endforeach; ?>

</div>

</body>
</html>