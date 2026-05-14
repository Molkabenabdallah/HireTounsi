<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"] ?? null;

if ($id) {

    // supprimer job
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->execute([$id]);

}

header("Location: mes_offres.php");
exit();