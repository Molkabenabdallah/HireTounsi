<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// récupérer user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $bio = $_POST["bio"];
    $skills = $_POST["skills"];
    $job = $_POST["job_title"];

    // upload photo
    if (!empty($_FILES["photo"]["name"])) {
        $photoName = time() . "_" . $_FILES["photo"]["name"];
        move_uploaded_file($_FILES["photo"]["tmp_name"], "uploads/" . $photoName);

        $pdo->prepare("UPDATE users SET profile_photo=? WHERE id=?")
            ->execute([$photoName, $userId]);
    }

    // upload cv
    if (!empty($_FILES["cv"]["name"])) {
        $cvName = time() . "_" . $_FILES["cv"]["name"];
        move_uploaded_file($_FILES["cv"]["tmp_name"], "uploads/" . $cvName);

        $pdo->prepare("UPDATE users SET cv=? WHERE id=?")
            ->execute([$cvName, $userId]);
    }

    // update data
    $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, bio=?, skills=?, job_title=? WHERE id=?");
    $stmt->execute([$name, $phone, $bio, $skills, $job, $userId]);

    header("Location: profile.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Modifier Profil — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
  --bg:#07080d; --s:#0e1018; --s2:#161824;
  --border:rgba(255,255,255,.07); --accent:#8b5cf6; --a2:#06b6d4;
  --text:#f0eeff; --muted:#7a7890; --muted2:#4a4860; --r:14px;
}
body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text); min-height:100vh;
}
body::before {
  content:''; position:fixed; top:-200px; right:-150px; width:600px; height:600px; border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* NAV */
nav {
  position:sticky; top:0; z-index:100;
  display:flex; align-items:center; justify-content:space-between;
  padding:0 40px; height:64px; border-bottom:1px solid var(--border);
  background:rgba(7,8,13,.88); backdrop-filter:blur(20px);
}
.nav-logo { font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:20px; letter-spacing:-.5px; color:var(--text); text-decoration:none; }
.nav-logo span { color:var(--accent); }
.nav-back {
  display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:13px;
  text-decoration:none; transition:.2s; border:1px solid var(--border); background:var(--s2); color:var(--muted);
}
.nav-back:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* MAIN */
main {
  position:relative; z-index:1;
  max-width:680px; margin:0 auto; padding:40px 24px;
}

/* CARD */
.form-card {
  background:var(--s2); border:1px solid var(--border); border-radius:20px;
  padding:36px; position:relative; overflow:hidden;
  animation:up .45s ease both;
}
.form-card::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
}

.form-header { margin-bottom:32px; }
.form-header h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:24px; letter-spacing:-.6px; margin-bottom:5px;
}
.form-header p { font-size:13px; color:var(--muted); }

/* AVATAR PREVIEW */
.avatar-section {
  display:flex; align-items:center; gap:20px; margin-bottom:28px;
  padding:20px; background:var(--s); border:1px solid var(--border);
  border-radius:14px;
}
.avatar-preview {
  width:72px; height:72px; border-radius:16px; flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:26px;
  overflow:hidden;
}
.avatar-preview img { width:100%; height:100%; object-fit:cover; display:none; }
.avatar-info { flex:1; }
.avatar-info p { font-size:13px; font-weight:600; margin-bottom:4px; }
.avatar-info span { font-size:11px; color:var(--muted2); }

/* SECTIONS */
.form-section-title {
  font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
  color:var(--muted2); margin-bottom:14px; margin-top:24px;
  display:flex; align-items:center; gap:8px;
}
.form-section-title::after { content:''; flex:1; height:1px; background:var(--border); }

/* FIELDS */
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

.field { margin-bottom:16px; }
.field label {
  display:block; font-size:10px; font-weight:700; letter-spacing:.07em;
  text-transform:uppercase; color:var(--muted); margin-bottom:7px;
}
.field input, .field textarea, .field select {
  width:100%; padding:12px 14px;
  background:var(--s); border:1px solid var(--border);
  border-radius:10px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none; transition:.2s;
}
.field input::placeholder, .field textarea::placeholder { color:var(--muted2); }
.field input:focus, .field textarea:focus {
  border-color:rgba(139,92,246,.6); box-shadow:0 0 0 3px rgba(139,92,246,.1);
}
.field textarea { resize:none; line-height:1.6; }
.field .hint { font-size:11px; color:var(--muted2); margin-top:5px; }

/* FILE INPUT */
.file-zone {
  width:100%; padding:16px;
  background:var(--s); border:1px dashed rgba(139,92,246,.3);
  border-radius:10px; cursor:pointer; transition:.2s;
  display:flex; align-items:center; gap:12px;
  position:relative;
}
.file-zone:hover { border-color:rgba(139,92,246,.6); background:rgba(139,92,246,.04); }
.file-zone input[type="file"] {
  position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
  background:none; border:none; padding:0;
}
.file-zone input[type="file"]:focus { box-shadow:none; }
.file-icon {
  width:36px; height:36px; border-radius:9px; flex-shrink:0;
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.file-txt p { font-size:13px; font-weight:600; margin-bottom:2px; }
.file-txt span { font-size:11px; color:var(--muted2); }

/* SKILLS HINT */
.skills-preview { display:flex; flex-wrap:wrap; gap:5px; margin-top:8px; }
.sk-tag {
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.2);
  color:#a78bfa; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:600;
}

