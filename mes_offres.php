<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// récupérer offres du recruteur
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$userId]);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Mes Offres</title>

<style>
body {
    background:#0e1018;
    color:white;
    font-family:Arial;
    margin:0;
}

.container {
    padding:30px;
}

.card {
    background:#161824;
    padding:20px;
    margin-bottom:15px;
    border-radius:10px;
}

.btn {
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
}

.edit { background:#60a5fa; color:white; }
.delete { background:#f87171; color:white; }
</style>

</head>

<body>

<div class="container">

<h1>📋 Mes offres</h1>



<br><br>

<?php if (count($jobs) > 0): ?>

    <?php foreach($jobs as $job): ?>

        <div class="card">
            <h3><?= $job["title"] ?></h3>
            <p><?= $job["company"] ?> - <?= $job["location"] ?></p>

            <p><?= substr($job["description"], 0, 100) ?>...</p>

            <a href="edit_job.php?id=<?= $job["id"] ?>" class="btn edit">Modifier</a>
            <a href="delete_job.php?id=<?= $job["id"] ?>" class="btn delete">Supprimer</a>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>Aucune offre pour le moment 😢</p>

<?php endif; ?>

</div>

</body>
</html>