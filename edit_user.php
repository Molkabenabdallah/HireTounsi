<?php
session_start();
include "config.php";

if ($_SESSION["role"] != "admin") {
    die("Accès refusé");
}

$id = $_GET["id"];

// récupérer user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = $_POST["name"];
    $email = $_POST["email"];
    $role  = $_POST["role"];

    $update = $pdo->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $update->execute([$name, $email, $role, $id]);

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Modifier User</title>
</head>

<body style="background:#0c0c18;color:white;font-family:Arial;padding:40px;">

<h2>Modifier utilisateur</h2>

<form method="POST">
  <input type="text" name="name" value="<?= $user["name"] ?>"><br><br>
  <input type="email" name="email" value="<?= $user["email"] ?>"><br><br>

  <select name="role">
    <option value="user" <?= $user["role"]=="user"?"selected":"" ?>>User</option>
    <option value="admin" <?= $user["role"]=="admin"?"selected":"" ?>>Admin</option>
  </select><br><br>

  <button type="submit">Enregistrer</button>
</form>

</body>
</html>