<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// ===== VÉRIFICATION RÔLE =====
$activeRole = $_SESSION['active_role'] ?? 'candidat';
if ($activeRole !== 'recruteur') {
    header("Location: dashboard.php");
    exit();
}

$userId = $_SESSION["user_id"];

// Récupérer user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$userName = htmlspecialchars($user["name"]);
$initials = strtoupper(substr($userName, 0, 1));
$joinDate = isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A';

// ===== STATS RÉELLES =====
// Offres publiées par ce recruteur
$stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE user_id=?");
$stmt->execute([$userId]);
$totalJobs = $stmt->fetchColumn();

// Candidatures reçues sur ses offres
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM applications 
    INNER JOIN jobs ON applications.job_id = jobs.id 
    WHERE jobs.user_id = ?
");
$stmt->execute([$userId]);
$totalCandidates = $stmt->fetchColumn();

// Vues simulées (à remplacer par vraies stats plus tard)
$totalViews = $totalJobs * 15 + rand(10, 50);
$totalInterviews = intval($totalCandidates * 0.3);

// Récupérer les dernières offres
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id=? ORDER BY id DESC LIMIT 5");
$stmt->execute([$userId]);
$myJobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>HireTounsi – Dashboard Recruteur</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:      #07080d;
      --sidebar: #0e1018;
      --card:    #0e1018;
      --card2:   #161824;
      --border:  rgba(255,255,255,0.07);
      --accent:  #8b5cf6;
      --accent2: #06b6d4;
      --text:    #f0eeff;
      --muted:   #7a7890;
      --muted2:  #4a4860;
      --green:   #34d399;
      --blue:    #60a5fa;
      --orange:  #fb923c;
      --red:     #f87171;
      --sw: 240px;
    }

    body {
      font-family: 'Instrument Sans', sans-serif;
      background: var(--bg); color: var(--text);
      min-height: 100vh; display: flex; overflow-x: hidden;
    }

    body::before {
      content:''; position:fixed; top:-200px; right:-100px;
      width:600px; height:600px;
      background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
      pointer-events:none; z-index:0;
    }
    body::after {
      content:''; position:fixed; bottom:-180px; left:180px;
      width:500px; height:500px;
      background:radial-gradient(circle,rgba(6,182,212,.05) 0%,transparent 70%);
      pointer-events:none; z-index:0;
    }

    /* ════════════════════════════════
       SIDEBAR
    ════════════════════════════════ */
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
      background: var(--sidebar); border-right: 1px solid var(--border);
      display: flex; flex-direction: column; z-index: 50;
    }

    .sidebar-logo {
      padding: 22px 20px 18px;
      border-bottom: 1px solid var(--border);
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900; font-size: 18px;
      color: #fff; display: flex; align-items: center; gap: 8px;
      text-decoration: none; letter-spacing: -.5px;
    }
    .sidebar-logo em { color: var(--accent); font-style: normal; }

    /* ── ROLE SWITCHER ── */
    .role-switcher {
      margin: 14px 14px 0;
      background: var(--card2);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 5px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px;
      position: relative;
    }

    .role-btn {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      padding: 9px 6px;
      border-radius: 8px;
      font-family: 'Instrument Sans', sans-serif;
      font-weight: 600; font-size: 12px;
      text-decoration: none; cursor: pointer;
      border: none; background: transparent;
      color: var(--muted);
      transition: all .2s;
      position: relative; z-index: 1;
    }

    .role-btn:hover { color: var(--text); }

    .role-btn.active {
      background: linear-gradient(135deg, var(--accent), #7c3aed);
      color: #fff;
      box-shadow: 0 4px 14px rgba(139,92,246,.4);
    }

    .role-switcher-label {
      font-size: 10px; font-weight: 700; letter-spacing: .08em;
      text-transform: uppercase; color: var(--muted2);
      padding: 12px 14px 4px;
    }

    /* nav */
    .sidebar-nav {
      flex: 1; padding: 10px 12px 12px;
      display: flex; flex-direction: column; gap: 2px;
      overflow-y: auto;
    }

    .nav-sep {
      font-size: 9px; font-weight: 700; letter-spacing: .1em;
      text-transform: uppercase; color: var(--muted2);
      padding: 10px 8px 4px;
    }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 10px; border-radius: 10px;
      color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 500;
      transition: all .2s;
    }
    .nav-item:hover { background: rgba(255,255,255,.04); color: var(--text); }
    .nav-item.active {
      background: rgba(139,92,246,.12);
      border: 1px solid rgba(139,92,246,.2);
      color: #c4b5fd;
    }
    .nav-icon {
      width: 26px; height: 26px; border-radius: 7px;
      background: rgba(255,255,255,.04);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; flex-shrink: 0;
    }

    /* bottom user pill */
    .sidebar-bottom {
      padding: 12px; border-top: 1px solid var(--border);
    }
    .user-pill {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 10px; border-radius: 10px;
      background: rgba(255,255,255,.03);
      border: 1px solid var(--border);
    }
    .avatar-sm {
      width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
      background: linear-gradient(135deg, var(--accent), #7c3aed);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900; font-size: 13px;
    }
    .user-pill-info { flex: 1; min-width: 0; }
    .upn { font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .upr { font-size: 11px; color: var(--muted); }
    .logout-btn {
      color: var(--muted); text-decoration: none;
      padding: 5px; border-radius: 6px; transition: color .2s;
      display: flex; align-items: center; flex-shrink: 0;
    }
    .logout-btn:hover { color: #f87171; }

    /* ════════════════════════════════
       MAIN
    ════════════════════════════════ */
    .main {
      margin-left: var(--sw); flex: 1;
      position: relative; z-index: 1;
      display: flex; flex-direction: column; min-height: 100vh;
    }

    /* topbar */
    .topbar {
      padding: 18px 32px; border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: rgba(7,8,13,.8); backdrop-filter: blur(20px);
      position: sticky; top: 0; z-index: 40;
    }
    .topbar h1 {
      font-family: 'Cabinet Grotesk', sans-serif;
      font-weight: 800; font-size: 20px; letter-spacing: -.4px;
    }
    .topbar p { color: var(--muted); font-size: 12px; margin-top: 2px; }
    .topbar-right { display: flex; align-items: center; gap: 10px; }
    .avatar-lg {
      width: 38px; height: 38px; border-radius: 10px;
      background: linear-gradient(135deg, var(--accent), #7c3aed);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900; font-size: 14px;
      box-shadow: 0 0 16px rgba(139,92,246,.4);
    }
    .topbar-logout {
      color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 500;
      background: rgba(255,255,255,.05); border: 1px solid var(--border);
      padding: 7px 14px; border-radius: 8px; transition: all .2s;
    }
    .topbar-logout:hover { color: #f87171; border-color: rgba(248,113,113,.3); }

    /* content */
    .content { padding: 32px; flex: 1; }

    /* welcome banner */
    .welcome-banner {
      background: linear-gradient(130deg, rgba(6,182,212,.15) 0%, rgba(139,92,246,.07) 100%);
      border: 1px solid rgba(6,182,212,.2); border-radius: 18px;
      padding: 28px 32px; margin-bottom: 28px;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 18px; position: relative; overflow: hidden;
    }
    .welcome-banner::before {
      content:''; position:absolute; top:0; left:0; right:0; height:1px;
      background:linear-gradient(90deg,transparent,rgba(6,182,212,.5),transparent);
    }
    .welcome-banner h2 {
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900;
      font-size: 22px; letter-spacing: -.5px; margin-bottom: 5px;
    }
    .welcome-banner h2 span { color: var(--accent2); }
    .welcome-banner p { color: var(--muted); font-size: 13px; }
    .welcome-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent2), #0891b2);
      color: #fff; border: none; padding: 10px 20px; border-radius: 10px;
      font-family: 'Instrument Sans', sans-serif; font-size: 13px; font-weight: 600;
      cursor: pointer; text-decoration: none; transition: all .2s;
      box-shadow: 0 4px 16px rgba(6,182,212,.35);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(6,182,212,.5); }
    .btn-secondary {
      background: rgba(255,255,255,.06); color: var(--text);
      border: 1px solid var(--border); padding: 10px 20px; border-radius: 10px;
      font-family: 'Instrument Sans', sans-serif; font-size: 13px; font-weight: 500;
      cursor: pointer; text-decoration: none; transition: background .2s;
    }
    .btn-secondary:hover { background: rgba(255,255,255,.1); }

    /* stats */
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 14px; margin-bottom: 28px;
    }
    .stat-card {
      background: var(--card2); border: 1px solid var(--border);
      border-radius: 14px; padding: 20px 18px;
      transition: border-color .25s, transform .25s;
      animation: up .4s ease both;
    }
    .stat-card:hover { border-color: rgba(6,182,212,.3); transform: translateY(-2px); }
    .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .stat-label { font-size: 10px; color: var(--muted); font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }
    .stat-icon {
      width: 34px; height: 34px; border-radius: 9px;
      display: flex; align-items: center; justify-content: center; font-size: 15px;
    }
    .si-blue   { background: rgba(96,165,250,.12); }
    .si-purple { background: rgba(139,92,246,.15); }
    .si-green  { background: rgba(52,211,153,.1); }
    .si-orange { background: rgba(251,146,60,.1); }
    .si-red    { background: rgba(248,113,113,.1); }
    .stat-val {
      font-family: 'Cabinet Grotesk', sans-serif;
      font-weight: 900; font-size: 32px; letter-spacing: -1px; margin-bottom: 3px;
    }
    .stat-sub { font-size: 11px; color: var(--muted); }
    .stat-sub em { color: var(--green); font-style: normal; font-weight: 600; }

    /* panels */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
    .panel {
      background: var(--card2); border: 1px solid var(--border);
      border-radius: 14px; padding: 22px; animation: up .4s ease both;
    }
    .panel-header {
      display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;
    }
    .panel-title {
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 800; font-size: 15px; letter-spacing: -.3px;
    }
    .panel-action { font-size: 12px; color: var(--accent2); text-decoration: none; }
    .panel-action:hover { text-decoration: underline; }

    /* job list */
    .job-item {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 0; border-bottom: 1px solid var(--border);
    }
    .job-item:last-child { border-bottom: none; padding-bottom: 0; }
    .job-co {
      width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900; font-size: 14px;
    }
    .job-info { flex: 1; min-width: 0; }
    .job-title { font-size: 13px; font-weight: 600; margin-bottom: 1px; }
    .job-meta { font-size: 11px; color: var(--muted); }
    .job-status {
      font-size: 10px; padding: 3px 9px; border-radius: 20px; font-weight: 700; white-space: nowrap;
    }
    .status-pending  { background: rgba(251,191,36,.12); color: #fbbf24; border: 1px solid rgba(251,191,36,.2); }
    .status-approved { background: rgba(52,211,153,.12); color: var(--green); border: 1px solid rgba(52,211,153,.2); }
    .status-rejected { background: rgba(239,68,68,.12); color: var(--red); border: 1px solid rgba(239,68,68,.2); }

    /* quick actions */
    .quick-actions {
      display: flex; gap: 12px; margin-bottom: 28px;
    }
    .action-card {
      flex: 1; background: var(--card2); border: 1px solid var(--border);
      border-radius: 14px; padding: 20px; text-align: center;
      transition: all .25s; cursor: pointer; text-decoration: none;
      color: var(--text);
    }
    .action-card:hover { border-color: var(--accent); transform: translateY(-3px); }
    .action-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 12px; font-size: 20px;
    }
    .action-card h3 { font-family: 'Cabinet Grotesk', sans-serif; font-weight: 700; font-size: 14px; margin-bottom: 4px; }
    .action-card p { font-size: 12px; color: var(--muted); }

    /* empty state */
    .empty-state {
      text-align: center; padding: 40px 20px; color: var(--muted2);
    }
    .empty-state svg { margin-bottom: 12px; opacity: .3; }

    @keyframes up {
      from{opacity:0;transform:translateY(10px)}
      to{opacity:1;transform:translateY(0)}
    }

    @media (max-width: 900px) {
      .sidebar { display: none; }
      .main { margin-left: 0; }
      .two-col { grid-template-columns: 1fr; }
      .quick-actions { flex-direction: column; }
    }
  </style>
</head>
<body>

  <!-- ════════════════════════════════
       SIDEBAR
  ════════════════════════════════ -->
  <aside class="sidebar">

    <!-- Logo -->
    <a href="index.html" class="sidebar-logo">
      Hire<em>Tounsi</em>
    </a>

    <!-- ── ROLE SWITCHER ── -->
    <div style="padding: 14px 12px 0">
      <div style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted2);margin-bottom:8px;padding:0 2px">
        Mode actuel
      </div>
      <div class="role-switcher">
        <a href="switch_role.php?role=candidat" 
           class="role-btn <?= $activeRole === 'candidat' ? 'active' : '' ?>" 
           id="btn-candidat">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Candidat
        </a>
        <a href="switch_role.php?role=recruteur" 
           class="role-btn <?= $activeRole === 'recruteur' ? 'active' : '' ?>" 
           id="btn-recruteur">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="20" height="14" x="2" y="7" rx="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
          Recruteur
        </a>
      </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
      <span class="nav-sep">Navigation</span>

      <?php if ($activeRole === 'recruteur'): ?>
        <!-- NAV RECRUTEUR -->
        <a href="recruiter_dashboard.php" class="nav-item active">
          <span class="nav-icon">🏠</span> Tableau de bord
        </a>
        <a href="jobs.php" class="nav-item">
          <span class="nav-icon">➕</span> Publier une offre
        </a>
        <a href="mes_offres.php" class="nav-item">
          <span class="nav-icon">📋</span> Mes offres
        </a>
        <a href="#" class="nav-item">
          <span class="nav-icon">👥</span> Candidatures reçues
        </a>
      <?php else: ?>
        <!-- NAV CANDIDAT (redirection si accès direct) -->
        <a href="dashboard.php" class="nav-item">
          <span class="nav-icon">🏠</span> Tableau de bord Candidat
        </a>
        <a href="jobs.php" class="nav-item">
          <span class="nav-icon">🔍</span> Offres d'emploi
        </a>
      <?php endif; ?>

      <span class="nav-sep">Compte</span>
      <a href="profile.php" class="nav-item">
        <span class="nav-icon">👤</span> Mon profil
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">⚙️</span> Paramètres
      </a>
    </nav>

    <!-- User pill -->
    <div class="sidebar-bottom">
      <div class="user-pill">
        <div class="avatar-sm"><?= $initials ?></div>
        <div class="user-pill-info">
          <div class="upn"><?= $userName ?></div>
          <div class="upr">Recruteur · Membre depuis <?= $joinDate ?></div>
        </div>
        <a href="logout.php" class="logout-btn" title="Déconnexion">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
          </svg>
        </a>
      </div>
    </div>

  </aside>

  <!-- ════════════════════════════════
       MAIN
  ════════════════════════════════ -->
  <div class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div>
        <h1>Bonjour, <?= $userName ?> 👋</h1>
        <p>Espace recruteur — Gérez vos offres et candidatures</p>
      </div>
      <div class="topbar-right">
        <a href="logout.php" class="topbar-logout">Déconnexion</a>
        <div class="avatar-lg"><?= $initials ?></div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">

      <!-- Welcome banner -->
      <div class="welcome-banner">
        <div>
          <h2>Espace <span>Recruteur</span></h2>
          <p>Trouvez les meilleurs talents tunisiens pour vos équipes.</p>
        </div>
        <div class="welcome-actions">
          <a href="jobs.php" class="btn-primary">Publier une offre</a>
          <a href="mes_offres.php" class="btn-secondary">Mes offres</a>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <a href="jobs.php" class="action-card">
          <div class="action-icon" style="background:rgba(139,92,246,.15);color:#a78bfa;">➕</div>
          <h3>Nouvelle offre</h3>
          <p>Publier rapidement</p>
        </a>
        <a href="mes_offres.php" class="action-card">
          <div class="action-icon" style="background:rgba(6,182,212,.15);color:#67e8f9;">📋</div>
          <h3>Mes offres</h3>
          <p>Gérer les publications</p>
        </a>
        <a href="#" class="action-card">
          <div class="action-icon" style="background:rgba(52,211,153,.15);color:#34d399;">👥</div>
          <h3>Candidats</h3>
          <p>Voir les candidatures</p>
        </a>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Offres publiées</span>
            <div class="stat-icon si-purple">💼</div>
          </div>
          <div class="stat-val"><?= $totalJobs ?></div>
          <div class="stat-sub">Actives sur la plateforme</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Candidatures</span>
            <div class="stat-icon si-green">📨</div>
          </div>
          <div class="stat-val"><?= $totalCandidates ?></div>
          <div class="stat-sub"><em><?= $totalCandidates > 0 ? 'Nouvelles' : 'Aucune' ?></em> cette semaine</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Vues totales</span>
            <div class="stat-icon si-blue">👁</div>
          </div>
          <div class="stat-val"><?= $totalViews ?></div>
          <div class="stat-sub">Sur vos offres</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Entretiens</span>
            <div class="stat-icon si-orange">📅</div>
          </div>
          <div class="stat-val"><?= $totalInterviews ?></div>
          <div class="stat-sub">Planifiés</div>
        </div>
      </div>

      <!-- Two col -->
      <div class="two-col">

        <!-- Mes dernières offres -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">Mes dernières offres</span>
            <a href="mes_offres.php" class="panel-action">Voir tout →</a>
          </div>

          <?php if (count($myJobs) > 0): ?>
            <?php foreach($myJobs as $job): 
              $statusClass = match($job['status'] ?? 'pending') {
                'approved' => 'status-approved',
                'rejected' => 'status-rejected',
                default => 'status-pending'
              };
              $statusLabel = match($job['status'] ?? 'pending') {
                'approved' => 'Approuvée',
                'rejected' => 'Rejetée',
                default => 'En attente'
              };
            ?>
            <div class="job-item">
              <div class="job-co" style="background:rgba(139,92,246,.15);color:#a78bfa;">
                <?= strtoupper(substr($job['title'], 0, 1)) ?>
              </div>
              <div class="job-info">
                <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
                <div class="job-meta"><?= htmlspecialchars($job['company']) ?> · <?= htmlspecialchars($job['city'] ?? 'Tunisie') ?></div>
              </div>
              <span class="job-status <?= $statusClass ?>"><?= $statusLabel ?></span>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="display:block;margin:0 auto 12px;">
                <rect width="20" height="14" x="2" y="7" rx="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
              </svg>
              <p>Aucune offre publiée pour le moment.</p>
              <a href="jobs.php" class="btn-primary" style="margin-top:16px;display:inline-block;">Publier ma première offre</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- Conseils -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">Conseils recruteur</span>
          </div>
          <div class="job-item" style="border:none;">
            <div class="job-co" style="background:rgba(52,211,153,.12);color:#34d399;">1</div>
            <div class="job-info">
              <div class="job-title">Soyez précis dans le titre</div>
              <div class="job-meta">Un titre clair attire 3x plus de candidats</div>
            </div>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(96,165,250,.12);color:#60a5fa;">2</div>
            <div class="job-info">
              <div class="job-title">Indiquez le salaire</div>
              <div class="job-meta">Les offres avec salaire reçoivent 40% plus de candidatures</div>
            </div>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(251,146,60,.12);color:#fb923c;">3</div>
            <div class="job-info">
              <div class="job-title">Répondez rapidement</div>
              <div class="job-meta">Répondez sous 48h pour garder les meilleurs talents</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>