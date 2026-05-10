<?php
session_start();
include "config.php";

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

$name = $_POST["name"];
$city = $_POST["city"];
$sector = $_POST["sector"];
$description = $_POST["description"];

$logo = $_FILES["logo"];

$logoName = time() . "_" . $logo["name"];

move_uploaded_file($logo["tmp_name"], "uploads/" . $logoName);

$stmt = $pdo->prepare("
INSERT INTO companies(name, city, sector, description, logo, status)
VALUES(?,?,?,?,?, 'pending')
");

$stmt->execute([
    $name,
    $city,
    $sector,
    $description,
    $logoName
]);

header("Location: companies.php");
exit();
?>