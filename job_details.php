<?php
session_start();
include "config.php";

if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND status='approved'");
$stmt->execute([$id]);

$job = $stmt->fetch();

if (!$job) {
    die("Offre introuvable");
}

$questions = [];

if (!empty($job['questions'])) {
    $questions = json_decode($job['questions'], true);
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($job['title']) ?> — HireTounsi</title>
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
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}
body::after {
  content:''; position:fixed; bottom:-200px; left:-100px;
  width:500px; height:500px; border-radius:50%;
  background:radial-gradient(circle,rgba(6,182,212,.05) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ── NAV ── */
nav {
  position:sticky; top:0; z-index:100;
  display:flex; align-items:center; justify-content:space-between;
  padding:0 40px; height:64px;
  border-bottom:1px solid var(--border);
  background:rgba(7,8,13,.88); backdrop-filter:blur(20px);
}
.nav-logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:20px; letter-spacing:-.5px;
  color:var(--text); text-decoration:none;
}
.nav-logo span { color:var(--accent); }
.back-btn {
  display:inline-flex; align-items:center; gap:6px;
  text-decoration:none; color:var(--muted);
  background:var(--s2); padding:8px 16px;
  border-radius:10px; border:1px solid var(--border);
  font-size:13px; font-weight:600; transition:.2s;
}
.back-btn:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── LAYOUT ── */
.wrapper {
  position:relative; z-index:1;
  max-width:1100px; margin:0 auto; padding:40px 24px;
  display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start;
}

/* ════ MAIN CONTENT ════ */
.job-main { display:flex; flex-direction:column; gap:16px; }

/* job hero card */
.job-hero {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; padding:32px; position:relative; overflow:hidden;
  animation:up .4s ease both;
}
.job-hero::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(90deg,var(--accent),var(--a2));
}

.company-row {
  display:flex; align-items:center; gap:12px; margin-bottom:18px;
}
.company-logo {
  width:48px; height:48px; border-radius:12px;
  background:linear-gradient(135deg,rgba(245,158,11,.2),rgba(239,68,68,.1));
  border:1px solid rgba(245,158,11,.2);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:18px;
  color:#fbbf24; flex-shrink:0;
}
.company-name { font-size:15px; color:var(--muted); font-weight:600; }

.job-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:clamp(24px,4vw,36px);
  letter-spacing:-1px; line-height:1.1; margin-bottom:18px;
}

/* badges */
.badges { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:24px; }
.badge {
  display:inline-flex; align-items:center; gap:6px;
  padding:6px 13px; border-radius:8px;
  font-size:13px; font-weight:600;
}
.badge-salary   { background:rgba(52,211,153,.1);  border:1px solid rgba(52,211,153,.25);  color:#4ade80; }
.badge-contract { background:rgba(139,92,246,.1);  border:1px solid rgba(139,92,246,.25);  color:#a78bfa; }
.badge-city     { background:rgba(6,182,212,.1);   border:1px solid rgba(6,182,212,.25);   color:#67e8f9; }

/* section */
.section {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; padding:28px;
  animation:up .4s ease both;
}
.section:nth-child(2){animation-delay:.06s}
.section:nth-child(3){animation-delay:.10s}

.section-header {
  display:flex; align-items:center; gap:10px; margin-bottom:20px;
}
.section-icon {
  width:34px; height:34px; border-radius:9px;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent); flex-shrink:0;
}
.section-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:16px; letter-spacing:-.3px;
}

.description {
  font-size:15px; line-height:1.85; color:#c8c3de; white-space:pre-line;
}

/* questions */
.questions { display:flex; flex-direction:column; gap:10px; }
.question {
  background:var(--s); border:1px solid var(--border);
  padding:16px; border-radius:12px; transition:.15s;
}
.question:hover { border-color:rgba(139,92,246,.3); }
.question h4 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:700; font-size:14px; margin-bottom:6px;
}
.q-type {
  display:inline-flex; align-items:center; gap:4px;
  font-size:11px; color:var(--a2); font-weight:600;
  background:rgba(6,182,212,.08); border:1px solid rgba(6,182,212,.2);
  padding:2px 8px; border-radius:20px;
}

/* ════ SIDEBAR ════ */
.job-sidebar { display:flex; flex-direction:column; gap:14px; position:sticky; top:84px; }

.sidebar-card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:18px; padding:22px;
  animation:up .4s ease both; animation-delay:.08s;
}

