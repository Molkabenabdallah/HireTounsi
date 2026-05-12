<?php
include "config.php";

if (!isset($_GET["id"])) {
    header("Location: talents.php");
    exit();
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM talents WHERE id=?");
$stmt->execute([$id]);

$talent = $stmt->fetch();

if (!$talent) {
    die("Talent introuvable");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($talent["name"]) ?> — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
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
}

body {
  background:var(--bg); color:var(--text);
  font-family:'Instrument Sans',sans-serif;
  min-height:100vh; overflow-x:hidden;
}

/* glows */
body::before {
  content:''; position:fixed; top:-200px; right:-150px;
  width:600px; height:600px; border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,.09) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}
body::after {
  content:''; position:fixed; bottom:-180px; left:-100px;
  width:500px; height:500px; border-radius:50%;
  background:radial-gradient(circle,rgba(6,182,212,.06) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ── NAV ── */
nav {
  height:64px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between;
  padding:0 40px;
  background:rgba(7,8,13,.88); backdrop-filter:blur(20px);
  position:sticky; top:0; z-index:100;
}
.logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-size:20px; font-weight:900; letter-spacing:-.5px;
  text-decoration:none; color:var(--text);
}
.logo span { color:var(--accent); }

.back-btn {
  display:inline-flex; align-items:center; gap:6px;
  text-decoration:none; color:var(--muted);
  background:var(--s2); padding:8px 16px;
  border-radius:10px; border:1px solid var(--border);
  font-size:13px; font-weight:600; transition:.2s;
}
.back-btn:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── WRAPPER ── */
.wrapper {
  position:relative; z-index:1;
  max-width:1200px; margin:0 auto; padding:40px 28px;
  display:grid; grid-template-columns:280px 1fr; gap:24px; align-items:start;
}

/* ════════════════════════════════
   SIDEBAR
════════════════════════════════ */
.sidebar {
  position:sticky; top:84px;
  display:flex; flex-direction:column; gap:14px;
}

/* profile card */
.profile-card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; padding:28px; text-align:center;
  position:relative; overflow:hidden;
  animation:up .4s ease both;
}
.profile-card::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
}

.avatar {
  width:88px; height:88px; border-radius:20px;
  margin:0 auto 16px;
  background:linear-gradient(135deg,var(--accent),var(--a2));
  display:flex; align-items:center; justify-content:center;
  font-size:30px; font-weight:900; font-family:'Cabinet Grotesk',sans-serif;
  box-shadow:0 0 0 3px var(--s2),0 0 0 5px rgba(139,92,246,.3);
  overflow:hidden;
}
.avatar img { width:100%; height:100%; object-fit:cover; }

.name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-size:22px; font-weight:900; letter-spacing:-.5px; margin-bottom:4px;
}
.role { font-size:13px; color:var(--accent); font-weight:600; margin-bottom:12px; }

.location-row {
  display:inline-flex; align-items:center; gap:6px;
  font-size:13px; color:var(--muted); margin-bottom:18px;
}

.verified-badge {
  display:inline-flex; align-items:center; gap:5px;
  background:rgba(52,211,153,.08); border:1px solid rgba(52,211,153,.2);
  color:var(--green); padding:5px 12px; border-radius:20px;
  font-size:12px; font-weight:600; margin-bottom:20px;
}
.vdot { width:6px; height:6px; border-radius:50%; background:var(--green); box-shadow:0 0 6px var(--green); }

.divider { height:1px; background:var(--border); margin:18px 0; }

/* info rows */
.info-row {
  display:flex; align-items:flex-start; gap:10px;
  text-align:left; margin-bottom:12px;
}
.info-row:last-child { margin-bottom:0; }
.info-icon {
  width:30px; height:30px; border-radius:8px; flex-shrink:0;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.15);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.info-label { font-size:10px; color:var(--muted2); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:2px; }
.info-val { font-size:13px; font-weight:600; }

/* actions */
.actions {
  display:flex; gap:8px; margin-top:4px;
}
.btn-primary {
  flex:1; display:inline-flex; align-items:center; justify-content:center; gap:7px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff; text-decoration:none; padding:11px 16px; border-radius:11px;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:13px;
  transition:.2s; box-shadow:0 4px 14px rgba(139,92,246,.35); border:none; cursor:pointer;
}
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(139,92,246,.5); }
.btn-secondary {
  display:inline-flex; align-items:center; justify-content:center; gap:7px;
  background:var(--s2); border:1px solid var(--border);
  color:var(--text); text-decoration:none; padding:11px 16px; border-radius:11px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:13px; transition:.2s;
}
.btn-secondary:hover { border-color:rgba(139,92,246,.4); color:var(--accent); }

/* sidebar bottom card */
.sidebar-meta {
  background:var(--s2); border:1px solid var(--border);
  border-radius:16px; padding:18px;
  animation:up .4s ease both; animation-delay:.07s;
}
.sidebar-meta-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:13px; letter-spacing:-.2px; margin-bottom:14px;
}

/* ════════════════════════════════
   CONTENT
════════════════════════════════ */
.content { display:flex; flex-direction:column; gap:16px; }

.section {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; padding:28px;
  animation:up .4s ease both;
}
.section:nth-child(2){animation-delay:.06s}
.section:nth-child(3){animation-delay:.10s}
.section:nth-child(4){animation-delay:.14s}

.section-header {
  display:flex; align-items:center; gap:10px; margin-bottom:20px;
}
.section-icon {
  width:36px; height:36px; border-radius:10px;
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent); flex-shrink:0;
}
.section-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-size:18px; font-weight:900; letter-spacing:-.4px;
}

