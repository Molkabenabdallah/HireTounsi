<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

// Récupérer user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();

$userName = htmlspecialchars($userData["name"]);
$initials = strtoupper(substr($userName, 0, 1));
$joinDate  = isset($userData['created_at']) ? date('M Y', strtotime($userData['created_at'])) : 'N/A';

// ===== CALCUL PROFIL =====
$fields = [
    !empty($userData['name']),
    !empty($userData['email']),
    !empty($userData['photo']),
    !empty($userData['cv']),
    !empty($userData['skills'])
];

$completed = array_sum($fields);
$total = count($fields);
$progress = intval(($completed / $total) * 100);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HireTounsi – Dashboard</title>
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
      --sw: 240px;
    }

    body {
      font-family: 'Instrument Sans', sans-serif;
      background: var(--bg); color: var(--text);
      min-height: 100vh; display: flex; overflow-x: hidden;
    }

    /* glow */
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

    /* logo */
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
      background: linear-gradient(130deg, rgba(139,92,246,.15) 0%, rgba(6,182,212,.07) 100%);
      border: 1px solid rgba(139,92,246,.2); border-radius: 18px;
      padding: 28px 32px; margin-bottom: 28px;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 18px; position: relative; overflow: hidden;
    }
    .welcome-banner::before {
      content:''; position:absolute; top:0; left:0; right:0; height:1px;
      background:linear-gradient(90deg,transparent,rgba(139,92,246,.5),transparent);
    }
    .welcome-banner h2 {
      font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900;
      font-size: 22px; letter-spacing: -.5px; margin-bottom: 5px;
    }
    .welcome-banner h2 span { color: var(--accent); }
    .welcome-banner p { color: var(--muted); font-size: 13px; }
    .welcome-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent), #7c3aed);
      color: #fff; border: none; padding: 10px 20px; border-radius: 10px;
      font-family: 'Instrument Sans', sans-serif; font-size: 13px; font-weight: 600;
      cursor: pointer; text-decoration: none; transition: all .2s;
      box-shadow: 0 4px 16px rgba(139,92,246,.35);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(139,92,246,.5); }
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
    .stat-card:hover { border-color: rgba(139,92,246,.3); transform: translateY(-2px); }
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
    .stat-val {
      font-family: 'Cabinet Grotesk', sans-serif;
      font-weight: 900; font-size: 32px; letter-spacing: -1px; margin-bottom: 3px;
    }
    .stat-sub { font-size: 11px; color: var(--muted); }
    .stat-sub em { color: var(--green); font-style: normal; font-weight: 600; }

    /* two col */
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
    .panel-action { font-size: 12px; color: var(--accent); text-decoration: none; }
    .panel-action:hover { text-decoration: underline; }

    /* job items */
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
    .job-company { font-size: 11px; color: var(--muted); }
    .job-badge { font-size: 10px; padding: 3px 9px; border-radius: 20px; font-weight: 700; white-space: nowrap; }
    .badge-new    { background: rgba(52,211,153,.12); color: var(--green); border: 1px solid rgba(52,211,153,.2); }
    .badge-urgent { background: rgba(251,146,60,.12); color: var(--orange); border: 1px solid rgba(251,146,60,.2); }
    .badge-hot    { background: rgba(139,92,246,.15); color: #c4b5fd; border: 1px solid rgba(139,92,246,.3); }

    /* profile completion */
    .progress-wrap { margin-bottom: 18px; }
    .progress-top { display: flex; justify-content: space-between; margin-bottom: 7px; font-size: 12px; }
    .progress-top strong { color: var(--accent); }
    .progress-bar { height: 5px; background: rgba(255,255,255,.07); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--accent), #7c3aed); }
    .completion-list { display: flex; flex-direction: column; gap: 11px; }
    .completion-item { display: flex; align-items: center; gap: 10px; }
    .check {
      width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 10px;
    }
    .done { background: rgba(52,211,153,.15); color: var(--green); }
    .todo { background: rgba(255,255,255,.05); color: var(--muted); border: 1px solid var(--border); }
    .completion-text { flex: 1; font-size: 13px; }
    .completion-text span { color: var(--muted); font-size: 11px; display: block; margin-top: 1px; }

    @keyframes up {
      from{opacity:0;transform:translateY(10px)}
      to{opacity:1;transform:translateY(0)}
    }

    @media (max-width: 900px) {
      .sidebar { display: none; }
      .main { margin-left: 0; }
      .two-col { grid-template-columns: 1fr; }
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
        <a href="switch_role.php?role=candidat" class="role-btn active" id="btn-candidat">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Candidat
        </a>
        <a href="switch_role.php?role=recruteur" class="role-btn" id="btn-recruteur">
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
      <a href="#" class="nav-item active">
        <span class="nav-icon">🏠</span> Tableau de bord
      </a>
      <a href="jobs.php" class="nav-item">
        <span class="nav-icon">🔍</span> Offres d'emploi
      </a>
      <a href="mes_candidatures.php" class="nav-item">
        <span class="nav-icon">📋</span> Mes candidatures
      </a>
      <a href="company.php" class="nav-item">
        <span class="nav-icon">🏢</span> Entreprises
      </a>
      <a href="talents.php" class="nav-item">
        <span class="nav-icon">👥</span> Talents
      </a>
      <span class="nav-sep">Compte</span>
      <a href="#" class="nav-item">
        <span class="nav-icon">💬</span> Messages
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">⚙️</span> Paramètres
      </a>
    </nav>

    <!-- User pill -->
    <div class="sidebar-bottom">
      <div class="user-pill">










  <div class="avatar-sm">
  <?php if (!empty($userData['profile_photo'])): ?>
    <img src="uploads/<?= htmlspecialchars($userData['profile_photo']) ?>" 
         style="width:100%;height:100%;object-fit:cover;border-radius:9px;">
  <?php else: ?>
    <?= $initials ?>
  <?php endif; ?>
</div>

















        <div class="user-pill-info">
          <div class="upn"><?= $userName ?></div>
          <div class="upr">Candidat · Membre depuis <?= $joinDate ?></div>
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
        <p>Voici votre activité du jour</p>
      </div>
      <div class="topbar-right">
        <a href="logout.php" class="topbar-logout">Déconnexion</a>









    <div class="avatar-lg">
  <?php if (!empty($userData['profile_photo'])): ?>
    <img src="uploads/<?= htmlspecialchars($userData['profile_photo']) ?>" 
         style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
  <?php else: ?>
    <?= $initials ?>
  <?php endif; ?>
</div>












      </div>
    </div>

    <!-- Content -->
    <div class="content">

      <!-- Welcome banner -->
      <div class="welcome-banner">
        <div>
          <h2>Bienvenue, <span><?= $userName ?></span> !</h2>
          <p>Votre compte a été créé le <?= $joinDate ?>. Complétez votre profil pour augmenter vos chances.</p>
        </div>
        <div class="welcome-actions">
          <a href="jobs.php" class="btn-primary">Voir les offres</a>
          <a href="profile.php" class="btn-secondary">Mon profil</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Offres disponibles</span>
            <div class="stat-icon si-blue">💼</div>
          </div>
          <div class="stat-val">1 248</div>
          <div class="stat-sub"><em>+42</em> cette semaine</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Candidatures</span>
            <div class="stat-icon si-purple">📋</div>
          </div>
          <div class="stat-val">0</div>
          <div class="stat-sub">Postulez à votre première offre</div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Profil vu</span>
            <div class="stat-icon si-green">👁</div>
          </div>
          <div class="stat-val">0</div>
          <div class="stat-sub">Complétez votre profil</div>
        </div>
       <!-- <div class="stat-card">
          <div class="stat-top">
            <span class="stat-label">Entretiens</span>
            <div class="stat-icon si-orange">📅</div>
          </div>
          <div class="stat-val">0</div>
          <div class="stat-sub">Aucun entretien planifié</div>
        </div>-->
      </div>

      <!-- Two col -->
      <div class="two-col">

        <!-- Offres récentes -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">Offres récentes</span>
            <a href="jobs.php" class="panel-action">Voir tout →</a>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(96,165,250,.12);color:#60a5fa;">V</div>
            <div class="job-info">
              <div class="job-title">Développeur Front-end</div>
              <div class="job-company">Valeo · Tunis</div>
            </div>
            <span class="job-badge badge-new">Nouveau</span>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(52,211,153,.12);color:#34d399;">T</div>
            <div class="job-info">
              <div class="job-title">Chef de projet IT</div>
              <div class="job-company">Telnet · Sfax</div>
            </div>
            <span class="job-badge badge-urgent">Urgent</span>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(139,92,246,.15);color:#a78bfa;">V</div>
            <div class="job-info">
              <div class="job-title">Data Analyst</div>
              <div class="job-company">Vermeg · Remote</div>
            </div>
            <span class="job-badge badge-hot">🔥 Hot</span>
          </div>
          <div class="job-item">
            <div class="job-co" style="background:rgba(251,146,60,.12);color:#fb923c;">O</div>
            <div class="job-info">
              <div class="job-title">UX Designer</div>
              <div class="job-company">Orange TN · Tunis</div>
            </div>
            <span class="job-badge badge-new">Nouveau</span>
          </div>
        </div>

        <!-- Profil -->
        <!-- Profil -->
<div class="panel">
  <div class="panel-header">
    <span class="panel-title">Compléter le profil</span>
    <a href="edit_profile.php" class="panel-action">Modifier →</a>
  </div>

  <div class="progress-wrap">
    <div class="progress-top">
      <span style="color:var(--muted)">Progression</span>
      <strong><?= $progress ?>%</strong>
    </div>
    <div class="progress-bar">
      <div class="progress-fill" style="width:<?= $progress ?>%"></div>
    </div>
  </div>

  <div class="completion-list">

    <div class="completion-item">
      <div class="check done">✓</div>
      <div class="completion-text">Créer un compte <span>Terminé</span></div>
    </div>

    <div class="completion-item">
      <div class="check <?= !empty($userData['photo']) ? 'done' : 'todo' ?>">
        <?= !empty($userData['photo']) ? '✓' : '○' ?>
      </div>
      <div class="completion-text">
        Ajouter une photo
        <span><?= !empty($userData['photo']) ? 'Terminé' : 'Non complété' ?></span>
      </div>
    </div>

    <div class="completion-item">
      <div class="check <?= !empty($userData['cv']) ? 'done' : 'todo' ?>">
        <?= !empty($userData['cv']) ? '✓' : '○' ?>
      </div>
      <div class="completion-text">
        Ajouter un CV
        <span><?= !empty($userData['cv']) ? 'Terminé' : 'Non complété' ?></span>
      </div>
    </div>

    <div class="completion-item">
      <div class="check <?= !empty($userData['skills']) ? 'done' : 'todo' ?>">
        <?= !empty($userData['skills']) ? '✓' : '○' ?>
      </div>
      <div class="completion-text">
        Ajouter compétences
        <span><?= !empty($userData['skills']) ? 'Terminé' : 'Non complété' ?></span>
      </div>
    </div>

    <div class="completion-item">
      <div class="check todo">○</div>
      <div class="completion-text">
        Première candidature
        <span>Non complété</span>
      </div>
    </div>

  </div>
</div>
  <script>
    // Highlight actif selon l'URL courante (optionnel)
    const params = new URLSearchParams(window.location.search);
    const role = params.get('role');
    if (role === 'recruteur') {
      document.getElementById('btn-recruteur').classList.add('active');
      document.getElementById('btn-candidat').classList.remove('active');
    }
  </script>

</body>
</html>