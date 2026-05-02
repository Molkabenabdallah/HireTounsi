<?php
session_start();
include "config.php";

if ($_SESSION["role"] != "admin") {
    die("Accès refusé");
}

$id = $_GET["id"];

$stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

header("Location: admin.php");
exit();