/* about */
.about { font-size:14px; line-height:1.8; color:#c4bfd8; }

/* skills */
.skills { display:flex; flex-wrap:wrap; gap:8px; }
.skill {
  padding:6px 14px; border-radius:8px;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.25);
  color:#a78bfa; font-size:13px; font-weight:600; transition:.15s;
}
.skill:hover { background:rgba(139,92,246,.2); border-color:rgba(139,92,246,.5); }

/* timeline */
.timeline { display:flex; flex-direction:column; gap:12px; }
.timeline-item {
  padding:18px; border-radius:14px;
  background:var(--s); border:1px solid var(--border);
  display:flex; gap:14px; align-items:flex-start;
}
.tl-dot {
  width:36px; height:36px; border-radius:10px; flex-shrink:0;
  background:linear-gradient(135deg,rgba(139,92,246,.2),rgba(6,182,212,.1));
  border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.tl-content h4 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px; margin-bottom:5px;
}
.tl-content p { font-size:13px; color:var(--muted); line-height:1.6; }

/* contact grid */
.contact-grid {
  display:grid; grid-template-columns:1fr 1fr; gap:12px;
}
.contact-box {
  background:var(--s); border:1px solid var(--border);
  padding:16px; border-radius:12px; transition:.15s;
}
.contact-box:hover { border-color:rgba(139,92,246,.3); }
.contact-box span {
  display:block; font-size:10px; color:var(--muted2);
  font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:5px;
}
.contact-box b { font-size:14px; font-weight:600; }

@keyframes up {
  from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)}
}

@media(max-width:900px) {
  .wrapper { grid-template-columns:1fr; }
  .sidebar { position:relative; top:auto; }
  .contact-grid { grid-template-columns:1fr; }
}
</style>
</head>
<body>

<nav>
  <a href="index.html" class="logo">Hire<span>Tounsi</span></a>
  <a href="talents.php" class="back-btn">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
    Retour aux talents
  </a>
</nav>

<div class="wrapper">

  <!-- ════ SIDEBAR ════ -->
  <div class="sidebar">

    <div class="profile-card">

      <div class="avatar">
        <?php if(!empty($talent["photo"])): ?>
          <img src="uploads/<?= htmlspecialchars($talent["photo"]) ?>" alt="photo">
        <?php else: ?>
          <?= strtoupper(substr($talent["name"],0,2)) ?>
        <?php endif; ?>
      </div>

      <div class="name"><?= htmlspecialchars($talent["name"]) ?></div>
      <div class="role"><?= htmlspecialchars($talent["skill"]) ?></div>

      <div class="location-row">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        <?= htmlspecialchars($talent["city"] ?? "Tunisie") ?>
      </div>

      <div class="verified-badge">
        <span class="vdot"></span> Profil vérifié
      </div>

      <div class="divider"></div>

      <div class="info-row">
        <div class="info-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div>
          <div class="info-label">Email</div>
          <div class="info-val"><?= htmlspecialchars($talent["email"] ?? "Non renseigné") ?></div>
        </div>
      </div>

      <div class="info-row">
        <div class="info-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div>
          <div class="info-label">Téléphone</div>
          <div class="info-val"><?= htmlspecialchars($talent["phone"] ?? "Non renseigné") ?></div>
        </div>
      </div>

      <div class="info-row">
        <div class="info-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
          <div class="info-label">Ville</div>
          <div class="info-val"><?= htmlspecialchars($talent["city"] ?? "Tunisie") ?></div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="actions">
        <a href="#" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07"/></svg>
          Contacter
        </a>
        <a href="#" class="btn-secondary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          CV
        </a>
      </div>

    </div>

  </div>

  <!-- ════ CONTENT ════ -->
  <div class="content">

    <!-- À propos -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="section-title">À propos</div>
      </div>
      <div class="about">
        <?= nl2br(htmlspecialchars($talent["description"] ?? "Aucune description disponible.")) ?>
      </div>
    </div>

    <!-- Compétences -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="section-title">Compétences</div>
      </div>
      <div class="skills">
        <?php
          $skills = explode(",", $talent["skill"]);
          foreach($skills as $skill):
            if(trim($skill)):
        ?>
          <div class="skill"><?= htmlspecialchars(trim($skill)) ?></div>
        <?php endif; endforeach; ?>
      </div>
    </div>

    <!-- Expérience -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div class="section-title">Expérience</div>
      </div>
      <div class="timeline">
        <div class="timeline-item">
          <div class="tl-dot">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          <div class="tl-content">
            <h4><?= htmlspecialchars($talent["skill"]) ?></h4>
            <p>Talent vérifié sur HireTounsi avec compétences professionnelles dans le domaine mentionné.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div class="section-title">Contact</div>
      </div>
      <div class="contact-grid">
        <div class="contact-box">
          <span>Email</span>
          <b><?= htmlspecialchars($talent["email"] ?? "Non renseigné") ?></b>
        </div>
        <div class="contact-box">
          <span>Téléphone</span>
          <b><?= htmlspecialchars($talent["phone"] ?? "Non renseigné") ?></b>
        </div>
        <div class="contact-box">
          <span>Ville</span>
          <b><?= htmlspecialchars($talent["city"] ?? "Tunisie") ?></b>
        </div>
        <div class="contact-box">
          <span>Compétence principale</span>
          <b><?= htmlspecialchars($talent["skill"]) ?></b>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>