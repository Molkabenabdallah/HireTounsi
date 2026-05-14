<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();






$profilePhoto = !empty($user['profile_photo']) ? "uploads/" . $user['profile_photo'] : null;
$initials = strtoupper(substr($user['name'],0,1));


















$initials = strtoupper(substr($user['name'],0,1));
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
  --bg:     #07080d;
  --s:      #0e1018;
  --s2:     #161824;
  --border: rgba(255,255,255,.07);
  --accent: #8b5cf6;
  --a2:     #06b6d4;
  --text:   #f0eeff;
  --muted:  #7a7890;
  --muted2: #4a4860;
  --green:  #34d399;
  --sw:     230px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text);
  min-height:100vh; display:flex;
}

body::before {
  content:''; position:fixed; top:-200px; right:-100px;
  width:600px; height:600px; border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,.07) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ════ SIDEBAR ════ */
.sidebar {
  width:var(--sw); min-height:100vh;
  background:var(--s); border-right:1px solid var(--border);
  position:fixed; top:0; left:0;
  display:flex; flex-direction:column; z-index:50; padding:0;
}

.sidebar-logo {
  padding:22px 20px 18px;
  border-bottom:1px solid var(--border);
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:18px; letter-spacing:-.5px;
  color:var(--text); text-decoration:none;
  display:flex; align-items:center; gap:6px;
}
.sidebar-logo span { color:var(--accent); }

.sidebar-nav {
  flex:1; padding:16px 12px;
  display:flex; flex-direction:column; gap:3px;
}

.nav-sep {
  font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
  color:var(--muted2); padding:8px 8px 4px;
}

.sidebar a {
  display:flex; align-items:center; gap:10px;
  padding:9px 10px; border-radius:10px;
  color:var(--muted); text-decoration:none; font-size:13px; font-weight:500; transition:.2s;
}
.sidebar a:hover { background:rgba(255,255,255,.04); color:var(--text); }
.sidebar a.active {
  background:rgba(139,92,246,.12);
  border:1px solid rgba(139,92,246,.2); color:#c4b5fd;
}

.nav-icon {
  width:26px; height:26px; border-radius:7px;
  background:rgba(255,255,255,.04);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; flex-shrink:0;
}

.sidebar-bottom {
  padding:12px; border-top:1px solid var(--border);
}
.user-pill {
  display:flex; align-items:center; gap:9px; padding:10px;
  border-radius:10px; background:rgba(255,255,255,.03); border:1px solid var(--border);
}
.pill-av {
  width:32px; height:32px; border-radius:8px; flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:12px;
}
.pill-name { font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pill-role { font-size:10px; color:var(--muted); }
.logout-btn {
  color:var(--muted); text-decoration:none; padding:5px; border-radius:6px;
  display:flex; align-items:center; flex-shrink:0; transition:.2s; margin-left:auto;
}
.logout-btn:hover { color:#f87171; }

/* ════ MAIN ════ */
.main {
  margin-left:var(--sw); flex:1;
  padding:36px 28px; position:relative; z-index:1; min-height:100vh;
}

/* page header */
.page-header {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:28px; flex-wrap:wrap; gap:12px;
}
.page-header h2 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:26px; letter-spacing:-.6px;
}
.page-header h2 span {
  background:linear-gradient(90deg,var(--accent),var(--a2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}

.btn-edit {
  display:inline-flex; align-items:center; gap:7px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; padding:10px 20px; border-radius:11px; color:#fff; cursor:pointer;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:13px;
  text-decoration:none; transition:.2s; box-shadow:0 4px 14px rgba(139,92,246,.35);
}
.btn-edit:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(139,92,246,.5); }

/* grid */
.grid {
  display:grid; grid-template-columns:1fr 2fr; gap:18px; align-items:start;
}

/* card */
.card {
  background:var(--s2); border:1px solid var(--border); border-radius:16px;
  padding:22px; margin-bottom:16px;
  animation:up .45s ease both;
}
.card:last-child { margin-bottom:0; }
.card:nth-child(2){animation-delay:.07s}

/* avatar card */
.avatar-card { text-align:center; position:relative; overflow:hidden; }
.avatar-card::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
}
.avatar {
  width:80px;
  height:80px;
  border-radius:20px;
  overflow:hidden; /* important */
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:900;
  font-size:28px;
  font-family:'Cabinet Grotesk',sans-serif;
  margin:0 auto 14px;
  box-shadow:0 0 0 3px var(--s2),0 0 0 5px rgba(139,92,246,.3);
}





.avatar img {
  width:100%;
  height:100%;
  object-fit:cover;
}
.av-name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:18px; letter-spacing:-.4px; margin-bottom:4px;
}
.av-title { font-size:13px; color:var(--accent); font-weight:600; margin-bottom:6px; }
.av-email { font-size:12px; color:var(--muted); margin-bottom:14px; }

