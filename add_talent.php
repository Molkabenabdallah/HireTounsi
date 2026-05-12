<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = $_POST["name"] ?? "";
    $skill       = $_POST["skill"] ?? "";
    $city        = $_POST["city"] ?? "";
    $email       = $_POST["email"] ?? "";
    $phone       = $_POST["phone"] ?? "";
    $description = $_POST["description"] ?? "";

    $photo = "";

    if (!empty($_FILES["photo"]["name"])) {

        $photo = time() . "_" . $_FILES["photo"]["name"];

        move_uploaded_file(
            $_FILES["photo"]["tmp_name"],
            "uploads/" . $photo
        );
    }

    $stmt = $pdo->prepare("
        INSERT INTO talents
        (
            user_id,
            name,
            skill,
            city,
            email,
            phone,
            description,
            photo,
            status
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?,
            'pending'
        )
    ");

    $stmt->execute([
        $_SESSION["user_id"],
        $name,
        $skill,
        $city,
        $email,
        $phone,
        $description,
        $photo
    ]);

    header("Location: talents.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ajouter Talent — HireTounsi</title>

<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

:root{
--bg:#07080d;
--s:#11131b;
--s2:#181b26;
--border:rgba(255,255,255,.07);
--accent:#8b5cf6;
--a2:#06b6d4;
--text:#f3f4ff;
--muted:#7a7890;
}

body{
font-family:'Instrument Sans',sans-serif;
background:var(--bg);
color:var(--text);
min-height:100vh;
display:flex;
align-items:center;
justify-content:center;
padding:30px;
overflow:hidden;
}

body::before{
content:'';
position:fixed;
top:-200px;
right:-200px;
width:600px;
height:600px;
border-radius:50%;
background:radial-gradient(circle,rgba(139,92,246,.12),transparent 70%);
}

body::after{
content:'';
position:fixed;
bottom:-200px;
left:-200px;
width:500px;
height:500px;
border-radius:50%;
background:radial-gradient(circle,rgba(6,182,212,.08),transparent 70%);
}

.wizard{
width:100%;
max-width:760px;
background:var(--s2);
border:1px solid var(--border);
border-radius:28px;
overflow:hidden;
position:relative;
z-index:2;
box-shadow:0 30px 80px rgba(0,0,0,.6);
}

.top{
padding:28px;
border-bottom:1px solid var(--border);
}

.top h1{
font-family:'Cabinet Grotesk',sans-serif;
font-size:30px;
font-weight:900;
letter-spacing:-1px;
margin-bottom:6px;
}

.top h1 span{
background:linear-gradient(90deg,var(--accent),var(--a2));
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
}

.top p{
color:var(--muted);
font-size:14px;
}

.progress{
display:flex;
gap:12px;
padding:24px 28px 0;
}

.step-dot{
flex:1;
height:6px;
border-radius:20px;
background:#252837;
transition:.3s;
}

.step-dot.active{
background:linear-gradient(90deg,var(--accent),var(--a2));
}

.form{
padding:30px 28px;
}

.step{
display:none;
animation:fade .35s ease;
}

.step.active{
display:block;
}

@keyframes fade{
from{
opacity:0;
transform:translateY(10px);
}
to{
opacity:1;
transform:none;
}
}

.step-title{
font-family:'Cabinet Grotesk',sans-serif;
font-size:24px;
font-weight:800;
margin-bottom:6px;
}

.step-sub{
font-size:14px;
color:var(--muted);
margin-bottom:24px;
}

.field{
margin-bottom:18px;
}

label{
display:block;
margin-bottom:8px;
font-size:12px;
font-weight:700;
text-transform:uppercase;
letter-spacing:.06em;
color:var(--muted);
}

input,
textarea{
width:100%;
padding:14px 16px;
background:var(--s);
border:1px solid var(--border);
border-radius:14px;
color:var(--text);
font-size:14px;
outline:none;
transition:.2s;
}

input:focus,
textarea:focus{
border-color:var(--accent);
box-shadow:0 0 0 4px rgba(139,92,246,.12);
}

textarea{
resize:none;
min-height:120px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:16px;
}

.upload{
border:2px dashed rgba(139,92,246,.3);
padding:34px;
border-radius:18px;
text-align:center;
background:rgba(139,92,246,.04);
position:relative;
cursor:pointer;
}

.upload input{
position:absolute;
inset:0;
opacity:0;
cursor:pointer;
}

.upload h3{
margin-bottom:8px;
font-size:16px;
}

.upload p{
font-size:13px;
color:var(--muted);
}

.footer{
display:flex;
justify-content:space-between;
align-items:center;
padding:22px 28px;
border-top:1px solid var(--border);
}

.btn{
border:none;
padding:13px 22px;
border-radius:14px;
cursor:pointer;
font-weight:700;
font-size:14px;
transition:.2s;
}

.btn-prev{
background:#232635;
color:#fff;
}

.btn-next,
.btn-submit{
background:linear-gradient(135deg,var(--accent),#7c3aed);
color:#fff;
box-shadow:0 10px 25px rgba(139,92,246,.35);
}

.btn:hover{
transform:translateY(-2px);
}

.hidden{
display:none;
}

@media(max-width:700px){

.grid{
grid-template-columns:1fr;
}

body{
padding:14px;
}

}

</style>
</head>
<body>

<div class="wizard">

<div class="top">
<h1>Ajouter un <span>Talent</span></h1>
<p>Complétez les étapes pour publier votre profil.</p>
</div>

<div class="progress">
<div class="step-dot active"></div>
<div class="step-dot"></div>
<div class="step-dot"></div>
<div class="step-dot"></div>
</div>

<form method="POST" enctype="multipart/form-data">

<div class="form">

<!-- STEP 1 -->
<div class="step active">

<div class="step-title">Informations personnelles</div>
<div class="step-sub">Ajoutez votre identité professionnelle.</div>

<div class="field">
<label>Nom complet</label>
<input type="text" name="name" required>
</div>

<div class="field">
<label>Compétences</label>
<input type="text" name="skill" placeholder="UI/UX, PHP, Unity..." required>
</div>

<div class="field">
<label>Ville</label>
<input type="text" name="city" placeholder="Tunis, Sfax..." required>
</div>

</div>

<!-- STEP 2 -->
<div class="step">

<div class="step-title">Coordonnées</div>
<div class="step-sub">Les recruteurs pourront vous contacter.</div>

<div class="grid">

<div class="field">
<label>Email</label>
<input type="email" name="email">
</div>

<div class="field">
<label>Téléphone</label>
<input type="text" name="phone">
</div>

</div>

</div>

<!-- STEP 3 -->
<div class="step">

<div class="step-title">Présentation</div>
<div class="step-sub">Parlez de votre expérience.</div>

<div class="field">
<label>Description</label>
<textarea name="description"></textarea>
</div>

</div>

<!-- STEP 4 -->
<div class="step">

<div class="step-title">Photo de profil</div>
<div class="step-sub">Ajoutez une photo professionnelle.</div>

<div class="upload">
<input type="file" name="photo" accept="image/*">
<h3>Importer une image</h3>
<p>PNG, JPG ou WEBP</p>
</div>

</div>

</div>

<div class="footer">

<button type="button" class="btn btn-prev hidden" id="prevBtn">
Retour
</button>

<div style="display:flex;gap:12px">

<button type="button" class="btn btn-next" id="nextBtn">
Suivant
</button>

<button type="submit" class="btn btn-submit hidden" id="submitBtn">
Publier profil
</button>

</div>

</div>

</form>

</div>

<script>

const steps = document.querySelectorAll(".step");
const dots  = document.querySelectorAll(".step-dot");

const prevBtn   = document.getElementById("prevBtn");
const nextBtn   = document.getElementById("nextBtn");
const submitBtn = document.getElementById("submitBtn");

let current = 0;

function updateWizard(){

steps.forEach((step,index)=>{
step.classList.toggle("active",index===current);
});

dots.forEach((dot,index)=>{
dot.classList.toggle("active",index<=current);
});

prevBtn.classList.toggle("hidden",current===0);

nextBtn.classList.toggle("hidden",current===steps.length-1);

submitBtn.classList.toggle("hidden",current!==steps.length-1);

}

nextBtn.addEventListener("click",()=>{

if(current < steps.length-1){
current++;
updateWizard();
}

});

prevBtn.addEventListener("click",()=>{

if(current > 0){
current--;
updateWizard();
}

});

</script>

</body>
</html>