<?php
session_start();
include "config.php";

$id = $_GET["id"];

$pdo->prepare("UPDATE talents SET status='approved' WHERE id=?")
    ->execute([$id]);

header("Location: admin.php");
exit();