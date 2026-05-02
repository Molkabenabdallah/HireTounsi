<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Ajouter Profil</title>

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

form{
  background:#14142c;
  padding:25px;
  border-radius:12px;
  width:300px;
}

input,textarea{
  width:100%;
  padding:10px;
  margin:8px 0;
  background:#0c0c18;
  color:white;
  border:1px solid #333;
}

button{
  width:100%;
  padding:10px;
  background:#7c3aed;
  border:none;
  color:white;
}
</style>
</head>

<body>

<form method="POST" action="save_profile.php">
  <h3>Ajouter Profil</h3>

  <input name="name" placeholder="Nom">
  <input name="skill" placeholder="Skill">
  <textarea name="description" placeholder="Description"></textarea>

  <button>Envoyer</button>
</form>

</body>
</html>