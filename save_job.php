<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $pdo->prepare("INSERT INTO jobs (title, company, salary, city, description, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    
    $stmt->execute([
        $_POST['title'],
        $_POST['company'],
        $_POST['salary'],
        $_POST['city'],
        $_POST['description']
    ]);

    header("Location: jobs.php?msg=waiting");
}