<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$initials = strtoupper(substr($user['name'],0,1));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Profil</title>

<style>
body {
    background:#07080d;
    color:white;
    font-family: 'Arial';
    margin:0;
}

/* layout */
.container {
    display:flex;
}

/* sidebar */
.sidebar {
    width:220px;
    background:#0e1018;
    height:100vh;
    padding:20px;
}

.sidebar a {
    display:block;
    color:#aaa;
    margin:10px 0;
    text-decoration:none;
}

/* main */
.main {
    flex:1;
    padding:30px;
}

/* card */
.card {
    background:#161824;
    border-radius:14px;
    padding:20px;
    margin-bottom:20px;
}

/* avatar */
.avatar {
    width:70px;
    height:70px;
    border-radius:50%;
    background:#8b5cf6;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    font-size:22px;
}

/* progress */
.progress-bar {
    height:6px;
    background:#333;
    border-radius:10px;
    margin-top:10px;
}

.progress {
    height:100%;
    background:#8b5cf6;
    border-radius:10px;
}

/* grid */
.grid {
    display:grid;
    grid-template-columns: 1fr 2fr;
    gap:20px;
}
</style>

</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>HireTounsi</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="profile.php">👤 Mon profil</a>
    <a href="mes_offres.php">📋 Mes offres</a>
    <a href="logout.php">🚪 Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

<h2>Mon Profil</h2>

<div class="grid">

<!-- LEFT -->
<div>

<div class="card">
    <div class="avatar"><?= $initials ?></div>
    <h3><?= $user['name'] ?></h3>
    <p><?= $user['job_title'] ?: "Aucun titre" ?></p>

    <p style="color:#aaa"><?= $user['email'] ?></p>

    <a href="edit_profile.php" style="color:#8b5cf6;">Modifier profil</a>
</div>

<div class="card">
    <h4>Infos</h4>
    <p>📞 <?= $user['phone'] ?: "Non renseigné" ?></p>
</div>

</div>

<!-- RIGHT -->
<div>

<div class="card">
    <h3>Complétez votre profil</h3>

    <?php
    $fields = [
        !empty($user['profile_photo']),
        !empty($user['cv']),
        !empty($user['skills']),
        !empty($user['bio']),
        !empty($user['job_title'])
    ];
    $progress = intval(array_sum($fields)/count($fields)*100);
    ?>

    <strong><?= $progress ?>%</strong>

    <div class="progress-bar">
        <div class="progress" style="width:<?= $progress ?>%"></div>
    </div>
</div>

<div class="card">
    <h3>Bio</h3>
    <p><?= $user['bio'] ?: "Ajoutez une bio..." ?></p>
</div>

<div class="card">
    <h3>Compétences</h3>
    <p><?= $user['skills'] ?: "Ajoutez vos compétences..." ?></p>
</div>

<div class="card">
    <h3>CV</h3>

    <?php if($user['cv']): ?>
        <a href="uploads/<?= $user['cv'] ?>" target="_blank">📄 Voir CV</a>
    <?php else: ?>
        <p>Aucun CV</p>
    <?php endif; ?>
</div>

</div>

</div>

</div>
</div>

</body>
</html>