/* SUBMIT */
.divider { height:1px; background:var(--border); margin:28px 0; }
.submit-row { display:flex; gap:12px; }
.btn-submit {
  flex:1; padding:14px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; border-radius:11px; color:#fff;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:15px;
  cursor:pointer; transition:.2s; box-shadow:0 4px 18px rgba(139,92,246,.35);
}
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(139,92,246,.5); }
.btn-cancel {
  display:flex; align-items:center; justify-content:center; gap:6px;
  padding:14px 22px; border-radius:11px;
  background:var(--s); border:1px solid var(--border); color:var(--muted);
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:14px;
  text-decoration:none; transition:.2s;
}
.btn-cancel:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

@keyframes up { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body>

<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <a href="profile.php" class="nav-back">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
    Retour au profil
  </a>
</nav>

<main>
<div class="form-card">

  <div class="form-header">
    <h1>Modifier votre profil</h1>
    <p>Mettez à jour vos informations pour attirer les recruteurs</p>
  </div>

  <!-- Avatar preview -->
  <div class="avatar-section">
    <div class="avatar-preview" id="avatarPreview">
      <?php if (!empty($user["profile_photo"])): ?>
        <img id="previewImg" src="uploads/<?= htmlspecialchars($user["profile_photo"]) ?>" style="display:block">
      <?php else: ?>
        <?= strtoupper(substr($user["name"], 0, 1)) ?>
      <?php endif; ?>
    </div>
    <div class="avatar-info">
      <p>Photo de profil</p>
      <span>JPG, PNG · Max 2MB</span>
    </div>
  </div>

  <form method="POST" enctype="multipart/form-data">

    <div class="form-section-title">Informations personnelles</div>

    <div class="form-row">
      <div class="field">
        <label>Nom complet</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Votre nom">
      </div>
      <div class="field">
        <label>Téléphone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+216 XX XXX XXX">
      </div>
    </div>

    <div class="field">
      <label>Titre / Métier</label>
      <input type="text" name="job_title" value="<?= htmlspecialchars($user['job_title'] ?? '') ?>" placeholder="Ex : Développeur Full-Stack, Designer UI/UX…">
    </div>

    <div class="field">
      <label>Bio</label>
      <textarea name="bio" rows="4" placeholder="Présentez-vous en quelques lignes…"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>

    <div class="field">
      <label>Compétences</label>
      <input type="text" name="skills" id="skillsInput" value="<?= htmlspecialchars($user['skills'] ?? '') ?>" placeholder="Ex : React, PHP, Figma, Python…">
      <div class="hint">Séparez par des virgules</div>
      <div class="skills-preview" id="skillsPreview"></div>
    </div>

    <div class="form-section-title">Fichiers</div>

    <div class="field">
      <label>Photo de profil</label>
      <div class="file-zone">
        <div class="file-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        </div>
        <div class="file-txt">
          <p>Choisir une photo</p>
          <span>JPG, PNG, WEBP</span>
        </div>
        <input type="file" name="photo" accept="image/*" id="photoInput">
      </div>
    </div>

    <div class="field">
      <label>CV</label>
      <div class="file-zone">
        <div class="file-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="file-txt" id="cvLabel">
          <p><?= !empty($user['cv']) ? 'Remplacer le CV' : 'Choisir un CV' ?></p>
          <span>PDF, DOCX</span>
        </div>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" id="cvInput">
      </div>
    </div>

    <div class="divider"></div>

    <div class="submit-row">
      <a href="profile.php" class="btn-cancel">Annuler</a>
      <button type="submit" class="btn-submit">Enregistrer les modifications →</button>
    </div>

  </form>
</div>
</main>

<script>
// Live skills preview
const skillsInput = document.getElementById('skillsInput');
const skillsPreview = document.getElementById('skillsPreview');

function renderSkills() {
  const vals = skillsInput.value.split(',').map(s => s.trim()).filter(Boolean);
  skillsPreview.innerHTML = vals.map(s => `<span class="sk-tag">${s}</span>`).join('');
}
skillsInput.addEventListener('input', renderSkills);
renderSkills();

// Photo preview
document.getElementById('photoInput').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('avatarPreview');
    preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:16px">`;
  };
  reader.readAsDataURL(file);
});

// CV label update
document.getElementById('cvInput').addEventListener('change', function() {
  if (this.files[0]) {
    document.getElementById('cvLabel').querySelector('p').textContent = this.files[0].name;
  }
});
</script>

</body>
</html>