.divider { height:1px; background:var(--border); margin:14px 0; }

.verified-badge {
  display:inline-flex; align-items:center; gap:5px;
  background:rgba(52,211,153,.08); border:1px solid rgba(52,211,153,.2);
  color:var(--green); padding:4px 11px; border-radius:20px;
  font-size:11px; font-weight:600;
}
.vdot { width:5px; height:5px; border-radius:50%; background:var(--green); box-shadow:0 0 5px var(--green); }

/* info card */
.info-row {
  display:flex; align-items:center; gap:10px; padding:8px 0;
  border-bottom:1px solid var(--border);
}
.info-row:last-child { border-bottom:none; padding-bottom:0; }
.info-icon {
  width:28px; height:28px; border-radius:7px; flex-shrink:0;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.15);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.info-label { font-size:10px; color:var(--muted2); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:1px; }
.info-val { font-size:13px; font-weight:600; }

/* card title */
.card-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
  margin-bottom:16px; display:flex; align-items:center; gap:8px;
}
.ct-icon {
  width:28px; height:28px; border-radius:8px; flex-shrink:0;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.15);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}

/* progress */
.progress-header {
  display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;
}
.progress-pct {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:22px; color:var(--accent);
}
.progress-bar {
  height:6px; background:var(--s); border-radius:10px; overflow:hidden;
}
.progress {
  height:100%; border-radius:10px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
  transition:width .6s ease;
}
.progress-steps { display:flex; flex-direction:column; gap:8px; margin-top:14px; }
.pstep-row {
  display:flex; align-items:center; gap:8px; font-size:12px; color:var(--muted);
}
.pstep-row.done { color:var(--green); }
.pstep-check {
  width:18px; height:18px; border-radius:50%; flex-shrink:0;
  border:1.5px solid var(--border);
  display:flex; align-items:center; justify-content:center; font-size:9px;
}
.pstep-row.done .pstep-check {
  background:rgba(52,211,153,.15); border-color:rgba(52,211,153,.3); color:var(--green);
}

/* bio */
.bio-text { font-size:14px; color:#c4bfd8; line-height:1.75; }
.no-data {
  font-size:13px; color:var(--muted2); font-style:italic;
  background:var(--s); border:1px dashed var(--border);
  padding:14px; border-radius:10px; text-align:center;
}

/* skills */
.skills-wrap { display:flex; flex-wrap:wrap; gap:7px; }
.skill-tag {
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.25);
  color:#a78bfa; padding:5px 12px; border-radius:7px;
  font-size:12px; font-weight:600;
}

/* cv */
.cv-link {
  display:inline-flex; align-items:center; gap:8px;
  background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25);
  color:#67e8f9; padding:9px 16px; border-radius:10px;
  font-size:13px; font-weight:600; text-decoration:none; transition:.2s;
}
.cv-link:hover { background:rgba(6,182,212,.2); transform:translateY(-1px); }

/* right col anim stagger */
.right-col .card:nth-child(1){animation-delay:.05s}
.right-col .card:nth-child(2){animation-delay:.10s}
.right-col .card:nth-child(3){animation-delay:.15s}
.right-col .card:nth-child(4){animation-delay:.20s}

