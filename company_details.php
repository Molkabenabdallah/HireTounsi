<?php
session_start();
include "config.php";

if(!isset($_GET["id"])){
    die("Entreprise introuvable");
}

$id = (int) $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM companies WHERE id=?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if(!$company){
    die("Entreprise inexistante");
}

/* =========================
   OFFRES DE L'ENTREPRISE
========================= */
$jobsStmt = $pdo->prepare("
    SELECT *
    FROM jobs
    WHERE (company_id = ? OR company = ?)
    AND status = 'approved'
    ORDER BY id DESC
");
$jobsStmt->execute([$id, $company["name"]]);
$jobs = $jobsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($company["name"]) ?> — HireTounsi</title>
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
  background:var(--s2); padding:8px 16px; border-radius:10px;
  border:1px solid var(--border); font-size:13px; font-weight:600; transition:.2s;
}
.back-btn:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── WRAPPER ── */
.wrapper {
  position: relative;
  z-index: 1;
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 24px;
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 24px;
  align-items: start;
}

/* ════ MAIN ════ */
.main { display:flex; flex-direction:column; gap:16px; min-width:0; overflow:hidden; }

/* ── HERO CARD ── */
.hero-card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:22px; overflow:hidden;
  animation:up .4s ease both;
  min-width:0;
}

.banner {
  height:180px; position:relative;
  background-size:cover;
  background-position:center;
  background-repeat:no-repeat;
}

/* particles on banner */
.banner::after {
  content:'';
  position:absolute; inset:0;
  background:url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='30' cy='30' r='1' fill='rgba(255,255,255,.15)'/%3E%3C/svg%3E") repeat;
  opacity:.4;
  pointer-events: none;
}

.company-logo {
  width:90px; height:90px; border-radius:20px;
  border:3px solid var(--s2);
  position:absolute; bottom:-38px; left:32px; z-index:2;
  background:var(--s); overflow:hidden;
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:32px;
  box-shadow:0 8px 24px rgba(0,0,0,.5);
}
.company-logo img { width:100%; height:100%; object-fit:cover; }

.hero-body { padding:56px 32px 28px; overflow:hidden; }

.company-name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:28px; letter-spacing:-.7px; margin-bottom:4px;
}
.company-sector { font-size:14px; color:var(--accent); font-weight:600; margin-bottom:14px; }

.chips { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px; }
.chip {
  display:inline-flex; align-items:center; gap:5px;
  padding:5px 12px; border-radius:7px; font-size:12px; font-weight:600;
}
.chip-loc  { background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25); color:#67e8f9; }
.chip-size { background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.25); color:#a78bfa; }
.chip-status {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
}
.sdot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.s-approved { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); color:#4ade80; }
.s-pending  { background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.3); color:#fbbf24; }
.s-rejected { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); color:#f87171; }

.hero-divider { height:1px; background:var(--border); margin:20px 0; }

.company-desc {
  font-size:14px; line-height:1.85; color:#c4bfd8; white-space:pre-line;
  overflow-wrap:break-word; word-break:break-word; max-width:100%;
}

/* ── SECTION CARD ── */
.section {
  background:var(--s2); border:1px solid var(--border);
  border-radius:18px; padding:24px;
  animation:up .4s ease both;
}
.section:nth-child(2){animation-delay:.07s}
.section:nth-child(3){animation-delay:.12s}

.section-header {
  display:flex; align-items:center; gap:10px; margin-bottom:20px;
}
.section-icon {
  width:32px; height:32px; border-radius:9px;
  background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center; color:var(--accent); flex-shrink:0;
}
.section-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
}

/* info grid */
.info-grid {
  display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px;
}
.info-box {
  background:var(--s); border:1px solid var(--border);
  border-radius:12px; padding:16px; transition:.15s;
}
.info-box:hover { border-color:rgba(139,92,246,.3); }
.info-label {
  font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
  color:var(--muted2); margin-bottom:5px;
}
.info-val { font-size:14px; font-weight:600; }
.info-val a { color:var(--accent); text-decoration:none; }
.info-val a:hover { text-decoration:underline; }

/* ════ SIDEBAR ════ */
.sidebar { display:flex; flex-direction:column; gap:14px; position:sticky; top:84px; }

.side-card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:18px; padding:22px;
  animation:up .4s ease both; animation-delay:.1s;
}

.website-btn {
  display:flex; align-items:center; justify-content:center; gap:8px;
  width:100%; padding:13px; border-radius:12px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff; text-decoration:none; border:none;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:14px;
  transition:.2s; box-shadow:0 4px 16px rgba(139,92,246,.4); cursor:pointer;
  margin-bottom:14px;
}
.website-btn:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(139,92,246,.5); }

.side-divider { height:1px; background:var(--border); margin:14px 0; }

.side-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:14px; letter-spacing:-.2px; margin-bottom:14px;
}

