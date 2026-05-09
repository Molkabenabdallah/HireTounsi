<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $pdo->prepare("
        INSERT INTO jobs (title, company, salary, city, description, contract_type, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $_POST['title'],
        $_POST['company'],
        $_POST['salary'],
        $_POST['city'],
        $_POST['description'],
        $_POST['contract_type'] // ✅ AJOUT IMPORTANT
    ]);

    header("Location: jobs.php?msg=waiting");
}