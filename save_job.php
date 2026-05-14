<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_id      = $_POST['company_id'] ?? null;
    $title           = $_POST['title'] ?? '';
    $company         = $_POST['company'] ?? '';
    $salary          = $_POST['salary'] ?? '';
    $salary_max      = $_POST['salary_max'] ?? '';
    $city            = $_POST['city'] ?? '';
    $description     = $_POST['description'] ?? '';
    $contract_type   = $_POST['contract_type'] ?? '';
    $category        = $_POST['category'] ?? '';
    $experience_level = $_POST['experience_level'] ?? '';
    $recruiter_type  = $_POST['recruiter_type'] ?? '';
    $external_apply  = $_POST['external_apply'] ?? 0;
    $external_url    = $_POST['external_url'] ?? '';
    $questions       = $_POST['questions'] ?? [];
    $question_types  = $_POST['question_types'] ?? [];

    /* QUESTIONS JSON */
    $formattedQuestions = [];
    if (!empty($questions)) {
        foreach ($questions as $index => $question) {
            if (trim($question) != "") {
                $formattedQuestions[] = [
                    "question" => $question,
                    "type" => $question_types[$index] ?? "text"
                ];
            }
        }
    }
    $questions_json = json_encode($formattedQuestions);

    /* INSERT */
    $stmt = $pdo->prepare("
        INSERT INTO jobs (
            user_id,
            company_id,
            title,
            company,
            salary,
            salary_max,
            city,
            description,
            contract_type,
            category,
            experience_level,
            recruiter_type,
            external_apply,
            external_url,
            questions,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $user_id,
        $company_id,
        $title,
        $company,
        $salary,
        $salary_max,
        $city,
        $description,
        $contract_type,
        $category,
        $experience_level,
        $recruiter_type,
        $external_apply,
        $external_url,
        $questions_json
    ]);

    header("Location: jobs.php?msg=waiting");
    exit();
}
?>