<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["role"])) {
    $_SESSION["active_role"] = $_GET["role"];
}

// redirection selon rôle
if ($_SESSION["active_role"] === "recruteur") {
    header("Location: recruiter_dashboard.php");
} else {
    header("Location: dashboard.php");
}
exit();