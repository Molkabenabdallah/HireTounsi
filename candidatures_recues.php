<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$recruiter_id = $_SESSION["user_id"];

/* ── récupérer candidatures des offres du recruteur ── */
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        j.title AS job_title,
        j.company,
        j.user_id AS recruiter_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.user_id = ?
    ORDER BY a.id DESC
");

$stmt->execute([$recruiter_id]);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Candidatures reçues</title>

<style>
body{
    background:#0e1018;
    color:white;
    font-family:Arial;
}
.container{padding:30px;}

.card{
    background:#161824;
    padding:18px;
    margin-bottom:12px;
    border-radius:10px;
}

.cv{
    color:#60a5fa;
    text-decoration:none;
}

.status{
    padding:4px 10px;
    border-radius:6px;
    font-size:12px;
    background:#fbbf24;
    color:black;
    display:inline-block;
}
</style>
</head>

<body>

<div class="container">

<h1>📩 Candidatures reçues</h1>

<?php if(count($applications) > 0): ?>

    <?php foreach($applications as $app): ?>

        <div class="card">

            <h3><?= htmlspecialchars($app["job_title"]) ?></h3>
            <p><?= htmlspecialchars($app["company"]) ?></p>

            <p>
                👤 Candidat ID : <?= $app["user_id"] ?>
            </p>

            <p>
                📄 CV :
                <a class="cv" href="uploads/<?= $app["cv"] ?>" target="_blank">
                    Voir CV
                </a>
            </p>

            <p class="status">
                <?= $app["status"] ?>
            </p>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>Aucune candidature reçue.</p>

<?php endif; ?>

</div>

</body>
</html>