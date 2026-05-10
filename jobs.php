<?php
session_start();
include "config.php";

// récupérer les offres
$stmt = $pdo->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
$jobs = $stmt->fetchAll();


;

// Check if user is logged in
$isLoggedIn = isset($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Offres d'emploi</title>

<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:      #07080d;
  --surface: #0e1018;
  --surface2:#161824;
  --border:  rgba(255,255,255,0.07);
  --accent:  #8b5cf6;
  --accent2: #06b6d4;
  --text:    #f0eeff;
  --muted:   #7a7890;
  --muted2:  #4a4860;
  --green:   #34d399;
  --r:       14px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text); min-height:100vh;
}

body::before {
  content:''; position:fixed; top:-200px; left:-200px;
  width:600px; height:600px;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}
body::after {
  content:''; position:fixed; bottom:-180px; right:-100px;
  width:500px; height:500px;
  background:radial-gradient(circle,rgba(6,182,212,.06) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ── NAV ── */
nav {
  position:sticky; top:0; z-index:100;
  display:flex; align-items:center; justify-content:space-between;
  padding:0 40px; height:64px;
  border-bottom:1px solid var(--border);
  background:rgba(7,8,13,.88);
  backdrop-filter:blur(20px);
}
.nav-logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:20px;
  color:var(--text); text-decoration:none; letter-spacing:-.5px;
}
.nav-logo span { color:var(--accent); }
.nav-links { display:flex; gap:4px; list-style:none; }
.nav-links a {
  color:var(--muted); text-decoration:none; font-size:14px;
  font-weight:500; padding:6px 14px; border-radius:20px; transition:.2s;
}
.nav-links a:hover { color:var(--text); background:var(--surface2); }
.btn-nav {
  background:var(--accent); border:none; padding:8px 20px;
  border-radius:20px; color:#fff; cursor:pointer;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:14px;
  text-decoration:none; transition:.2s;
  box-shadow:0 0 20px rgba(139,92,246,.3);
}
.btn-nav:hover { background:#7c3aed; }

/* ── MAIN ── */
main {
  position:relative; z-index:1;
  max-width:1280px; margin:0 auto;
  padding:40px 28px;
}

/* ── PAGE HEADER ── */
.page-header {
  display:flex; align-items:flex-start; justify-content:space-between;
  margin-bottom:28px; gap:20px; flex-wrap:wrap;
}
.page-header-text h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:34px; letter-spacing:-1px; line-height:1.1; margin-bottom:6px;
}
.page-header-text h1 span {
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.page-header-text p { color:var(--muted); font-size:15px; }

.btn {
  display:inline-flex; align-items:center; gap:8px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; padding:11px 22px; border-radius:12px; color:#fff; cursor:pointer;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:14px;
  transition:.2s; box-shadow:0 4px 18px rgba(139,92,246,.35);
  text-decoration:none; white-space:nowrap;
}
.btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(139,92,246,.5); }

/* ── SEARCH ── */
.search-wrapper { position:relative; margin-bottom:24px; }
.search-icon {
  position:absolute; left:16px; top:50%; transform:translateY(-50%);
  color:var(--muted); pointer-events:none;
}
.search-input {
  width:100%; padding:13px 16px 13px 46px;
  background:var(--surface2); border:1px solid var(--border);
  border-radius:12px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none; transition:.2s;
}
.search-input::placeholder { color:var(--muted2); }
.search-input:focus { border-color:rgba(139,92,246,.6); box-shadow:0 0 0 3px rgba(139,92,246,.1); }

/* ── LAYOUT ── */
.body-layout {
  display:grid;
  grid-template-columns:240px 1fr;
  gap:24px;
  align-items:start;
}

/* ════════ SIDEBAR ════════ */
.filters-sidebar {
  position:sticky; top:84px;
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:var(--r);
  padding:20px;
}

.filters-header {
  display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;
}
.filters-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
}
.filters-reset {
  font-size:11px; font-weight:600; color:var(--accent);
  cursor:pointer; background:none; border:none;
  font-family:'Instrument Sans',sans-serif; opacity:.8; transition:.15s;
}
.filters-reset:hover { opacity:1; text-decoration:underline; }

