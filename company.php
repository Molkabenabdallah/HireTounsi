<?php
session_start();
include "config.php";

/* =========================
   AJOUT ENTREPRISE
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // sécurité : vérifier connexion seulement pour ajout
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }

    $name        = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $website     = $_POST["website"] ?? "";
    $fiscal      = $_POST["fiscal"] ?? "";
    $size        = $_POST["size"] ?? "";
    $country     = $_POST["country"] ?? "";
    $location    = $_POST["location"] ?? "";
    $sector      = $_POST["sector"] ?? "";

    $manager_name  = $_POST["manager_name"] ?? "";
    $manager_phone = $_POST["manager_phone"] ?? "";
    $manager_email = $_POST["manager_email"] ?? "";
    $manager_role  = $_POST["manager_role"] ?? "";

    /* IMAGE */
    $logo = "";

    if (!empty($_FILES["logo"]["name"])) {

        $logo = time() . "_" . basename($_FILES["logo"]["name"]);

        move_uploaded_file(
            $_FILES["logo"]["tmp_name"],
            "uploads/" . $logo
        );
    }

    /* INSERT */
    $stmt = $pdo->prepare("
        INSERT INTO companies
        (
            user_id,
            name,
            sector,
            description,
            logo,
            website,
            fiscal_number,
            company_size,
            country,
            location,
            manager_name,
            manager_phone,
            manager_email,
            manager_role,
            status
        )

        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'pending'
        )
    ");

    $stmt->execute([
        $_SESSION["user_id"],
        $name,
        $sector,
        $description,
        $logo,
        $website,
        $fiscal,
        $size,
        $country,
        $location,
        $manager_name,
        $manager_phone,
        $manager_email,
        $manager_role
    ]);

    $success = "Entreprise ajoutée avec succès.";
}

/* =========================
   RECUP ENTREPRISES
========================= */

$stmt = $pdo->query("
    SELECT * FROM companies
    WHERE status = 'approved'
    ORDER BY id DESC
");

$companies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mes Entreprises — HireTounsi</title>
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
  --green:  #22c55e;
  --red:    #ef4444;
  --yellow: #f59e0b;
  --r:      16px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text); min-height:100vh;
}

body::before {
  content:''; position:fixed; top:-200px; right:-150px;
  width:600px; height:600px; border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}
body::after {
  content:''; position:fixed; bottom:-180px; left:-100px;
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
.nav-links { display:flex; gap:4px; list-style:none; }
.nav-links a {
  color:var(--muted); text-decoration:none; font-size:14px;
  font-weight:500; padding:6px 14px; border-radius:20px; transition:.2s;
}
.nav-links a:hover { color:var(--text); background:var(--s2); }
.nav-cta {
  background:var(--accent); color:#fff; text-decoration:none;
  padding:8px 20px; border-radius:20px; font-weight:600; font-size:14px;
  transition:.2s; box-shadow:0 0 18px rgba(139,92,246,.3);
}
.nav-cta:hover { background:#7c3aed; }

/* ── MAIN ── */
main { position:relative; z-index:1; max-width:1200px; margin:0 auto; padding:40px 28px; }

/* ── PAGE HEADER ── */
.page-header {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:32px; flex-wrap:wrap; gap:16px;
}
.page-header h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:30px; letter-spacing:-.8px;
}
.page-header h1 span {
  background:linear-gradient(90deg,var(--accent),var(--a2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.page-header p { color:var(--muted); font-size:14px; margin-top:4px; }

.btn-create {
  display:inline-flex; align-items:center; gap:8px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; padding:12px 22px; border-radius:12px; color:#fff; cursor:pointer;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:14px;
  transition:.2s; box-shadow:0 4px 18px rgba(139,92,246,.35);
}
.btn-create:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(139,92,246,.5); }

/* ── SUCCESS ── */
.alert-success {
  display:flex; align-items:center; gap:10px;
  background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.25);
  color:#4ade80; padding:14px 18px; border-radius:12px; margin-bottom:24px;
  font-size:14px; font-weight:500;
  animation:up .4s ease both;
}

/* ── EMPTY STATE ── */
.empty-state {
  text-align:center; padding:80px 20px;
  animation:up .5s ease both;
}
.empty-state svg { display:block; margin:0 auto 20px; opacity:.2; }
.empty-state h3 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:20px; margin-bottom:8px;
}
.empty-state p { color:var(--muted); font-size:14px; margin-bottom:24px; }

/* ── GRID ── */
.grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
  gap:18px;
}

