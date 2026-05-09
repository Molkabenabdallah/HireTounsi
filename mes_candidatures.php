<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// Récupérer les candidatures avec infos des offres
$stmt = $pdo->prepare("
    SELECT 
        applications.*,
        jobs.title,
        jobs.company,
        jobs.city,
        jobs.contract_type,
        jobs.salary
    FROM applications
    INNER JOIN jobs ON applications.job_id = jobs.id
    WHERE applications.user_id = ?
    ORDER BY applications.created_at DESC
");

$stmt->execute([$userId]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer infos user pour la sidebar
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$userData = $stmtUser->fetch();

$userName = htmlspecialchars($userData["name"] ?? 'Utilisateur');
$initials = strtoupper(substr($userName, 0, 1));
$activeRole = $_SESSION['active_role'] ?? 'candidat';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Mes candidatures — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:      #07080d;
  --sidebar: #0e1018;
  --card:    #161824;
  --border:  rgba(255,255,255,0.07);
  --accent:  #8b5cf6;
  --accent2: #06b6d4;
  --text:    #f0eeff;
  --muted:   #7a7890;
  --muted2:  #4a4860;
  --green:   #34d399;
  --yellow:  #fbbf24;
  --sw: 240px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text); min-height:100vh; display:flex;
}

body::before {
  content:''; position:fixed; top:-200px; right:-150px;
  width:600px; height:600px;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ── SIDEBAR ── */
.sidebar {
  position:fixed; top:0; left:0; bottom:0; width:var(--sw);
  background:var(--sidebar); border-right:1px solid var(--border);
  display:flex; flex-direction:column; z-index:50;
}
.sidebar-logo {
  padding:22px 20px 18px; border-bottom:1px solid var(--border);
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:18px;
  color:#fff; text-decoration:none; display:flex; align-items:center; gap:8px;
}
.sidebar-logo em { color:var(--accent); font-style:normal; }

.sidebar-nav {
  flex:1; padding:14px 12px;
  display:flex; flex-direction:column; gap:2px;
}
.nav-sep {
  font-size:9px; font-weight:700; letter-spacing:.1em;
  text-transform:uppercase; color:var(--muted2);
  padding:10px 8px 4px;
}
.nav-item {
  display:flex; align-items:center; gap:10px;
  padding:9px 10px; border-radius:10px;
  color:var(--muted); text-decoration:none; font-size:13px; font-weight:500;
  transition:all .2s;
}
.nav-item:hover { background:rgba(255,255,255,.04); color:var(--text); }
.nav-item.active {
  background:rgba(139,92,246,.12); border:1px solid rgba(139,92,246,.2); color:#c4b5fd;
}
.nav-icon {
  width:26px; height:26px; border-radius:7px;
  background:rgba(255,255,255,.04);
  display:flex; align-items:center; justify-content:center;
  font-size:13px;
}

.sidebar-bottom {
  padding:12px; border-top:1px solid var(--border);
}
.user-pill {
  display:flex; align-items:center; gap:10px;
  padding:10px; border-radius:10px;
  background:rgba(255,255,255,.03); border:1px solid var(--border);
}
.avatar-sm {
  width:34px; height:34px; border-radius:9px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:13px;
}
.user-pill-info { flex:1; min-width:0; }
.upn { font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.upr { font-size:11px; color:var(--muted); }

/* ── MAIN ── */
.main {
  margin-left:var(--sw); flex:1;
  position:relative; z-index:1;
  padding:32px;
}

/* ── HEADER ── */
.page-header {
  margin-bottom:28px;
}
.page-header h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:28px; letter-spacing:-.6px; margin-bottom:6px;
}
.page-header h1 span {
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.page-header p { color:var(--muted); font-size:14px; }

/* ── FLASH MESSAGES ── */
.flash {
  padding:14px 18px; border-radius:12px; margin-bottom:24px;
  font-size:14px; display:flex; align-items:center; gap:10px;
  animation:slideDown .3s ease both;
}
.flash-success {
  background:rgba(52,211,153,.1); border:1px solid rgba(52,211,153,.25);
  color:var(--green);
}
.flash-error {
  background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);
  color:#f87171;
}
@keyframes slideDown {
  from{opacity:0;transform:translateY(-10px)}
  to{opacity:1;transform:translateY(0)}
}

/* ── GRID ── */
.grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
  gap:16px;
}

