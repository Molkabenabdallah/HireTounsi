<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mon Profil — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
  --bg:#07080d; --s:#0e1018; --s2:#161824;
  --border:rgba(255,255,255,.07); --accent:#8b5cf6; --a2:#06b6d4;
  --text:#f0eeff; --muted:#7a7890; --muted2:#4a4860;
  --green:#34d399; --r:16px;
}
body { font-family:'Instrument Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
body::before {
  content:''; position:fixed; top:-200px; right:-150px; width:600px; height:600px; border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}
nav {
  position:sticky; top:0; z-index:100;
  display:flex; align-items:center; justify-content:space-between;
  padding:0 40px; height:64px; border-bottom:1px solid var(--border);
  background:rgba(7,8,13,.88); backdrop-filter:blur(20px);
}
.nav-logo { font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:20px; letter-spacing:-.5px; color:var(--text); text-decoration:none; }
.nav-logo span { color:var(--accent); }
.nav-right { display:flex; gap:10px; align-items:center; }
.nav-btn {
  display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:13px;
  text-decoration:none; transition:.2s; border:1px solid var(--border); background:var(--s2); color:var(--muted);
}
.nav-btn:hover { color:var(--text); border-color:rgba(139,92,246,.4); }
.nav-btn.primary {
  background:linear-gradient(135deg,var(--accent),#7c3aed); color:#fff; border:none;
  box-shadow:0 4px 14px rgba(139,92,246,.35);
}
.nav-btn.primary:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(139,92,246,.5); }
main { position:relative; z-index:1; max-width:860px; margin:0 auto; padding:40px 24px; }
.profile-hero {
  background:var(--s2); border:1px solid var(--border); border-radius:20px;
  padding:36px; margin-bottom:20px; display:flex; align-items:center; gap:28px; flex-wrap:wrap;
  position:relative; overflow:hidden; animation:up .45s ease both;
}
.profile-hero::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
}
.avatar { width:90px; height:90px; border-radius:20px; object-fit:cover; display:block; box-shadow:0 0 0 3px var(--s2),0 0 0 5px rgba(139,92,246,.35); }
.avatar-init {
  width:90px; height:90px; border-radius:20px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:32px;
  box-shadow:0 0 0 3px var(--s2),0 0 0 5px rgba(139,92,246,.3);
}
.profile-info { flex:1; min-width:0; }
.profile-name { font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:26px; letter-spacing:-.6px; margin-bottom:4px; }
.profile-job { font-size:14px; color:var(--accent); font-weight:600; margin-bottom:10px; }
.profile-meta { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:14px; }
.meta-chip { display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--muted); }
.verified-badge {
  display:inline-flex; align-items:center; gap:5px;
  background:rgba(52,211,153,.08); border:1px solid rgba(52,211,153,.2);
  color:var(--green); padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600;
}
.v-dot { width:6px; height:6px; border-radius:50%; background:var(--green); box-shadow:0 0 6px var(--green); }
.card {
  background:var(--s2); border:1px solid var(--border); border-radius:var(--r);
  padding:24px; margin-bottom:16px; animation:up .45s ease both;
}
.card:nth-child(1){animation-delay:.07s} .card:nth-child(2){animation-delay:.12s} .card:nth-child(3){animation-delay:.17s}
.card-title {
  font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:15px; letter-spacing:-.3px;
  margin-bottom:16px; display:flex; align-items:center; gap:8px;
}
.card-icon {
  width:30px; height:30px; border-radius:8px;
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent); flex-shrink:0;
}
.skills-wrap { display:flex; flex-wrap:wrap; gap:7px; }
.skill-tag {
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.25);
  color:#a78bfa; padding:5px 12px; border-radius:7px; font-size:12px; font-weight:600;
}
.cv-download {
  display:inline-flex; align-items:center; gap:8px;
  background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25);
  color:#67e8f9; padding:10px 18px; border-radius:10px;
  font-size:13px; font-weight:600; text-decoration:none; transition:.2s;
}
.cv-download:hover { background:rgba(6,182,212,.2); transform:translateY(-1px); }
.no-data {
  color:var(--muted2); font-size:13px; font-style:italic;
  background:var(--s); border:1px dashed var(--border);
  padding:16px; border-radius:10px; text-align:center;
}
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@keyframes up { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>
</head>
<body>
<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <div class="nav-right">
    <a href="dashboard.php" class="nav-btn">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Dashboard
    </a>
    <a href="edit_profile.php" class="nav-btn primary">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Modifier
    </a>
  </div>
</nav>
<main>
  <div class="profile-hero">
    <div>
      <?php if (!empty($user["profile_photo"])): ?>
        <img class="avatar" src="uploads/<?= htmlspecialchars($user["profile_photo"]) ?>" alt="Photo">
      <?php else: ?>
        <div class="avatar-init"><?= strtoupper(substr($user["name"], 0, 1)) ?></div>
      <?php endif; ?>
    </div>
    <div class="profile-info">
      <div class="profile-name"><?= htmlspecialchars($user["name"]) ?></div>
      <?php if (!empty($user["job_title"])): ?>
        <div class="profile-job"><?= htmlspecialchars($user["job_title"]) ?></div>
      <?php endif; ?>
      <div class="profile-meta">
        <span class="meta-chip">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <?= htmlspecialchars($user["email"]) ?>
        </span>
        <?php if (!empty($user["phone"])): ?>
        <span class="meta-chip">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <?= htmlspecialchars($user["phone"]) ?>
        </span>
        <?php endif; ?>
      </div>
      <div class="verified-badge"><span class="v-dot"></span> Profil vérifié</div>
    </div>
  </div>

  <div class="two-col">
    <div class="card">
      <div class="card-title">
        <div class="card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        À propos
      </div>
      <?php if (!empty($user["bio"])): ?>
        <p style="font-size:14px;color:var(--muted);line-height:1.7"><?= nl2br(htmlspecialchars($user["bio"])) ?></p>
      <?php else: ?>
        <div class="no-data">Aucune bio renseignée</div>
      <?php endif; ?>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
        Compétences
      </div>
      <?php if (!empty($user["skills"])): ?>
        <div class="skills-wrap">
          <?php foreach(array_filter(array_map('trim', explode(',', $user["skills"]))) as $sk): ?>
            <span class="skill-tag"><?= htmlspecialchars($sk) ?></span>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-data">Aucune compétence renseignée</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">
      <div class="card-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
      Mon CV
    </div>
    <?php if (!empty($user["cv"])): ?>
      <a class="cv-download" href="uploads/<?= htmlspecialchars($user["cv"]) ?>" download>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger mon CV
      </a>
    <?php else: ?>
      <div class="no-data">Aucun CV uploadé — <a href="edit_profile.php" style="color:var(--accent)">Ajouter un CV</a></div>
    <?php endif; ?>
  </div>
</main>
</body>
</html>