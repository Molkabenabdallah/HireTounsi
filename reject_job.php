<?php
include "config.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE jobs SET status='rejected' WHERE id=?");
$stmt->execute([$id]);

header("Location: admin.php");