/* apply button */
.apply-btn {
  display:flex; align-items:center; justify-content:center; gap:8px;
  width:100%; padding:14px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff; text-decoration:none; border:none; cursor:pointer;
  border-radius:12px; font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; transition:.2s;
  box-shadow:0 4px 18px rgba(139,92,246,.4);
}
.apply-btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(139,92,246,.55); }

.apply-note {
  text-align:center; margin-top:10px; font-size:11px; color:var(--muted2);
}

/* job meta list */
.meta-divider { height:1px; background:var(--border); margin:16px 0; }
.meta-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:14px; letter-spacing:-.2px; margin-bottom:14px;
}
.meta-row {
  display:flex; align-items:center; gap:10px; margin-bottom:12px;
}
.meta-row:last-child { margin-bottom:0; }
.meta-icon {
  width:30px; height:30px; border-radius:8px; flex-shrink:0;
  background:rgba(139,92,246,.08); border:1px solid rgba(139,92,246,.15);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.meta-label { font-size:10px; color:var(--muted2); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:2px; }
.meta-val { font-size:13px; font-weight:600; }

/* share */
.share-title {
  font-size:12px; font-weight:700; color:var(--muted); text-align:center; margin-bottom:12px;
  text-transform:uppercase; letter-spacing:.06em;
}
.share-btns { display:flex; gap:8px; }
.share-btn {
  flex:1; display:flex; align-items:center; justify-content:center; gap:5px;
  padding:9px; border-radius:9px;
  background:var(--s); border:1px solid var(--border);
  color:var(--muted); font-size:12px; font-weight:600;
  text-decoration:none; transition:.2s; cursor:pointer;
}
.share-btn:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ════ MODALS ════ */
.modal {
  display:none; position:fixed; inset:0; z-index:200;
  background:rgba(0,0,0,.65); backdrop-filter:blur(6px);
  align-items:center; justify-content:center; padding:20px;
}
.modal.open { display:flex; }
.modal-content {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; width:100%; max-width:440px; padding:32px;
  position:relative; animation:modalIn .3s cubic-bezier(.4,0,.2,1);
  box-shadow:0 24px 60px rgba(0,0,0,.6);
}
.modal-content::before {
  content:''; position:absolute; top:0; left:30px; right:30px; height:1px;
  background:linear-gradient(90deg,transparent,rgba(139,92,246,.6),transparent);
}
@keyframes modalIn {
  from{opacity:0;transform:translateY(20px) scale(.97)}
  to{opacity:1;transform:translateY(0) scale(1)}
}
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.modal-title { font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:20px; letter-spacing:-.4px; }
.close {
  width:32px; height:32px; border-radius:8px;
  background:var(--s); border:1px solid var(--border);
  color:var(--muted); font-size:18px; cursor:pointer;
  display:flex; align-items:center; justify-content:center; transition:.2s;
}
.close:hover { color:var(--text); border-color:var(--accent); }
.modal label {
  display:block; font-size:11px; font-weight:600; letter-spacing:.06em;
  text-transform:uppercase; color:var(--muted); margin-bottom:6px; margin-top:14px;
}
.modal label:first-of-type { margin-top:0; }
.modal input, .modal textarea, .modal select {
  width:100%; padding:12px 14px;
  background:var(--s); border:1px solid var(--border);
  border-radius:9px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none; transition:.2s;
}
.modal input::placeholder, .modal textarea::placeholder { color:var(--muted2); }
.modal input:focus, .modal textarea:focus, .modal select:focus {
  border-color:rgba(139,92,246,.6); box-shadow:0 0 0 3px rgba(139,92,246,.1);
}
.modal textarea { height:100px; resize:none; line-height:1.6; }
.modal select {
  appearance:none; cursor:pointer;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7890' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 14px center; padding-right:36px;
}
.modal select option { background:var(--s2); }
.modal input[type="file"] { padding:10px 14px; cursor:pointer; color:var(--muted); }
.modal input[type="file"]::file-selector-button {
  background:rgba(139,92,246,.15); border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; border-radius:6px; padding:4px 10px;
  font-family:'Instrument Sans',sans-serif; font-size:12px; font-weight:600;
  cursor:pointer; margin-right:10px; transition:.2s;
}
.modal-divider { height:1px; background:var(--border); margin:20px 0; }
.btn-modal {
  width:100%; padding:13px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; border-radius:10px; color:#fff;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:15px;
  cursor:pointer; transition:.2s; box-shadow:0 4px 18px rgba(139,92,246,.3); margin-top:20px;
}
.btn-modal:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(139,92,246,.45); }