/* ── CARD ── */
.card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:20px; overflow:hidden;
  transition:.25s cubic-bezier(.4,0,.2,1);
  animation:up .45s ease both;
}
.card:nth-child(1){animation-delay:.05s}
.card:nth-child(2){animation-delay:.10s}
.card:nth-child(3){animation-delay:.15s}
.card:nth-child(4){animation-delay:.20s}
.card:hover { transform:translateY(-4px); border-color:rgba(139,92,246,.4); box-shadow:0 16px 40px rgba(0,0,0,.4); }

.card-banner {
  height:100px; position:relative;
  background:linear-gradient(135deg,#3b1fa8,#8b5cf6,#06b6d4);
}

.card-logo {
  width:64px; height:64px; border-radius:16px;
  border:3px solid var(--s2); overflow:hidden;
  position:absolute; bottom:-28px; left:20px;
  background:var(--s); display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:22px;
  box-shadow:0 4px 14px rgba(0,0,0,.4);
}
.card-logo img { width:100%; height:100%; object-fit:cover; }

.card-body { padding:44px 20px 20px; }

.company-name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:18px; letter-spacing:-.4px; margin-bottom:3px;
}
.company-sector { font-size:13px; color:var(--muted); margin-bottom:14px; }

.card-meta { display:flex; flex-wrap:wrap; gap:8px; align-items:center; }

