<?php
include "config.php";

$id = $_GET["id"];

$stmt = $pdo->prepare("
UPDATE companies
SET status='rejected'
WHERE id=?
");

$stmt->execute([$id]);

header("Location: admin.php?page=companies");
exit();
?>