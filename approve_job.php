<?php
include "config.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE jobs SET status='approved' WHERE id=?");
$stmt->execute([$id]);

header("Location: admin.php");