.btn-manual{
  width:100%;
  margin-top:12px;
  padding:13px;
  background:transparent;
  border:1px solid var(--border);
  border-radius:10px;
  color:var(--muted);
  cursor:pointer;
  font-family:'Instrument Sans',sans-serif;
  font-size:14px;
  transition:.2s;
}
.btn-manual:hover{
  border-color:var(--accent);
  color:var(--text);
}

@keyframes up { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

@media(max-width:900px) {
  .wrapper { grid-template-columns:1fr; }
  .job-sidebar { position:relative; top:auto; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <a href="jobs.php" class="back-btn">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
    Retour aux offres
  </a>
</nav>

<div class="wrapper">

  <!-- ════ MAIN ════ -->
  <div class="job-main">

    <!-- Hero card -->
    <div class="job-hero">
      <div class="company-row">
        <div class="company-logo"><?= strtoupper(substr($job['company'], 0, 1)) ?></div>
        <div class="company-name"><?= htmlspecialchars($job['company']) ?></div>
      </div>

      <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>

      <div class="badges">
        <?php if($job['salary']): ?>
        <span class="badge badge-salary">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <?= htmlspecialchars($job['salary']) ?> TND
        </span>
        <?php endif; ?>

        <?php if(!empty($job['contract_type'])): ?>
        <span class="badge badge-contract">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
          <?= htmlspecialchars($job['contract_type']) ?>
        </span>
        <?php endif; ?>

        <?php if($job['city']): ?>
        <span class="badge badge-city">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <?= htmlspecialchars($job['city']) ?>
        </span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Description -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="section-title">Description du poste</div>
      </div>
      <div class="description"><?= nl2br(htmlspecialchars($job['description'])) ?></div>
    </div>

    <!-- Questions -->
    <?php if(!empty($questions)): ?>
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
        </div>
        <div class="section-title">Questions de candidature</div>
      </div>
      <div class="questions">
        <?php foreach($questions as $q): ?>
        <div class="question">
          <h4><?= htmlspecialchars($q['question']) ?></h4>
          <span class="q-type">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <?= htmlspecialchars($q['type']) ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- ════ SIDEBAR ════ -->
  <div class="job-sidebar">

    <!-- Apply card -->
    <div class="sidebar-card">
      <?php if($job['external_apply'] == 1 && !empty($job['external_url'])): ?>
        <a href="<?= htmlspecialchars($job['external_url']) ?>" target="_blank" class="apply-btn">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Postuler sur le site externe
        </a>
        <p class="apply-note">Vous serez redirigé vers le site de l'entreprise</p>
      <?php else: ?>
        <button class="apply-btn" id="applyBtn" data-job-id="<?= $job['id'] ?>">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
          Postuler maintenant
        </button>
        <p class="apply-note">Candidature directe via HireTounsi</p>
      <?php endif; ?>

      <div class="meta-divider"></div>
      <div class="meta-title">Détails de l'offre</div>

      <?php if($job['company']): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/></svg>
        </div>
        <div>
          <div class="meta-label">Entreprise</div>
          <div class="meta-val"><?= htmlspecialchars($job['company']) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if($job['city']): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
          <div class="meta-label">Ville</div>
          <div class="meta-val"><?= htmlspecialchars($job['city']) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if($job['salary']): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div>
          <div class="meta-label">Salaire</div>
          <div class="meta-val"><?= htmlspecialchars($job['salary']) ?> TND</div>
        </div>
      </div>
      <?php endif; ?>

      <?php if(!empty($job['contract_type'])): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
        </div>
        <div>
          <div class="meta-label">Contrat</div>
          <div class="meta-val"><?= htmlspecialchars($job['contract_type']) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if(!empty($job['created_at'])): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="meta-label">Publiée le</div>
          <div class="meta-val"><?= date('d/m/Y', strtotime($job['created_at'])) ?></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Share card -->
    <div class="sidebar-card">
      <div class="share-title">Partager cette offre</div>
      <div class="share-btns">
        <button class="share-btn" onclick="navigator.clipboard.writeText(window.location.href).then(()=>this.textContent='Copié ✓')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
          Copier le lien
        </button>
        <a class="share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>" target="_blank">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
          LinkedIn
        </a>
      </div>
    </div>

  </div>
</div>

<!-- ════ MODAL CV ════ -->
<div id="cvModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-title">Envoyer votre CV</span>
      <button class="close" id="closeCv">&times;</button>
    </div>
    <form action="upload_cv.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="job_id" id="job_id" value="<?= $job['id'] ?>">
      <label>Votre CV (PDF, DOCX)</label>
      <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
      <div class="modal-divider"></div>
      <button class="btn-modal" type="submit">Importer mon CV</button>
      <button type="button" class="btn-manual" id="openApplyStep2">Compléter manuellement</button>
    </form>
  </div>
</div>

<!-- ════ MODAL ETAPE 2 ════ -->
<div id="applyModal" class="modal">
  <div class="modal-content" style="max-width:700px;">
    <div class="modal-header">
      <span class="modal-title">Finaliser ma candidature</span>
      <button class="close" id="closeApply">&times;</button>
    </div>
    <form action="save_application.php" method="POST">
      <input type="hidden" name="job_id" value="<?= $job['id'] ?>">

      <label>Lettre de motivation</label>
      <textarea name="cover_letter" placeholder="Présentez-vous et expliquez pourquoi ce poste vous intéresse..."></textarea>

      <div class="modal-divider"></div>

      <label>Avez-vous une expérience ?</label>
      <div style="display:flex;gap:10px;margin-top:10px;">
        <button type="button" class="btn-modal" id="expYes" style="flex:1;">Oui</button>
        <button type="button" class="btn-manual" id="expNo" style="flex:1;margin-top:0;">Non</button>
      </div>

      <div id="experienceFields" style="display:none;">
        <label style="margin-top:20px;">Années d'expérience</label>
        <select name="experience_years">
          <option value="">Sélectionner...</option>
          <option value="<1">Moins d'1 an</option>
          <option value="1-3">1 - 3 ans</option>
          <option value="3-5">3 - 5 ans</option>
          <option value="5+">Plus de 5 ans</option>
        </select>
      </div>

      <input type="hidden" name="has_experience" id="hasExperience" value="0">

      <label style="margin-top:20px;">Disponibilité</label>
      <input type="text" name="availability" placeholder="Ex : Immédiatement">

      <button class="btn-modal" type="submit" style="margin-top:25px;">
        Soumettre ma candidature
      </button>
    </form>
  </div>
</div>

<script>
  const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

  document.addEventListener("DOMContentLoaded", () => {

    /* ─── MODALS ─── */
    const cvModal     = document.getElementById("cvModal");
    const applyModal  = document.getElementById("applyModal");

    /* OPEN APPLY MODAL */
    const applyBtn = document.getElementById("applyBtn");
    if (applyBtn) {
      applyBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (!isLoggedIn) {
          window.location.href = "login.php";
          return;
        }
        cvModal.classList.add("open");
      });
    }

    /* CLOSE CV MODAL */
    document.getElementById("closeCv").addEventListener("click", () => {
      cvModal.classList.remove("open");
    });

    /* CLOSE APPLY MODAL */
    document.getElementById("closeApply").addEventListener("click", () => {
      applyModal.classList.remove("open");
    });

    /* OPEN STEP 2 MODAL (manual apply) */
    document.getElementById("openApplyStep2").addEventListener("click", () => {
      cvModal.classList.remove("open");
      applyModal.classList.add("open");
    });

    /* CLOSE WHEN CLICK OUTSIDE */
    window.addEventListener("click", (e) => {
      if (e.target === cvModal) cvModal.classList.remove("open");
      if (e.target === applyModal) applyModal.classList.remove("open");
    });

    /* ─── EXPERIENCE TOGGLE ─── */
    const expYes = document.getElementById("expYes");
    const expNo  = document.getElementById("expNo");
    const expFields = document.getElementById("experienceFields");
    const hasExp = document.getElementById("hasExperience");

    if (expYes && expNo) {
      expYes.addEventListener("click", () => {
        expFields.style.display = "block";
        hasExp.value = "1";
        expYes.style.opacity = "1";
        expNo.style.opacity = "0.5";
      });

      expNo.addEventListener("click", () => {
        expFields.style.display = "none";
        hasExp.value = "0";
        expYes.style.opacity = "0.5";
        expNo.style.opacity = "1";
      });
    }

  });
</script>

</body>
</html>