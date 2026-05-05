<?php
session_start();
include "config.php";

/* 🔒 SECURITY */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

/* 🔎 GET ID */
if (!isset($_GET["id"])) {
    header("Location: admin.php?page=users");
    exit();
}

$id = intval($_GET["id"]);

/* 📥 FETCH USER */
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable");
}

/* ✏️ UPDATE USER */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $role  = $_POST["role"];

    if ($name && $email) {
        $update = $pdo->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        $update->execute([$name, $email, $role, $id]);

        header("Location: admin.php?page=users&success=updated");
        exit();
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier utilisateur</title>

<style>
body{
  background:#0c0c18;
  color:white;
  font-family:Arial;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}

.form-box{
  background:#14142c;
  padding:30px;
  border-radius:12px;
  width:350px;
}

h2{
  margin-bottom:20px;
  color:#a78bfa;
}

input, select{
  width:100%;
  padding:10px;
  margin-bottom:12px;
  border:none;
  border-radius:8px;
  background:#1a1a38;
  color:white;
}

button{
  width:100%;
  padding:10px;
  background:#8b5cf6;
  border:none;
  border-radius:8px;
  color:white;
  cursor:pointer;
}

button:hover{
  background:#7c3aed;
}

.error{
  color:#ef4444;
  margin-bottom:10px;
}
</style>

</head>

<body>

<div class="form-box">

<h2>Modifier utilisateur</h2>

<?php if(isset($error)): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

  <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" placeholder="Nom">

  <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" placeholder="Email">

  <select name="role">
    <option value="user" <?= $user["role"]=="user"?"selected":"" ?>>User</option>
    <option value="admin" <?= $user["role"]=="admin"?"selected":"" ?>>Admin</option>
  </select>

  <button type="submit">Enregistrer</button>

</form>

</div>

</body>
</html>