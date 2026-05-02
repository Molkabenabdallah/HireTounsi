<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    exit();
}

$stmt = $pdo->prepare("
INSERT INTO talents (user_id,name,skill,description,status)
VALUES (?,?,?,?,'pending')
");

$stmt->execute([
    $_SESSION["user_id"],
    $_POST["name"],
    $_POST["skill"],
    $_POST["description"]
]);

header("Location: talents.php");
exit();