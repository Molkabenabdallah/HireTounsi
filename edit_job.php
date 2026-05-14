<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"] ?? null;

/* récupérer job */
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id=?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
    die("Offre introuvable");
}

/* update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $company = $_POST["company"];
    $city = $_POST["city"];
    $description = $_POST["description"];

    $update = $pdo->prepare("
        UPDATE jobs
        SET title=?, company=?, city=?, description=?
        WHERE id=?
    ");

    $update->execute([$title, $company, $city, $description, $id]);

    header("Location: mes_offres.php");
    exit();
}
?>

<h2>Modifier l'offre</h2>

<form method="POST">

    <input type="text" name="title" value="<?= htmlspecialchars($job["title"]) ?>"><br><br>

    <input type="text" name="company" value="<?= htmlspecialchars($job["company"]) ?>"><br><br>

    <input type="text" name="city" value="<?= htmlspecialchars($job["city"]) ?>"><br><br>

    <textarea name="description"><?= htmlspecialchars($job["description"]) ?></textarea><br><br>

    <button type="submit">Modifier</button>

</form>