<?php
session_start();
include "config.php";

// ── VÉRIFICATION AUTH ──
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ── VÉRIFICATION REQUÊTE POST ──
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: jobs.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

// ── VALIDATION ──
$errors = [];

if ($job_id <= 0) {
    $errors[] = "Offre d'emploi invalide.";
}

if (!isset($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "Veuillez sélectionner un fichier CV.";
} elseif ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Erreur lors de l'upload du fichier.";
}

// ── VÉRIFICATION TYPE FICHIER ──
$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$allowedExts = ['pdf', 'doc', 'docx'];

if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['cv'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExts)) {
        $errors[] = "Format non supporté. Utilisez PDF, DOC ou DOCX.";
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        $errors[] = "Fichier trop volumineux (max 5MB).";
    }
}

// ── SI ERREURS → REDIRECT AVEC MESSAGE ──
if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(" ", $errors);
    header("Location: jobs.php");
    exit();
}

// ── UPLOAD FICHIER ──
$file = $_FILES['cv'];
$safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
$filename = time() . "_" . $safeName;

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    $_SESSION['flash_error'] = "Erreur lors de la sauvegarde du fichier.";
    header("Location: jobs.php");
    exit();
}

// ── VÉRIFICATION JOB EXISTE ──
$check = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND status = 'approved'");
$check->execute([$job_id]);
if (!$check->fetch()) {
    $_SESSION['flash_error'] = "Cette offre n'existe pas ou n'est pas encore approuvée.";
    header("Location: jobs.php");
    exit();
}

// ── VÉRIFICATION PAS DÉJÀ CANDIDATÉ ──
$check2 = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
$check2->execute([$user_id, $job_id]);
if ($check2->fetch()) {
    $_SESSION['flash_error'] = "Vous avez déjà postulé à cette offre.";
    header("Location: mes_candidatures.php");
    exit();
}

// ── ENREGISTRER CANDIDATURE ──
try {
    $stmt = $pdo->prepare("
        INSERT INTO applications (user_id, job_id, cv, created_at)
        VALUES (?, ?, ?, NOW())
    ");

    $result = $stmt->execute([$user_id, $job_id, $filename]);

    if ($result) {
        $_SESSION['flash_success'] = "Votre candidature a été envoyée avec succès !";
        header("Location: mes_candidatures.php");
        exit();
    } else {
        $_SESSION['flash_error'] = "Erreur lors de l'enregistrement de la candidature.";
        header("Location: jobs.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Erreur base de données : " . $e->getMessage();
    header("Location: jobs.php");
    exit();
}