.filter-section { margin-bottom:18px; }
.filter-section:last-child { margin-bottom:0; }

.filter-section-title {
  font-size:10px; font-weight:700; letter-spacing:.08em;
  text-transform:uppercase; color:var(--muted2); margin-bottom:10px;
}

.filter-divider { height:1px; background:var(--border); margin:16px 0; }

.filter-option {
  display:flex; align-items:center; gap:9px;
  padding:5px 0; cursor:pointer;
}
.filter-option:hover .filter-label { color:var(--text); }
.filter-option input[type="checkbox"] {
  width:16px; height:16px; border-radius:4px;
  accent-color:var(--accent); cursor:pointer; flex-shrink:0;
}
.filter-label {
  font-size:13px; color:var(--muted); font-weight:500; transition:.15s; user-select:none;
}
.filter-count {
  margin-left:auto; font-size:11px; color:var(--muted2);
  background:var(--surface); border:1px solid var(--border);
  border-radius:20px; padding:1px 7px;
}

/* salary range */
.salary-labels {
  display:flex; justify-content:space-between;
  font-size:12px; color:var(--muted); margin-bottom:8px;
}
input[type="range"] {
  width:100%; accent-color:var(--accent); cursor:pointer;
}
.salary-display {
  text-align:center; margin-top:8px;
  font-size:13px; font-weight:600; color:var(--accent);
}

/* ── ACTIVE CHIPS ── */
.active-filters { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px; }
.active-filter-chip {
  display:inline-flex; align-items:center; gap:5px;
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; padding:4px 10px; border-radius:20px;
  font-size:12px; font-weight:600; cursor:pointer; transition:.15s;
}
.active-filter-chip:hover { background:rgba(139,92,246,.22); }

.results-count { font-size:13px; color:var(--muted); margin-bottom:16px; }
.results-count b { color:var(--text); font-weight:700; }

/* ── GRID ── */
.grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  gap:14px;
}

/* ── CARD ── */
.card {
  background:var(--surface2); border:1px solid var(--border);
  border-radius:var(--r); padding:22px;
  transition:.25s cubic-bezier(.4,0,.2,1);
  position:relative; overflow:hidden;
  animation:up .4s ease both;
}
.card::before {
  content:''; position:absolute; inset:0;
  background:linear-gradient(135deg,rgba(139,92,246,.03) 0%,transparent 60%);
  opacity:0; transition:.25s;
  pointer-events:none;
}
.card:hover { transform:translateY(-4px); border-color:rgba(139,92,246,.4); box-shadow:0 12px 36px rgba(0,0,0,.35); }
.card:hover::before { opacity:1; }
.card.hidden { display:none !important; }

@keyframes up {
  from{opacity:0;transform:translateY(14px)}
  to{opacity:1;transform:translateY(0)}
}