.meta-row {
  display:flex; align-items:flex-start; gap:10px; margin-bottom:12px;
}
.meta-row:last-child { margin-bottom:0; }
.meta-icon {
  width:28px; height:28px; border-radius:7px; flex-shrink:0;
  background:rgba(139,92,246,.08); border:1px solid rgba(139,92,246,.15);
  display:flex; align-items:center; justify-content:center; color:var(--accent);
}
.meta-label { font-size:10px; color:var(--muted2); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:2px; }
.meta-val { font-size:13px; font-weight:600; word-break:break-word; }

/* contact card */
.contact-rows { display:flex; flex-direction:column; gap:10px; }
.contact-row {
  display:flex; align-items:center; gap:10px;
  padding:10px; border-radius:10px;
  background:var(--s); border:1px solid var(--border); transition:.15s;
}
.contact-row:hover { border-color:rgba(139,92,246,.3); }
.cr-icon {
  width:32px; height:32px; border-radius:9px; flex-shrink:0;
  background:rgba(6,182,212,.08); border:1px solid rgba(6,182,212,.2);
  display:flex; align-items:center; justify-content:center; color:var(--a2);
}
.cr-label { font-size:10px; color:var(--muted2); font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:2px; }
.cr-val { font-size:13px; font-weight:600; word-break:break-word; }

@keyframes up { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

/* ════ JOBS SECTION ════ */
.jobs-section {
  grid-column: 1 / -1;
  margin-top: 8px;
}

.jobs-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:16px;
}

.job-card{
  background:var(--s);
  border:1px solid var(--border);
  border-radius:16px;
  padding:18px;
  transition:.2s;
}

.job-card:hover{
  transform:translateY(-3px);
  border-color:rgba(139,92,246,.35);
}

.job-title{
  font-family:'Cabinet Grotesk',sans-serif;
  font-size:18px;
  font-weight:800;
  margin-bottom:8px;
}

.job-meta{
  font-size:13px;
  color:var(--muted);
  margin-bottom:12px;
}

.job-desc{
  font-size:13px;
  line-height:1.7;
  color:#cfcbe2;
  margin-bottom:16px;
}

.job-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:10px 16px;
  border-radius:10px;
  text-decoration:none;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff;
  font-weight:700;
  font-size:13px;
}

.no-jobs {
  text-align:center;
  padding:40px 20px;
  color:var(--muted);
  font-size:14px;
}