.status-badge {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
}
.sdot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.s-pending  { background:rgba(245,158,11,.1);  border:1px solid rgba(245,158,11,.3);  color:#fbbf24; }
.s-approved { background:rgba(34,197,94,.1);   border:1px solid rgba(34,197,94,.25);  color:#4ade80; }
.s-rejected { background:rgba(239,68,68,.1);   border:1px solid rgba(239,68,68,.25);  color:#f87171; }

.card-location {
  display:flex; align-items:center; gap:4px;
  font-size:12px; color:var(--muted);
}

/* ════════════════════════════════
   MODAL
════════════════════════════════ */
.modal {
  position:fixed; inset:0; z-index:999;
  background:rgba(0,0,0,.65); backdrop-filter:blur(12px);
  display:none; align-items:center; justify-content:center; padding:20px;
}
.modal.active { display:flex; }

.modal-box {
  width:100%; max-width:760px; max-height:92vh;
  background:var(--s2); border:1px solid var(--border);
  border-radius:24px;
  animation:modalIn .35s cubic-bezier(.4,0,.2,1);
  box-shadow:0 32px 80px rgba(0,0,0,.7);
  display:flex; flex-direction:column;
}

@keyframes modalIn {
  from{opacity:0;transform:translateY(24px) scale(.97)}
  to{opacity:1;transform:translateY(0) scale(1)}
}

.modal-header {
  padding:24px 28px;
  border-bottom:1px solid var(--border);
  display:flex; align-items:flex-start; justify-content:space-between;
  flex-shrink:0; position:relative;
}
.modal-header::before {
  content:''; position:absolute; top:0; left:30px; right:30px; height:1px;
  background:linear-gradient(90deg,transparent,rgba(139,92,246,.6),transparent);
}
.modal-h-left {}
.modal-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:20px; letter-spacing:-.4px; margin-bottom:3px;
}
.modal-sub { font-size:13px; color:var(--muted); }

.modal-close {
  width:34px; height:34px; border-radius:9px;
  background:rgba(255,255,255,.05); border:1px solid var(--border);
  color:var(--muted); font-size:20px; cursor:pointer;
  display:flex; align-items:center; justify-content:center; transition:.2s; flex-shrink:0;
}
.modal-close:hover { color:var(--text); border-color:var(--accent); }

/* progress */
.progress-wrap { padding:20px 28px 0; flex-shrink:0; }
.progress-steps { display:flex; align-items:center; gap:0; margin-bottom:8px; }
.pstep { display:flex; align-items:center; }
.pstep::after {
  content:''; flex:1; height:2px; min-width:20px;
  background:var(--border); border-radius:99px; transition:.3s;
}
.pstep:last-child::after { display:none; }
.pstep.done::after { background:var(--accent); }
.pdot {
  width:30px; height:30px; border-radius:50%; flex-shrink:0;
  background:var(--s); border:2px solid var(--border);
  display:flex; align-items:center; justify-content:center;
  font-size:12px; font-weight:700; color:var(--muted2); transition:.3s;
}
.pstep.done   .pdot { background:var(--accent); border-color:var(--accent); color:#fff; }
.pstep.current .pdot { border-color:var(--accent); color:var(--accent); box-shadow:0 0 0 4px rgba(139,92,246,.2); }
.pbar-wrap { display:none; }

/* form area */
.form-area { padding:24px 28px; flex:1; overflow-y:auto; min-height:0; }

.form-step { display:none; }
.form-step.active { display:block; animation:up .3s ease both; }

.step-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:22px; letter-spacing:-.5px; margin-bottom:4px;
}
.step-desc { font-size:13px; color:var(--muted); margin-bottom:24px; }

/* fields */
.field { margin-bottom:16px; }
label {
  display:block; font-size:10px; font-weight:700; letter-spacing:.07em;
  text-transform:uppercase; color:var(--muted); margin-bottom:7px;
}

input, textarea, select {
  width:100%; padding:12px 14px;
  background:var(--s); border:1px solid var(--border);
  border-radius:10px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none; transition:.2s;
}
input::placeholder, textarea::placeholder { color:var(--muted2); }
input:focus, textarea:focus, select:focus {
  border-color:rgba(139,92,246,.6); box-shadow:0 0 0 3px rgba(139,92,246,.1);
}
textarea { resize:none; line-height:1.6; }
select {
  appearance:none; cursor:pointer;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7890' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 14px center; padding-right:36px;
}
select option { background:var(--s2); }

.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

/* logo upload */
.logo-upload-area {
  display:flex; align-items:center; gap:16px; margin-bottom:20px;
  padding:16px; background:var(--s); border:1px solid var(--border); border-radius:12px;
}
.logo-preview {
  width:64px; height:64px; border-radius:14px; flex-shrink:0;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-size:26px; overflow:hidden;
}
.logo-preview img { width:100%; height:100%; object-fit:cover; display:none; }
.logo-upload-info p { font-size:13px; font-weight:600; margin-bottom:3px; }
.logo-upload-info span { font-size:11px; color:var(--muted2); }

.file-input-wrap {
  position:relative; display:inline-block;
}
.file-btn {
  display:inline-flex; align-items:center; gap:6px;
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; padding:7px 14px; border-radius:8px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:12px;
  cursor:pointer; transition:.2s; margin-top:8px;
}
.file-btn:hover { background:rgba(139,92,246,.22); }
.file-input-wrap input[type="file"] {
  position:absolute; inset:0; opacity:0; cursor:pointer;
  width:100%; height:100%; border:none; background:none; padding:0;
  box-shadow:none;
}
.file-input-wrap input[type="file"]:focus { box-shadow:none; }

/* step 3 doc */
.doc-zone {
  border:2px dashed rgba(139,92,246,.3); border-radius:14px;
  padding:36px; text-align:center; background:rgba(139,92,246,.03);
  transition:.2s; cursor:pointer; position:relative;
}
.doc-zone:hover { border-color:rgba(139,92,246,.6); background:rgba(139,92,246,.06); }
.doc-zone input[type="file"] {
  position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
  border:none; background:none; padding:0;
}
.doc-icon { font-size:32px; margin-bottom:10px; }
.doc-zone p { font-size:14px; font-weight:600; margin-bottom:4px; }
.doc-zone span { font-size:12px; color:var(--muted2); }

/* modal footer */
.modal-footer {
  display:flex; justify-content:space-between; align-items:center;
  padding:18px 28px; border-top:1px solid var(--border); flex-shrink:0;
}
.btn-prev {
  display:inline-flex; align-items:center; gap:6px;
  background:var(--s); border:1px solid var(--border);
  color:var(--muted); padding:11px 18px; border-radius:10px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:14px;
  cursor:pointer; transition:.2s;
}
.btn-prev:hover { color:var(--text); border-color:rgba(139,92,246,.4); }
.btn-next, .btn-submit {
  display:inline-flex; align-items:center; gap:7px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  border:none; padding:11px 22px; border-radius:10px; color:#fff;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:700; font-size:14px;
  cursor:pointer; transition:.2s; box-shadow:0 4px 16px rgba(139,92,246,.35);
}
.btn-next:hover, .btn-submit:hover { transform:translateY(-1px); box-shadow:0 8px 22px rgba(139,92,246,.5); }
.hidden { display:none !important; }

.step-counter { font-size:12px; color:var(--muted2); }

@keyframes up {
  from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)}
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <ul class="nav-links">
    <li><a href="jobs.php">Offres</a></li>
    <li><a href="talents.php">Talents</a></li>
    <li><a href="companies.php" style="color:var(--text)">Entreprises</a></li>
  </ul>
  <a href="#" class="nav-cta">connexion</a>
</nav>

<main>

  <?php if(isset($success)): ?>
  <div class="alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
    <?= htmlspecialchars($success) ?>
  </div>
  <?php endif; ?>

  <div class="page-header">
    <div>
      <h1>Entreprises <span> Partenaires</span></h1>
      <p>entreprises trouvees</p>
    </div>
    <?php if(isset($_SESSION["user_id"])): ?>

<button class="btn-create" onclick="openModal()">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <path d="M12 5v14M5 12h14"/>
  </svg>
  Ajouter votre entreprise
</button>

<?php else: ?>

<a href="login.php" class="btn-create" style="text-decoration:none;">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <path d="M12 5v14M5 12h14"/>
  </svg>
  Ajouter votre entreprise
</a>

<?php endif; ?>
  </div>

  <?php if(empty($companies)): ?>
  <div class="empty-state">
    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
      <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
      <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
      <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
    </svg>
    <h3>Aucune entreprise</h3>
    <p>Vous n'avez pas encore ajouté d'entreprise.</p>
    <button class="btn-create" onclick="openModal()">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
      Créer ma première entreprise
    </button>
  </div>

  <?php else: ?>
  <div class="grid">
    <?php foreach($companies as $c): ?>
    <div class="card">
      <div class="card-banner">
        <div class="card-logo">
          <?php if(!empty($c["logo"])): ?>
            <img src="uploads/<?= htmlspecialchars($c["logo"]) ?>" alt="logo">
          <?php else: ?>
            <?= strtoupper(substr($c["name"], 0, 1)) ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body">
        <div class="company-name"><?= htmlspecialchars($c["name"]) ?></div>
        <div class="company-sector"><?= htmlspecialchars($c["sector"] ?? '') ?></div>
        <div class="card-meta">
          <span class="status-badge s-<?= $c["status"] ?>">
            <span class="sdot"></span><?= ucfirst($c["status"]) ?>
          </span>
          <?php if(!empty($c["location"])): ?>
          <span class="card-location">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= htmlspecialchars($c["location"]) ?>
          </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>

<!-- ════════════════════════════════
     MODAL MULTI-ÉTAPES
════════════════════════════════ -->
<div class="modal" id="modal">
<div class="modal-box">

  <!-- Header -->
  <div class="modal-header">
    <div class="modal-h-left">
      <div class="modal-title">Créer une entreprise</div>
      <div class="modal-sub">Complétez les 4 étapes pour soumettre votre entreprise</div>
    </div>
    <button class="modal-close" onclick="closeModal()">&times;</button>
  </div>

  <!-- Progress -->
  <div class="progress-wrap">
    <div class="progress-steps">
      <div class="pstep current" id="ps0"><div class="pdot">1</div></div>
      <div class="pstep" id="ps1"><div class="pdot">2</div></div>
      <div class="pstep" id="ps2"><div class="pdot">3</div></div>
      <div class="pstep" id="ps3"><div class="pdot">4</div></div>
    </div>
    <div class="pbar-wrap">
      <div class="pbar done" id="pb0"></div>
      <div class="pbar" id="pb1"></div>
      <div class="pbar" id="pb2"></div>
      <div class="pbar" id="pb3"></div>
    </div>
  </div>

  <!-- Form -->
  <form method="POST" enctype="multipart/form-data">
  <div class="form-area">

    <!-- STEP 1 -->
    <div class="form-step active" id="step0">
      <div class="step-title">Identité de l'entreprise</div>
      <div class="step-desc">Ajoutez le logo et les informations de base de votre entreprise</div>

      <div class="logo-upload-area">
        <div class="logo-preview" id="logoPreview">🏢</div>
        <div class="logo-upload-info">
          <p>Logo de l'entreprise</p>
          <span>JPG, PNG, WEBP · Max 2MB</span>
          <div class="file-input-wrap">
            <div class="file-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Choisir un logo
            </div>
            <input type="file" name="logo" accept="image/*" id="logoInput">
          </div>
        </div>
      </div>

      <div class="field">
        <label>Nom de l'entreprise *</label>
        <input type="text" name="name" placeholder="Ex : TechCorp Tunisie" required>
      </div>

      <div class="field">
        <label>Description</label>
        <textarea name="description" rows="4" placeholder="Décrivez votre entreprise, sa mission, ses activités…"></textarea>
      </div>
    </div>

    <!-- STEP 2 -->
    <div class="form-step" id="step1">
      <div class="step-title">Informations de l'entreprise</div>
      <div class="step-desc">Renseignez les détails officiels et coordonnées de votre entreprise</div>

      <div class="grid-2">
        <div class="field">
          <label>Site Web</label>
          <input type="text" name="website" placeholder="https://exemple.com">
        </div>
        <div class="field">
          <label>Matricule Fiscal</label>
          <input type="text" name="fiscal" placeholder="Ex : 1234567A">
        </div>
        <div class="field">
          <label>Taille de l'entreprise</label>
          <select name="size">
            <option value="">Choisir…</option>
            <option>1-10 employés</option>
            <option>10-50 employés</option>
            <option>50-200 employés</option>
            <option>200+ employés</option>
          </select>
        </div>
        <div class="field">
          <label>Pays</label>
          <input type="text" name="country" value="Tunisie" placeholder="Tunisie">
        </div>
        <div class="field">
          <label>Ville / Lieu</label>
          <input type="text" name="location" placeholder="Ex : Tunis, Sfax…">
        </div>
        <div class="field">
          <label>Secteur d'activité</label>
          <input type="text" name="sector" placeholder="Ex : Technologie, Finance…">
        </div>
      </div>
    </div>

    <!-- STEP 3 -->
    <div class="form-step" id="step2">
      <div class="step-title">Documents de vérification</div>
      <div class="step-desc">Uploadez les documents officiels pour valider votre entreprise</div>

      <div class="doc-zone">
        <div class="doc-icon">📄</div>
        <p>Glissez vos documents ici</p>
        <span>PDF, JPG, PNG · Registre de commerce, patente…</span>
        <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <div style="margin-top:16px;padding:14px;background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.2);border-radius:10px;font-size:13px;color:var(--muted)">
        <strong style="color:var(--text)">Documents acceptés :</strong> Registre de commerce, patente fiscale, attestation d'immatriculation. Cette étape est optionnelle mais accélère la validation.
      </div>
    </div>

    <!-- STEP 4 -->
    <div class="form-step" id="step3">
      <div class="step-title">Informations du manager</div>
      <div class="step-desc">Renseignez le contact principal de l'entreprise</div>

      <div class="grid-2">
        <div class="field" style="grid-column:1/-1">
          <label>Nom complet *</label>
          <input type="text" name="manager_name" placeholder="Prénom et Nom" required>
        </div>
        <div class="field">
          <label>Téléphone *</label>
          <input type="text" name="manager_phone" placeholder="+216 XX XXX XXX">
        </div>
        <div class="field">
          <label>Email professionnel *</label>
          <input type="email" name="manager_email" placeholder="contact@entreprise.com">
        </div>
        <div class="field" style="grid-column:1/-1">
          <label>Poste / Titre</label>
          <input type="text" name="manager_role" placeholder="Ex : Directeur Général, RH Manager…">
        </div>
      </div>
    </div>

  </div>

  <!-- Footer -->
  <div class="modal-footer">
    <div style="display:flex;align-items:center;gap:12px">
      <button type="button" class="btn-prev hidden" id="prevBtn" onclick="prevStep()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
        Retour
      </button>
      <span class="step-counter" id="stepCounter">Étape 1 sur 4</span>
    </div>
    <div style="display:flex;gap:10px">
      <button type="button" class="btn-next" id="nextBtn" onclick="nextStep()">
        Suivant
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
      </button>
      <button type="submit" class="btn-submit hidden" id="submitBtn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
        Créer l'entreprise
      </button>
    </div>
  </div>

  </form>
</div>
</div>

<script>
// ── MODAL ──
const modal = document.getElementById("modal");
function openModal()  { modal.classList.add("active"); current = 0; updateSteps(); }
function closeModal() { modal.classList.remove("active"); }
modal.addEventListener('click', e => { if(e.target === modal) closeModal(); });
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });

