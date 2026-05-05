<?php
session_start();
include "config.php";

/* 🔒 SECURITY */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

/* 🔎 CHECK ID */
if (!isset($_GET["id"])) {
    header("Location: admin.php?page=users");
    exit();
}

$id = intval($_GET["id"]);

/* ⚠️ EMPÊCHER SUPPRESSION ADMIN */
$stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable");
}

if ($user["role"] === "admin") {
    die("Impossible de supprimer un admin !");
}

/* 🗑 DELETE */
$delete = $pdo->prepare("DELETE FROM users WHERE id=?");
$delete->execute([$id]);

/* 🔁 REDIRECT */
header("Location: admin.php?page=users&success=deleted");
exit();