@media(max-width:900px) {
  .wrapper { grid-template-columns:1fr; }
  .sidebar { position:relative; top:auto; }
  .info-grid { grid-template-columns:1fr 1fr; }
  .jobs-section { grid-column: 1; }
}
@media(max-width:500px) {
  .info-grid { grid-template-columns:1fr; }
  .hero-body { padding:52px 20px 22px; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <a href="companies.php" class="back-btn">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="m15 18-6-6 6-6"/>
    </svg>
    Retour
  </a>
</nav>
<div class="wrapper">

  <!-- ════ MAIN ════ -->
  <div class="main">

    <!-- Hero card -->
    <div class="hero-card">
      <div class="banner" style="
        <?php
        echo !empty($company['cover'])
          ? "background-image:url('uploads/" . htmlspecialchars($company['cover']) . "');"
          : "background:linear-gradient(135deg,#2d1b69,#8b5cf6 50%,#06b6d4);";
        ?>
      ">
        <div class="company-logo">
          <?php if(!empty($company["logo"])): ?>
            <img src="uploads/<?= htmlspecialchars($company["logo"]) ?>">
          <?php else: ?>
            <?= strtoupper(substr($company["name"],0,1)) ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="hero-body">
        <div class="company-name"><?= htmlspecialchars($company["name"]) ?></div>
        <div class="company-sector"><?= htmlspecialchars($company["sector"] ?? '') ?></div>

        <div class="chips">
          <?php if(!empty($company["location"])): ?>
          <span class="chip chip-loc">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= htmlspecialchars($company["location"]) ?>
          </span>
          <?php endif; ?>
          <?php if(!empty($company["company_size"])): ?>
          <span class="chip chip-size">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <?= htmlspecialchars($company["company_size"]) ?>
          </span>
          <?php endif; ?>
          <?php if(!empty($company["status"])): ?>
          <span class="chip-status s-<?= $company["status"] ?>">
            <span class="sdot"></span><?= ucfirst($company["status"]) ?>
          </span>
          <?php endif; ?>
        </div>

        <?php if(!empty($company["description"])): ?>
          <div class="hero-divider"></div>
          <div class="company-desc"><?= nl2br(htmlspecialchars($company["description"])) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Informations section -->
    <div class="section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/></svg>
        </div>
        <div class="section-title">Informations de l'entreprise</div>
      </div>

      <div class="info-grid">
        <div class="info-box">
          <div class="info-label">Localisation</div>
          <div class="info-val"><?= htmlspecialchars($company["location"] ?: '—') ?></div>
        </div>
        <div class="info-box">
          <div class="info-label">Pays</div>
          <div class="info-val"><?= htmlspecialchars($company["country"] ?: '—') ?></div>
        </div>
        <div class="info-box">
          <div class="info-label">Taille</div>
          <div class="info-val"><?= htmlspecialchars($company["company_size"] ?: '—') ?></div>
        </div>
        <div class="info-box">
          <div class="info-label">Secteur</div>
          <div class="info-val"><?= htmlspecialchars($company["sector"] ?: '—') ?></div>
        </div>
        <?php if(!empty($company["fiscal_number"])): ?>
        <div class="info-box">
          <div class="info-label">Matricule fiscal</div>
          <div class="info-val"><?= htmlspecialchars($company["fiscal_number"]) ?></div>
        </div>
        <?php endif; ?>
        <?php if(!empty($company["website"])): ?>
        <div class="info-box">
          <div class="info-label">Site web</div>
          <div class="info-val"><a href="<?= htmlspecialchars($company["website"]) ?>" target="_blank"><?= htmlspecialchars($company["website"]) ?></a></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ════ SECTION EMPLOIS PUBLIÉS ════ -->
    <div class="section jobs-section">
      <div class="section-header">
        <div class="section-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 20V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v16"/>
            <rect x="2" y="14" width="20" height="6" rx="2"/>
          </svg>
        </div>
        <div class="section-title">
          Emplois publiés (<?= count($jobs) ?>)
        </div>
      </div>

      <?php if(count($jobs) > 0): ?>

        <div class="jobs-grid">

          <?php foreach($jobs as $job): ?>

            <div class="job-card">

              <div class="job-title">
                <?= htmlspecialchars($job["title"]) ?>
              </div>

              <div class="job-meta">
                <?= htmlspecialchars($job["city"] ?: '—') ?>
                •
                <?= htmlspecialchars($job["contract_type"] ?: '—') ?>
                <?php if(!empty($job["salary"])): ?>
                • <?= htmlspecialchars($job["salary"]) ?> TND
                <?php endif; ?>
              </div>

              <div class="job-desc">
                <?= mb_strimwidth(strip_tags($job["description"]), 0, 120, "...") ?>
              </div>

              <a href="job_details.php?id=<?= $job["id"] ?>" class="job-btn">
                Voir l'offre →
              </a>

            </div>

          <?php endforeach; ?>

        </div>

      <?php else: ?>

        <div class="no-jobs">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="display:block;margin:0 auto 12px;opacity:.3;">
            <rect width="20" height="14" x="2" y="7" rx="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
          Aucun emploi disponible pour le moment.
        </div>

      <?php endif; ?>

    </div>

  </div>

  <!-- ════ SIDEBAR ════ -->
  <div class="sidebar">

    <!-- Website + quick meta -->
    <div class="side-card">
      <?php if(!empty($company["website"])): ?>
      <a href="<?= htmlspecialchars($company["website"]) ?>" target="_blank" class="website-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        Visiter le site web
      </a>
      <?php endif; ?>

      <div class="side-title">Aperçu rapide</div>

      <?php if(!empty($company["country"])): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </div>
        <div>
          <div class="meta-label">Pays</div>
          <div class="meta-val"><?= htmlspecialchars($company["country"]) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if(!empty($company["company_size"])): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <div class="meta-label">Effectif</div>
          <div class="meta-val"><?= htmlspecialchars($company["company_size"]) ?></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if(!empty($company["sector"])): ?>
      <div class="meta-row">
        <div class="meta-icon">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
          <div class="meta-label">Secteur</div>
          <div class="meta-val"><?= htmlspecialchars($company["sector"]) ?></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Contact du manager -->
    <?php if(!empty($company["manager_name"]) || !empty($company["manager_email"]) || !empty($company["manager_phone"])): ?>
    <div class="side-card" style="animation-delay:.15s">
      <div class="side-title">Contact</div>
      <div class="contact-rows">

        <?php if(!empty($company["manager_name"])): ?>
        <div class="contact-row">
          <div class="cr-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div>
            <div class="cr-label">Manager</div>
            <div class="cr-val"><?= htmlspecialchars($company["manager_name"]) ?><?= !empty($company["manager_role"]) ? ' · '.htmlspecialchars($company["manager_role"]) : '' ?></div>
          </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($company["manager_email"])): ?>
        <div class="contact-row">
          <div class="cr-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <div class="cr-label">Email</div>
            <div class="cr-val"><?= htmlspecialchars($company["manager_email"]) ?></div>
          </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($company["manager_phone"])): ?>
        <div class="contact-row">
          <div class="cr-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div>
            <div class="cr-label">Téléphone</div>
            <div class="cr-val"><?= htmlspecialchars($company["manager_phone"]) ?></div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>