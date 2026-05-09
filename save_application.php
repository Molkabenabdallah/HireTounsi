<?php
session_start();
include "config.php";

// ── AUTH ──
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// ── ONLY POST ──
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: jobs.php");
    exit();
}

// ── GET DATA ──
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
$cover_letter = trim($_POST['cover_letter'] ?? '');
$experience_years = trim($_POST['experience_years'] ?? '');
$has_experience = isset($_POST['has_experience']) ? 1 : 0;
$availability = trim($_POST['availability'] ?? '');

// ── VALIDATION ──
$errors = [];

if ($job_id <= 0) {
    $errors[] = "Offre invalide.";
}

// Check if job exists and is approved
$check = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND status = 'approved'");
$check->execute([$job_id]);
if (!$check->fetch()) {
    $errors[] = "Cette offre n'existe pas ou n'est pas disponible.";
}

// Check if already applied
$check2 = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
$check2->execute([$user_id, $job_id]);
if ($check2->fetch()) {
    $_SESSION['flash_error'] = "Vous avez déjà postulé à cette offre.";
    header("Location: mes_candidatures.php");
    exit();
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(" ", $errors);
    header("Location: jobs.php");
    exit();
}

// ── OPTIONAL CV UPLOAD IN STEP 2 ──
$cv_filename = null;
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['cv'];
    $allowedExts = ['pdf', 'doc', 'docx'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowedExts) && $file['size'] <= 5 * 1024 * 1024) {
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
        $cv_filename = time() . "_" . $safeName;

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        move_uploaded_file($file['tmp_name'], $uploadDir . $cv_filename);
    }
}

// ── SAVE APPLICATION ──
try {
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (user_id, job_id, cv, cover_letter, has_experience, experience_years, availability, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $stmt->execute([
        $user_id,
        $job_id,
        $cv_filename,
        $cover_letter,
        $has_experience,
        $experience_years,
        $availability
    ]);

    $_SESSION['flash_success'] = "Votre candidature a été soumise avec succès !";
    header("Location: mes_candidatures.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Erreur lors de la soumission : " . $e->getMessage();
    header("Location: jobs.php");
    exit();
}