@keyframes up { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

/* ── RESPONSIVE ── */
@media(max-width:900px) {
  .sidebar { width:100%; min-height:auto; position:relative; flex-direction:row; flex-wrap:wrap; border-right:none; border-bottom:1px solid var(--border); }
  body { flex-direction:column; }
  .main { margin-left:0; padding:20px; }
  .grid { grid-template-columns:1fr; }
}
@media(max-width:640px) {
  .sidebar { display:none; }
  .main { padding:16px; }
}
</style>
</head>
<body>

<!-- ════ SIDEBAR ════ -->
<div class="sidebar">
  <a href="index.html" class="sidebar-logo">Hire<span>Tounsi</span></a>

  <div class="sidebar-nav">
    <span class="nav-sep">Navigation</span>
    <a href="dashboard.php">
      <span class="nav-icon">🏠</span> Dashboard
    </a>
    <a href="profile.php" class="active">
      <span class="nav-icon">👤</span> Mon profil
    </a>
    <a href="mes_offres.php">
      <span class="nav-icon">📋</span> Mes offres
    </a>
  </div>

  <div class="sidebar-bottom">
    <div class="user-pill">
      <div class="pill-av"><?= $initials ?></div>
      <div style="flex:1;min-width:0">
        <div class="pill-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="pill-role"><?= htmlspecialchars($user['job_title'] ?: 'Membre') ?></div>
      </div>
      <a href="logout.php" class="logout-btn" title="Déconnexion">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
        </svg>
      </a>
    </div>
  </div>
</div>

<!-- ════ MAIN ════ -->
<div class="main">

  <div class="page-header">
    <h2>Mon <span>Profil</span></h2>
    <a href="edit_profile.php" class="btn-edit">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Modifier le profil
    </a>
  </div>

  <div class="grid">

    <!-- ── LEFT ── -->
    <div>

      <!-- Avatar card -->
      <div class="card avatar-card">








       <div class="avatar">
  <?php if ($profilePhoto): ?>
    <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Profile photo">
  <?php else: ?>
    <?= $initials ?>
  <?php endif; ?>
</div>









        <div class="av-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="av-title"><?= htmlspecialchars($user['job_title'] ?: 'Aucun titre') ?></div>
        <div class="av-email"><?= htmlspecialchars($user['email']) ?></div>
        <div class="verified-badge"><span class="vdot"></span> Profil vérifié</div>
      </div>

      <!-- Info card -->
      <div class="card">
        <div class="card-title">
          <div class="ct-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          Informations
        </div>

        <div class="info-row">
          <div class="info-icon">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07"/></svg>
          </div>
          <div>
            <div class="info-label">Téléphone</div>
            <div class="info-val"><?= htmlspecialchars($user['phone'] ?: 'Non renseigné') ?></div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <div class="info-label">Email</div>
            <div class="info-val"><?= htmlspecialchars($user['email']) ?></div>
          </div>
        </div>

      </div>

    </div>

    <!-- ── RIGHT ── -->
    <div class="right-col">

      <!-- Progress card -->
      <div class="card">
        <div class="card-title">
          <div class="ct-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          Complétez votre profil
        </div>

        <?php
        $fields = [
            !empty($user['profile_photo']),
            !empty($user['cv']),
            !empty($user['skills']),
            !empty($user['bio']),
            !empty($user['job_title'])
        ];
        $progress = intval(array_sum($fields)/count($fields)*100);
        ?>

        <div class="progress-header">
          <span style="font-size:13px;color:var(--muted)">Progression</span>
          <span class="progress-pct"><?= $progress ?>%</span>
        </div>
        <div class="progress-bar">
          <div class="progress" style="width:<?= $progress ?>%"></div>
        </div>

        <div class="progress-steps">
          <div class="pstep-row <?= !empty($user['profile_photo'])?'done':'' ?>">
            <div class="pstep-check"><?= !empty($user['profile_photo'])?'✓':'' ?></div>
            Photo de profil
          </div>
          <div class="pstep-row <?= !empty($user['cv'])?'done':'' ?>">
            <div class="pstep-check"><?= !empty($user['cv'])?'✓':'' ?></div>
            CV uploadé
          </div>
          <div class="pstep-row <?= !empty($user['skills'])?'done':'' ?>">
            <div class="pstep-check"><?= !empty($user['skills'])?'✓':'' ?></div>
            Compétences renseignées
          </div>
          <div class="pstep-row <?= !empty($user['bio'])?'done':'' ?>">
            <div class="pstep-check"><?= !empty($user['bio'])?'✓':'' ?></div>
            Bio ajoutée
          </div>
          <div class="pstep-row <?= !empty($user['job_title'])?'done':'' ?>">
            <div class="pstep-check"><?= !empty($user['job_title'])?'✓':'' ?></div>
            Titre / Métier
          </div>
        </div>
      </div>

      <!-- Bio card -->
      <div class="card">
        <div class="card-title">
          <div class="ct-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          Bio
        </div>
        <?php if(!empty($user['bio'])): ?>
          <p class="bio-text"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
        <?php else: ?>
          <div class="no-data">Aucune bio — <a href="edit_profile.php" style="color:var(--accent)">Ajouter une bio</a></div>
        <?php endif; ?>
      </div>

      <!-- Skills card -->
      <div class="card">
        <div class="card-title">
          <div class="ct-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          Compétences
        </div>
        <?php if(!empty($user['skills'])): ?>
          <div class="skills-wrap">
            <?php foreach(array_filter(array_map('trim', explode(',', $user['skills']))) as $sk): ?>
              <span class="skill-tag"><?= htmlspecialchars($sk) ?></span>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="no-data">Aucune compétence — <a href="edit_profile.php" style="color:var(--accent)">En ajouter</a></div>
        <?php endif; ?>
      </div>

      <!-- CV card -->
      <div class="card">
        <div class="card-title">
          <div class="ct-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          Mon CV
        </div>
        <?php if($user['cv']): ?>
          <a class="cv-link" href="uploads/<?= htmlspecialchars($user['cv']) ?>" target="_blank">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir mon CV
          </a>
        <?php else: ?>
          <div class="no-data">Aucun CV — <a href="edit_profile.php" style="color:var(--accent)">Uploader un CV</a></div>
        <?php endif; ?>
      </div>

    </div>

  </div>
</div>

</body>
</html>