.card-icon {
  width:44px; height:44px; border-radius:12px;
  background:linear-gradient(135deg,rgba(139,92,246,.2),rgba(6,182,212,.1));
  border:1px solid rgba(139,92,246,.2);
  display:flex; align-items:center; justify-content:center;
  margin-bottom:14px; color:var(--accent);
}
.card h3 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:16px; letter-spacing:-.3px; margin-bottom:4px;
}
.card-company {
  font-size:13px; color:var(--muted); margin-bottom:14px;
  display:flex; align-items:center; gap:5px;
}
.card-meta { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:16px; }
.chip {
  display:inline-flex; align-items:center; gap:5px;
  background:var(--surface); border:1px solid var(--border);
  padding:4px 10px; border-radius:6px; font-size:12px; color:var(--muted); font-weight:500;
}
.chip.salary  { color:#34d399; border-color:rgba(52,211,153,.2);  background:rgba(52,211,153,.06); }
.chip.contract{ color:#a78bfa; border-color:rgba(139,92,246,.25); background:rgba(139,92,246,.08); }
.card-divider { height:1px; background:var(--border); margin:14px 0; }
.btn-apply {
  display:inline-flex; align-items:center; gap:6px;
  background:var(--accent); border:none; padding:8px 16px; border-radius:9px;
  color:#fff; cursor:pointer;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:13px; transition:.2s;
}
.btn-apply:hover { background:#7c3aed; box-shadow:0 4px 14px rgba(139,92,246,.4); transform:translateY(-1px); }

/* empty */
.empty-state {
  grid-column:1/-1; text-align:center; padding:60px 20px;
  color:var(--muted2); display:none;
}
.empty-state svg { margin-bottom:14px; opacity:.25; display:block; margin-inline:auto; }

/* ════════ MODALS ════════ */
.modal {
  display:none; position:fixed; inset:0; z-index:200;
  background:rgba(0,0,0,.65); backdrop-filter:blur(6px);
  align-items:center; justify-content:center; padding:20px;
}
.modal.open { display:flex; }
.modal-content {
  background:var(--surface2); border:1px solid var(--border);
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
  background:var(--surface); border:1px solid var(--border);
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
  background:var(--surface); border:1px solid var(--border);
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
.modal select option { background:var(--surface2); }
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
</style>
</head>

<body>

<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <ul class="nav-links">
    <li><a href="talents.php">Talents</a></li>
    <li><a href="companies.php">Entreprises</a></li>
    <li><a href="jobs.php" style="color:var(--text)">Offres</a></li>
  </ul>
  <a href="dashboard.php" class="btn-nav">mon compte</a>
</nav>

<main>

  <div class="page-header">
    <div class="page-header-text">
      <h1>Offres d'<span>emploi</span></h1>
      <p>Trouvez l'opportunité qui correspond à votre profil</p>
    </div>
    <button id="openJobModal" class="btn">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
      Publier une offre
    </button>
  </div>

  <div class="search-wrapper">
    <span class="search-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </span>
    <input id="searchInput" class="search-input" placeholder="Rechercher un poste, une entreprise, une ville…">
  </div>

  <div class="body-layout">

    <!-- ════ SIDEBAR ════ -->
    <div class="filters-sidebar">
      <div class="filters-header">
        <span class="filters-title">Filtres</span>
        <button class="filters-reset" id="resetFilters">Réinitialiser</button>
      </div>

      <!-- Ville -->
      <div class="filter-section">
        <div class="filter-section-title">Ville</div>
        <?php
          $cities = array_unique(array_filter(array_column($jobs, 'city')));
          sort($cities);
          foreach($cities as $city):
            $cnt = count(array_filter($jobs, fn($j) => $j['city'] === $city));
        ?>
        <label class="filter-option">
          <input type="checkbox" class="filter-city" value="<?= htmlspecialchars($city) ?>">
          <span class="filter-label"><?= htmlspecialchars($city) ?></span>
          <span class="filter-count"><?= $cnt ?></span>
        </label>
        <?php endforeach; ?>
        <?php if(empty($cities)): ?>
          <label class="filter-option">
            <input type="checkbox" class="filter-city" value="Tunis">
            <span class="filter-label">Tunis</span><span class="filter-count">0</span>
          </label>
          <label class="filter-option">
            <input type="checkbox" class="filter-city" value="Sfax">
            <span class="filter-label">Sfax</span><span class="filter-count">0</span>
          </label>
        <?php endif; ?>
      </div>

      <div class="filter-divider"></div>

      <!-- Contrat -->
      <div class="filter-section">
        <div class="filter-section-title">Type de contrat</div>
        <?php
          $contractTypes = ['CDI','CDD','Freelance','Stage','Temps partiel'];
          foreach($contractTypes as $ct):
            $cnt = count(array_filter($jobs, fn($j) => ($j['contract_type'] ?? '') === $ct));
        ?>
        <label class="filter-option">
          <input type="checkbox" class="filter-contract" value="<?= $ct ?>">
          <span class="filter-label"><?= $ct ?></span>
          <span class="filter-count"><?= $cnt ?></span>
        </label>
        <?php endforeach; ?>
      </div>

      <div class="filter-divider"></div>

      <!-- Salaire -->
      <div class="filter-section">
        <div class="filter-section-title">Salaire minimum (TND)</div>
        <div class="salary-labels"><span>0</span><span>5 000</span></div>
        <input type="range" id="salaryRange" min="0" max="5000" step="100" value="0">
        <div class="salary-display" id="salaryDisplay">Tous les salaires</div>
      </div>
    </div>

    <!-- ════ CONTENU ════ -->
    <div>
      <div class="active-filters" id="activeFilters"></div>
      <div class="results-count" id="resultsCount">
        <b><?= count($jobs) ?></b> offre<?= count($jobs)>1?'s':'' ?> disponible<?= count($jobs)>1?'s':'' ?>
      </div>

      <div class="grid" id="jobsGrid">

      <?php foreach($jobs as $job): ?>
        <div class="card"
          data-title="<?= strtolower(htmlspecialchars($job['title'])) ?>"
          data-company="<?= strtolower(htmlspecialchars($job['company'])) ?>"
          data-city="<?= htmlspecialchars($job['city'] ?? '') ?>"
          data-salary="<?= (int)($job['salary'] ?? 0) ?>"
          data-contract="<?= htmlspecialchars($job['contract_type'] ?? '') ?>"
        >
          <div class="card-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect width="20" height="14" x="2" y="7" rx="2"/>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
          </div>

          <h3><?= htmlspecialchars($job['title']) ?></h3>

          <div class="card-company">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/></svg>
            <?= htmlspecialchars($job['company']) ?>
          </div>

          <div class="card-meta">
            <?php if(!empty($job['salary'])): ?>
            <span class="chip salary">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              <?= htmlspecialchars($job['salary']) ?> TND
            </span>
            <?php endif; ?>
            <?php if(!empty($job['city'])): ?>
            <span class="chip">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              <?= htmlspecialchars($job['city']) ?>
            </span>
            <?php endif; ?>
            <?php if(!empty($job['contract_type'])): ?>
            <span class="chip contract">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <?= htmlspecialchars($job['contract_type']) ?>
            </span>
            <?php endif; ?>
          </div>

          <div class="card-divider"></div>

          <button class="btn-apply" data-id="<?= $job['id'] ?>">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
            Postuler
          </button>
        </div>
      <?php endforeach; ?>

        <div class="empty-state" id="emptyState">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <p>Aucune offre ne correspond à vos filtres.</p>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- MODAL PUBLIER -->
<div id="jobModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-title">Publier une offre</span>
      <button class="close" id="closeJob">&times;</button>
    </div>
    <form action="save_job.php" method="POST">
      <label>Titre du poste</label>
      <input name="title" placeholder="Ex : Développeur Full-Stack" required>
      <label>Entreprise</label>
      <input name="company" placeholder="Nom de l'entreprise" required>
      <label>Type de contrat</label>
      <select name="contract_type">
        <option value="">Choisir…</option>
        <option>CDI</option><option>CDD</option>
        <option>Freelance</option><option>Stage</option><option>Temps partiel</option>
      </select>
      <label>Salaire (TND)</label>
      <input name="salary" placeholder="Ex : 2500">
      <label>Ville</label>
      <input name="city" placeholder="Ex : Tunis, Sfax…">
      <label>Description</label>
      <textarea name="description" placeholder="Décrivez le poste, les missions…"></textarea>
      <div class="modal-divider"></div>
      <button class="btn-modal" type="submit">Publier l'offre →</button>
    </form>
  </div>
</div>

<!-- MODAL CV -->
<div id="cvModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="modal-title">Envoyer votre CV</span>
      <button class="close" id="closeCv">&times;</button>
    </div>
    <form action="upload_cv.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="job_id" id="job_id">
      <label>Votre CV (PDF, DOCX)</label>
      <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
      <div class="modal-divider"></div>
      <button class="btn-modal" type="submit">importer mon cv</button>
      <button type="button" class="btn-manual" id="openApplyStep2">
  Compléter manuellement
            </button>
    </form>
  </div>
</div>




<!-- MODAL ETAPE 2 -->
<div id="applyModal" class="modal">
  <div class="modal-content" style="max-width:700px;">

    <div class="modal-header">
      <span class="modal-title">Finaliser ma candidature</span>
      <button class="close" id="closeApply">&times;</button>
    </div>

    <form>

      <label>Lettre de motivation</label>
      <textarea placeholder="Présentez-vous et expliquez pourquoi ce poste vous intéresse..."></textarea>

      <div class="modal-divider"></div>

      <label>Avez-vous une expérience ?</label>

      <div style="display:flex;gap:10px;margin-top:10px;">
        <button type="button" class="btn-modal">Oui</button>

        <button type="button"
        class="btn-manual">
        Non
        </button>
      </div>

      <label style="margin-top:20px;">Années d'expérience</label>

      <select>
        <option>Moins d'1 an</option>
        <option>1 - 3 ans</option>
        <option>3 - 5 ans</option>
        <option>Plus de 5 ans</option>
      </select>

      <label style="margin-top:20px;">Disponibilité</label>

      <input type="text" placeholder="Ex : Immédiatement">

      <button class="btn-modal" style="margin-top:25px;">
        Soumettre ma candidature
      </button>

    </form>

  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  /* ───────────────── MODALS ───────────────── */

  const jobModal    = document.getElementById("jobModal");
  const cvModal     = document.getElementById("cvModal");
  const applyModal  = document.getElementById("applyModal");
  const jobInput    = document.getElementById("job_id");

  /* OPEN JOB MODAL */
  document.getElementById("openJobModal").addEventListener("click", () => {

  <?php if($isLoggedIn): ?>

    jobModal.classList.add("open");

  <?php else: ?>

    window.location.href = "login.php";

  <?php endif; ?>

});

  /* CLOSE JOB MODAL */
  document.getElementById("closeJob").addEventListener("click", () => {
    jobModal.classList.remove("open");
  });

  /* CLOSE CV MODAL */
  document.getElementById("closeCv").addEventListener("click", () => {
    cvModal.classList.remove("open");
  });

  /* CLOSE APPLY MODAL */
  document.getElementById("closeApply").addEventListener("click", () => {
    applyModal.classList.remove("open");
  });

  /* OPEN CV MODAL */
  document.querySelectorAll(".btn-apply").forEach(btn => {

    btn.addEventListener("click", function(e) {

      e.preventDefault();

      const jobId = this.getAttribute("data-id");

      jobInput.value = jobId;

      cvModal.classList.add("open");

    });

  });

  /* OPEN STEP 2 MODAL */
  document.getElementById("openApplyStep2").addEventListener("click", () => {

    cvModal.classList.remove("open");

    applyModal.classList.add("open");

  });

  /* CLOSE WHEN CLICK OUTSIDE */
  window.addEventListener("click", (e) => {

    if (e.target === jobModal) {
      jobModal.classList.remove("open");
    }

    if (e.target === cvModal) {
      cvModal.classList.remove("open");
    }

    if (e.target === applyModal) {
      applyModal.classList.remove("open");
    }

  });

  /* ───────────────── FILTRES ───────────────── */

  const cards           = document.querySelectorAll(".card[data-title]");
  const searchInput     = document.getElementById("searchInput");
  const salaryRange     = document.getElementById("salaryRange");
  const salaryDisplay   = document.getElementById("salaryDisplay");
  const resultsCount    = document.getElementById("resultsCount");
  const emptyState      = document.getElementById("emptyState");
  const activeFiltersEl = document.getElementById("activeFilters");

  let state = {
    search:"",
    cities:[],
    contracts:[],
    salary:0
  };

  /* SALARY FILTER */

  salaryRange.addEventListener("input", () => {

    state.salary = parseInt(salaryRange.value);

    salaryDisplay.textContent = state.salary === 0
      ? "Tous les salaires"
      : state.salary.toLocaleString("fr-TN") + " TND et +";

    applyFilters();

  });

  /* CITY FILTER */

  document.querySelectorAll(".filter-city").forEach(cb => {

    cb.addEventListener("change", () => {

      state.cities = [
        ...document.querySelectorAll(".filter-city:checked")
      ].map(c => c.value);

      applyFilters();

    });

  });

  /* CONTRACT FILTER */

  document.querySelectorAll(".filter-contract").forEach(cb => {

    cb.addEventListener("change", () => {

      state.contracts = [
        ...document.querySelectorAll(".filter-contract:checked")
      ].map(c => c.value);

      applyFilters();

    });

  });

  /* SEARCH */

  searchInput.addEventListener("input", () => {

    state.search = searchInput.value.toLowerCase().trim();

    applyFilters();

  });

  /* RESET FILTERS */

  document.getElementById("resetFilters").addEventListener("click", () => {

    state = {
      search:"",
      cities:[],
      contracts:[],
      salary:0
    };

    searchInput.value = "";

    salaryRange.value = 0;

    salaryDisplay.textContent = "Tous les salaires";

    document.querySelectorAll(".filter-city, .filter-contract").forEach(cb => {
      cb.checked = false;
    });

    applyFilters();

  });

  /* APPLY FILTERS */

  function applyFilters() {

    let visible = 0;

    cards.forEach(card => {

      const ok =

        (!state.search ||
          card.dataset.title.includes(state.search) ||
          card.dataset.company.includes(state.search))

        &&

        (state.cities.length === 0 ||
          state.cities.includes(card.dataset.city))

        &&

        (state.contracts.length === 0 ||
          state.contracts.includes(card.dataset.contract))

        &&

        ((parseInt(card.dataset.salary) || 0) >= state.salary);

      card.classList.toggle("hidden", !ok);

      if (ok) visible++;

    });

    resultsCount.innerHTML =
      `<b>${visible}</b> offre${visible > 1 ? "s" : ""} disponible${visible > 1 ? "s" : ""}`;

    emptyState.style.display =
      visible === 0 ? "block" : "none";

    renderChips();

  }

  /* FILTER CHIPS */

  function renderChips() {

    activeFiltersEl.innerHTML = "";

    state.cities.forEach(city => {

      chip(city, () => {

        document.querySelector(`.filter-city[value="${city}"]`).checked = false;

        state.cities = state.cities.filter(c => c !== city);

        applyFilters();

      });

    });

    state.contracts.forEach(ct => {

      chip(ct, () => {

        document.querySelector(`.filter-contract[value="${ct}"]`).checked = false;

        state.contracts = state.contracts.filter(c => c !== ct);

        applyFilters();

      });

    });

    if (state.salary > 0) {

      chip(`≥ ${state.salary.toLocaleString("fr-TN")} TND`, () => {

        state.salary = 0;

        salaryRange.value = 0;

        salaryDisplay.textContent = "Tous les salaires";

        applyFilters();

      });

    }

  }

  /* CREATE CHIP */

  function chip(label, onRemove) {

    const el = document.createElement("span");

    el.className = "active-filter-chip";

    el.innerHTML = `
      ${label}
      <svg width="10" height="10" viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="3">
        <path d="M18 6 6 18M6 6l12 12"/>
      </svg>
    `;

    el.addEventListener("click", onRemove);

    activeFiltersEl.appendChild(el);

  }

});
</script>

</body>
</html>