/* ── CARD ── */
.card {
  background:var(--card); border:1px solid var(--border);
  border-radius:16px; padding:22px;
  transition:all .25s;
  animation:up .4s ease both;
}
.card:hover { border-color:rgba(139,92,246,.3); transform:translateY(-3px); }

.card-header {
  display:flex; align-items:flex-start; justify-content:space-between;
  margin-bottom:16px; gap:12px;
}
.card-title {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:16px; letter-spacing:-.3px;
  line-height:1.3;
}
.card-company {
  font-size:13px; color:var(--muted); margin-top:4px;
}

.status-badge {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:20px;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.03em;
  white-space:nowrap; flex-shrink:0;
}
.status-pending {
  background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.25);
  color:var(--yellow);
}
.status-reviewed {
  background:rgba(96,165,250,.1); border:1px solid rgba(96,165,250,.25);
  color:#60a5fa;
}
.status-accepted {
  background:rgba(52,211,153,.1); border:1px solid rgba(52,211,153,.25);
  color:var(--green);
}
.status-rejected {
  background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25);
  color:#f87171;
}
.status-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }

.card-meta {
  display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px;
}
.chip {
  display:inline-flex; align-items:center; gap:5px;
  background:var(--sidebar); border:1px solid var(--border);
  padding:4px 10px; border-radius:6px; font-size:12px; color:var(--muted);
}
.chip.salary { color:var(--green); border-color:rgba(52,211,153,.2); background:rgba(52,211,153,.06); }
.chip.location { color:var(--accent2); border-color:rgba(6,182,212,.2); background:rgba(6,182,212,.06); }
.chip.contract { color:#a78bfa; border-color:rgba(139,92,246,.2); background:rgba(139,92,246,.08); }

.card-footer {
  display:flex; align-items:center; justify-content:space-between;
  padding-top:14px; border-top:1px solid var(--border);
}
.card-date {
  font-size:12px; color:var(--muted2);
}
.btn-view {
  display:inline-flex; align-items:center; gap:5px;
  padding:6px 14px; border-radius:8px;
  background:rgba(139,92,246,.15); border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; text-decoration:none; font-size:12px; font-weight:600;
  transition:all .2s;
}
.btn-view:hover { background:rgba(139,92,246,.25); color:#c4b5fd; }

.btn-view-cv {
  display:inline-flex; align-items:center; gap:5px;
  padding:6px 14px; border-radius:8px;
  background:rgba(255,255,255,.05); border:1px solid var(--border);
  color:var(--muted); text-decoration:none; font-size:12px; font-weight:600;
  transition:all .2s;
}
.btn-view-cv:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── EMPTY STATE ── */
.empty-state {
  text-align:center; padding:60px 20px;
  grid-column:1/-1;
}
.empty-state svg { margin-bottom:16px; opacity:.25; }
.empty-state h3 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:18px; margin-bottom:8px;
}
.empty-state p { color:var(--muted); font-size:14px; margin-bottom:20px; }
.empty-state .btn-primary {
  display:inline-flex; align-items:center; gap:8px;
  background:linear-gradient(135deg,var(--accent),#7c3aed);
  color:#fff; text-decoration:none; padding:10px 20px; border-radius:10px;
  font-weight:600; font-size:14px; transition:all .2s;
}
.empty-state .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(139,92,246,.4); }

@keyframes up {
  from{opacity:0;transform:translateY(14px)}
  to{opacity:1;transform:translateY(0)}
}

@media (max-width:900px) {
  .sidebar { display:none; }
  .main { margin-left:0; padding:20px; }
}
</style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
  <a href="index.html" class="sidebar-logo">Hire<em>Tounsi</em></a>

  <nav class="sidebar-nav">
    <span class="nav-sep">Navigation</span>
    <a href="dashboard.php" class="nav-item">
      <span class="nav-icon">🏠</span> Tableau de bord
    </a>
    <a href="jobs.php" class="nav-item">
      <span class="nav-icon">🔍</span> Offres d'emploi
    </a>
    <a href="mes_candidatures.php" class="nav-item active">
      <span class="nav-icon">📋</span> Mes candidatures
    </a>
    <a href="talents.php" class="nav-item">
      <span class="nav-icon">👥</span> Talents
    </a>

    <span class="nav-sep">Compte</span>
    <a href="profile.php" class="nav-item">
      <span class="nav-icon">👤</span> Mon profil
    </a>
  </nav>

  <div class="sidebar-bottom">
    <div class="user-pill">
      <div class="avatar-sm"><?= $initials ?></div>
      <div class="user-pill-info">
        <div class="upn"><?= $userName ?></div>
        <div class="upr">Candidat</div>
      </div>
    </div>
  </div>
</aside>

<!-- ═══ MAIN ═══ -->
<main class="main">

  <div class="page-header">
    <h1>Mes <span>candidatures</span></h1>
    <p>Suivez l'état de vos candidatures envoyées</p>
  </div>

  <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="flash flash-success">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M20 6 9 17l-5-5"/>
      </svg>
      <?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="flash flash-error">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <div class="grid">

    <?php if (count($applications) > 0): ?>
      <?php foreach($applications as $app): 
        // Déterminer le statut (par défaut pending si pas de colonne status)
        $status = $app['status'] ?? 'pending';
        $statusClass = match($status) {
            'pending' => 'status-pending',
            'reviewed', 'seen' => 'status-reviewed',
            'accepted', 'approved' => 'status-accepted',
            'rejected' => 'status-rejected',
            default => 'status-pending'
        };
        $statusLabel = match($status) {
            'pending' => 'En attente',
            'reviewed', 'seen' => 'Vue',
            'accepted', 'approved' => 'Acceptée',
            'rejected' => 'Refusée',
            default => 'En attente'
        };
      ?>
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title"><?= htmlspecialchars($app["title"]) ?></div>
            <div class="card-company"><?= htmlspecialchars($app["company"]) ?></div>
          </div>
          <span class="status-badge <?= $statusClass ?>">
            <span class="status-dot"></span><?= $statusLabel ?>
          </span>
        </div>

        <div class="card-meta">
          <?php if (!empty($app['city'])): ?>
          <span class="chip location">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            <?= htmlspecialchars($app["city"]) ?>
          </span>
          <?php endif; ?>

          <?php if (!empty($app['salary'])): ?>
          <span class="chip salary">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
            <?= htmlspecialchars($app["salary"]) ?> TND
          </span>
          <?php endif; ?>

          <?php if (!empty($app['contract_type'])): ?>
          <span class="chip contract">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
            <?= htmlspecialchars($app["contract_type"]) ?>
          </span>
          <?php endif; ?>
        </div>

        <div class="card-footer">
          <span class="card-date">
            Postulé le <?= date("d/m/Y", strtotime($app["created_at"])) ?>
          </span>
          <div style="display:flex;gap:8px;">
            <a href="uploads/<?= htmlspecialchars($app["cv"]) ?>" target="_blank" class="btn-view-cv">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
              </svg>
              Mon CV
            </a>
            <a href="jobs.php" class="btn-view">Voir l'offre →</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

    <?php else: ?>
      <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="display:block;margin:0 auto 16px;">
          <rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
        <h3>Aucune candidature</h3>
        <p>Vous n'avez pas encore postulé à une offre d'emploi.</p>
        <a href="jobs.php" class="btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          Découvrir les offres
        </a>
      </div>
    <?php endif; ?>

  </div>
</main>

</body>
</html>