// ── STEPS ──
const steps   = document.querySelectorAll(".form-step");
const psteps  = document.querySelectorAll(".pstep");
const pbars   = document.querySelectorAll(".pbar");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const submitBtn  = document.getElementById("submitBtn");
const stepCounter = document.getElementById("stepCounter");
let current = 0;

function updateSteps() {
  steps.forEach((s, i)  => s.classList.toggle("active", i === current));

  psteps.forEach((p, i) => {
    p.classList.remove("done","current");
    if(i < current)  p.classList.add("done");
    if(i === current) p.classList.add("current");
  });

  pbars.forEach((b, i) => b.classList.toggle("done", i < current));

  prevBtn.classList.toggle("hidden", current === 0);
  nextBtn.classList.toggle("hidden", current === steps.length - 1);
  submitBtn.classList.toggle("hidden", current !== steps.length - 1);
  stepCounter.textContent = `Étape ${current + 1} sur ${steps.length}`;
}

function nextStep() { if(current < steps.length - 1) { current++; updateSteps(); } }
function prevStep() { if(current > 0) { current--; updateSteps(); } }

// ── LOGO PREVIEW ──
document.getElementById('logoInput').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const p = document.getElementById('logoPreview');
    p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;display:block">`;
  };
  reader.readAsDataURL(file);
});
</script>

</body>
</html>