<?php
include "config.php";

if (!isset($_GET['id'])) {
    die("Profil introuvable");
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM talents WHERE id = ?");
$stmt->execute([$id]);

$talent = $stmt->fetch();

if (!$talent) {
    die("Talent introuvable");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($talent['name']) ?></title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#0e1018;
    color:white;
    padding:40px;
}

.card{
    max-width:700px;
    margin:auto;
    background:#161824;
    padding:30px;
    border-radius:15px;
    border:1px solid rgba(255,255,255,0.08);
}

h1{
    margin-bottom:10px;
    font-size:32px;
}

.skill{
    color:#8b5cf6;
    margin-bottom:20px;
    font-size:18px;
}

.info{
    margin-bottom:15px;
    line-height:1.6;
}

.label{
    color:#8b5cf6;
    font-weight:bold;
}

a{
    display:inline-block;
    margin-top:20px;
    color:white;
    background:#8b5cf6;
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
    transition:0.2s;
}

a:hover{
    background:#7c3aed;
}

</style>

</head>
<body>

<div class="card">

    <h1><?= htmlspecialchars($talent['name'] ?? 'Talent') ?></h1>

    <div class="skill">
        <?= htmlspecialchars($talent['skill'] ?? 'Non spécifié') ?>
    </div>

    <div class="info">
        <span class="label">Ville :</span>
        <?= htmlspecialchars($talent['city'] ?? 'Tunisie') ?>
    </div>

    <div class="info">
        <span class="label">Email :</span>
        <?= htmlspecialchars($talent['email'] ?? 'Non disponible') ?>
    </div>

    <div class="info">
        <span class="label">Description :</span><br>
        <?= nl2br(htmlspecialchars($talent['description'] ?? 'Aucune description')) ?>
    </div>

    <a href="talents.php">← Retour</a>

</div>

</body>
</html>