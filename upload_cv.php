<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $job_id = $_POST['job_id'];

    $file = $_FILES['cv'];
    $filename = time() . "_" . $file['name'];

    move_uploaded_file($file['tmp_name'], "uploads/" . $filename);

    $stmt = $pdo->prepare("INSERT INTO applications (job_id, cv) VALUES (?, ?)");
    $stmt->execute([$job_id, $filename]);

    header("Location: jobs.php");
}