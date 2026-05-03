<?php
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userName = htmlspecialchars($_SESSION["user_name"]);
// Récupérer les infos complètes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
$stmt->execute([$_SESSION["user_name"]]);
$userData = $stmt->fetch();
$initials = strtoupper(substr($userName, 0, 1));
$joinDate  = isset($userData['created_at']) ? date('M Y', strtotime($userData['created_at'])) : 'N/A';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HireTounsi – Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:       #0c0c18;
      --sidebar:  #0f0f22;
      --card:     #15152c;
      --card2:    #1a1a38;
      --border:   rgba(255,255,255,0.07);
      --purple:   #7c3aed;
      --purple2:  #9d5bfc;
      --accent:   #a78bfa;
      --text:     #f0eeff;
      --muted:    #8a89aa;
      --green:    #34d399;
      --blue:     #60a5fa;
      --orange:   #fb923c;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg); color: var(--text);
      min-height: 100vh; display: flex; overflow-x: hidden;
    }

    /* BLOBS */
    .blob { position: fixed; border-radius: 50%; filter: blur(110px); pointer-events: none; z-index: 0; }
    .blob-a { width: 480px; height: 480px; background: #2e0f7a; opacity: .32; top: -100px; right: -80px; }
    .blob-b { width: 320px; height: 320px; background: #1a0a50; opacity: .35; bottom: -60px; left: 220px; }

    /* SIDEBAR */
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0; width: 240px;
      background: var(--sidebar); border-right: 1px solid var(--border);
      display: flex; flex-direction: column; z-index: 50;
      padding: 28px 0;
    }
    .sidebar-logo {
      padding: 0 24px 28px;
      border-bottom: 1px solid var(--border);
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.15rem;
      color: #fff; display: flex; align-items: center; gap: 9px; text-decoration: none;
    }
    .sidebar-logo .logo-box {
      width: 32px; height: 32px; background: var(--purple); border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: .82rem; font-weight: 900;
      box-shadow: 0 0 14px rgba(124,58,237,.5);
    }
    .sidebar-logo em { color: var(--purple2); font-style: normal; }

    .sidebar-nav { flex: 1; padding: 22px 14px; display: flex; flex-direction: column; gap: 4px; }
    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 14px; border-radius: 10px;
      color: var(--muted); text-decoration: none; font-size: .875rem;
      transition: background .2s, color .2s;
    }
    .nav-item:hover { background: rgba(255,255,255,.05); color: var(--text); }
    .nav-item.active { background: rgba(124,58,237,.18); color: var(--accent); }
    .nav-item.active .nav-icon { color: var(--purple2); }
    .nav-icon { font-size: 1rem; flex-shrink: 0; }
    .nav-label { font-weight: 400; }

    .sidebar-bottom { padding: 18px 14px; border-top: 1px solid var(--border); }
    .user-pill {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px; border-radius: 10px;
      background: rgba(255,255,255,.04);
    }
    .avatar-small {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, var(--purple), var(--purple2));
      display: flex; align-items: center; justify-content: center;
      font-family: 'Syne', sans-serif; font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .user-pill-info { flex: 1; min-width: 0; }
    .user-pill-name { font-size: .82rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-pill-role { font-size: .72rem; color: var(--muted); }
    .logout-btn {
      color: var(--muted); text-decoration: none; font-size: .88rem;
      padding: 6px; border-radius: 6px; transition: color .2s;
      display: flex; align-items: center;
    }
    .logout-btn:hover { color: #f87171; }

    /* MAIN */
    .main {
      margin-left: 240px; flex: 1; position: relative; z-index: 1;
      display: flex; flex-direction: column;
    }

    /* TOP BAR */
    .topbar {
      padding: 22px 36px; border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: rgba(12,12,24,.7); backdrop-filter: blur(16px);
      position: sticky; top: 0; z-index: 40;
    }
    .topbar-left h1 {
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.35rem;
    }
    .topbar-left p { color: var(--muted); font-size: .83rem; margin-top: 2px; }
    .topbar-right { display: flex; align-items: center; gap: 14px; }
    .avatar-large {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, var(--purple), var(--purple2));
      display: flex; align-items: center; justify-content: center;
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: .95rem;
      box-shadow: 0 0 18px rgba(124,58,237,.4); cursor: pointer;
    }

    /* CONTENT */
    .content { padding: 36px; flex: 1; }

    /* WELCOME BANNER */
    .welcome-banner {
      background: linear-gradient(130deg, rgba(124,58,237,.2) 0%, rgba(157,91,252,.1) 100%);
      border: 1px solid rgba(124,58,237,.25); border-radius: 20px;
      padding: 32px 36px; margin-bottom: 32px;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;
    }
    .welcome-banner h2 {
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.55rem;
      margin-bottom: 6px;
    }
    .welcome-banner h2 span { color: var(--accent); }
    .welcome-banner p { color: var(--muted); font-size: .88rem; }
    .welcome-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-primary {
      background: var(--purple); color: #fff; border: none;
      padding: 11px 22px; border-radius: 10px;
      font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 500;
      cursor: pointer; text-decoration: none; transition: background .2s, box-shadow .2s;
      box-shadow: 0 0 18px rgba(124,58,237,.4);
    }
    .btn-primary:hover { background: var(--purple2); box-shadow: 0 0 26px rgba(124,58,237,.6); }
    .btn-secondary {
      background: rgba(255,255,255,.06); color: var(--text);
      border: 1px solid var(--border); padding: 11px 22px; border-radius: 10px;
      font-family: 'DM Sans', sans-serif; font-size: .875rem; cursor: pointer;
      text-decoration: none; transition: background .2s;
    }
    .btn-secondary:hover { background: rgba(255,255,255,.1); }

    /* STATS GRID */
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 18px; margin-bottom: 32px;
    }
    .stat-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 24px 22px;
      transition: border-color .25s, transform .25s;
    }
    .stat-card:hover { border-color: rgba(124,58,237,.3); transform: translateY(-2px); }
    .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .stat-label { font-size: .78rem; color: var(--muted); font-weight: 500; letter-spacing: .3px; }
    .stat-icon {
      width: 36px; height: 36px; border-radius: 9px;
      display: flex; align-items: center; justify-content: center; font-size: 1rem;
    }
    .stat-icon.green { background: rgba(52,211,153,.12); }
    .stat-icon.blue  { background: rgba(96,165,250,.12); }
    .stat-icon.purple{ background: rgba(124,58,237,.15); }
    .stat-icon.orange{ background: rgba(251,146,60,.12); }
    .stat-value {
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.8rem; margin-bottom: 4px;
    }
    .stat-sub { font-size: .75rem; color: var(--muted); }
    .stat-sub em { color: var(--green); font-style: normal; font-weight: 500; }

    /* TWO-COL */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
    .panel {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 24px;
    }
    .panel-header {
      display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
    }
    .panel-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: .95rem; }
    .panel-action { font-size: .78rem; color: var(--accent); text-decoration: none; }
    .panel-action:hover { text-decoration: underline; }

    /* Job items */
    .job-item {
      display: flex; align-items: center; gap: 14px;
      padding: 12px 0; border-bottom: 1px solid var(--border);
    }
    .job-item:last-child { border-bottom: none; padding-bottom: 0; }
    .job-co-logo {
      width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: .9rem; font-weight: 700;
    }
    .job-info { flex: 1; min-width: 0; }
    .job-title { font-size: .87rem; font-weight: 500; margin-bottom: 2px; }
    .job-company { font-size: .75rem; color: var(--muted); }
    .job-badge {
      font-size: .7rem; padding: 3px 9px; border-radius: 50px; font-weight: 500;
    }
    .badge-new    { background: rgba(52,211,153,.14); color: var(--green); }
    .badge-urgent { background: rgba(251,146,60,.14); color: var(--orange); }
    .badge-hot    { background: rgba(124,58,237,.18); color: var(--accent); }

    /* Profile completion */
    .completion-list { display: flex; flex-direction: column; gap: 14px; }
    .completion-item { display: flex; align-items: center; gap: 12px; }
    .completion-check {
      width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: .7rem;
    }
    .done   { background: rgba(52,211,153,.18); color: var(--green); }
    .todo   { background: rgba(255,255,255,.07); color: var(--muted); border: 1px solid var(--border); }
    .completion-text { flex: 1; font-size: .85rem; }
    .completion-text span { color: var(--muted); font-size: .75rem; display: block; margin-top: 1px; }

    /* Progress bar */
    .progress-wrap { margin-bottom: 20px; }
    .progress-top { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: .78rem; }
    .progress-top strong { color: var(--accent); }
    .progress-bar { height: 6px; background: rgba(255,255,255,.07); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--purple), var(--purple2)); }

    @media (max-width: 900px) {
      .sidebar { display: none; }
      .main { margin-left: 0; }
      .two-col { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
      .content { padding: 20px; }
      .topbar { padding: 16px 20px; }
    }
  </style>
</head>
<body>

  <div class="blob blob-a"></div>
  <div class="blob blob-b"></div>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <a href="index.html" class="sidebar-logo">
      <img src="imgs/logo.png" alt="HireTounsi" width="5%">
      Hire<em>Tounsi</em>
    </a>

    <nav class="sidebar-nav">
      <a href="#" class="nav-item active">
        <span class="nav-icon">🏠</span>
        <span class="nav-label">Tableau de bord</span>
      </a>
      <a href="jobs.php" class="nav-item">
        <span class="nav-icon">🔍</span>
        <span class="nav-label">Offres d'emploi</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">📋</span>
        <span class="nav-label">Mes candidatures</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">🏢</span>
        <span class="nav-label">Entreprises</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">👥</span>
        <span class="nav-label">Talents</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">💬</span>
        <span class="nav-label">Messages</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">⚙️</span>
        <span class="nav-label">Paramètres</span>
      </a>
    </nav>

    <div class="sidebar-bottom">
      <div class="user-pill">
        <div class="avatar-small"><?= $initials ?></div>
        <div class="user-pill-info">
          <div class="user-pill-name"><?= $userName ?></div>
          <div class="user-pill-role">Candidat</div>
        </div>
        <a href="logout.php" class="logout-btn" title="Se déconnecter">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
          </svg>
        </a>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-left">
        <h1>Bonjour, <?= $userName ?> 👋</h1>
        <p>Voici votre activité du jour</p>
      </div>
      <div class="topbar-right">
        <a href="logout.php" style="color:var(--muted);text-decoration:none;font-size:.85rem;
           background:rgba(255,255,255,.05);padding:8px 16px;border-radius:8px;border:1px solid var(--border);
           transition:color .2s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='var(--muted)'">
          Déconnexion
        </a>
        <div class="avatar-large"><?= $initials ?></div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

      <!-- WELCOME BANNER -->
      <div class="welcome-banner">
        <div>
          <h2>Bienvenue, <span><?= $userName ?></span> !</h2>
          <p>Votre compte a été créé le <?= $joinDate ?>. Complétez votre profil pour augmenter vos chances.</p>
        </div>
        <div class="welcome-actions">
          <a href="#" class="btn-primary">Voir les offres</a>
          <a href="#" class="btn-secondary">Mon profil</a>
        </div>
      </div>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-label">OFFRES DISPONIBLES</span>
            <div class="stat-icon blue">💼</div>
          </div>
          <div class="stat-value">1 248</div>
          <div class="stat-sub"><em>+42</em> cette semaine</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-label">CANDIDATURES</span>
            <div class="stat-icon purple">📋</div>
          </div>
          <div class="stat-value">0</div>
          <div class="stat-sub">Postulez à votre première offre</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-label">PROFIL VU</span>
            <div class="stat-icon green">👁</div>
          </div>
          <div class="stat-value">0</div>
          <div class="stat-sub">Complétez votre profil</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-label">ENTRETIENS</span>
            <div class="stat-icon orange">📅</div>
          </div>
          <div class="stat-value">0</div>
          <div class="stat-sub">Aucun entretien planifié</div>
        </div>
      </div>

      <!-- TWO COL -->
      <div class="two-col">

        <!-- Recent jobs -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">Offres récentes</span>
            <a href="#" class="panel-action">Voir tout →</a>
          </div>
          <div class="job-item">
            <div class="job-co-logo" style="background:rgba(96,165,250,.14);color:#60a5fa;">V</div>
            <div class="job-info">
              <div class="job-title">Développeur Front-end</div>
              <div class="job-company">Valeo · Tunis</div>
            </div>
            <span class="job-badge badge-new">Nouveau</span>
          </div>
          <div class="job-item">
            <div class="job-co-logo" style="background:rgba(52,211,153,.12);color:#34d399;">T</div>
            <div class="job-info">
              <div class="job-title">Chef de projet IT</div>
              <div class="job-company">Telnet · Sfax</div>
            </div>
            <span class="job-badge badge-urgent">Urgent</span>
          </div>
          <div class="job-item">
            <div class="job-co-logo" style="background:rgba(124,58,237,.16);color:#a78bfa;">V</div>
            <div class="job-info">
              <div class="job-title">Data Analyst</div>
              <div class="job-company">Vermeg · Remote</div>
            </div>
            <span class="job-badge badge-hot">🔥 Hot</span>
          </div>
          <div class="job-item">
            <div class="job-co-logo" style="background:rgba(251,146,60,.12);color:#fb923c;">O</div>
            <div class="job-info">
              <div class="job-title">UX Designer</div>
              <div class="job-company">Orange TN · Tunis</div>
            </div>
            <span class="job-badge badge-new">Nouveau</span>
          </div>
        </div>

        <!-- Profile completion -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title">Compléter le profil</span>
            <a href="#" class="panel-action">Modifier →</a>
          </div>
          <div class="progress-wrap">
            <div class="progress-top">
              <span style="color:var(--muted)">Progression</span>
              <strong>20%</strong>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width:20%"></div>
            </div>
          </div>
          <div class="completion-list">
            <div class="completion-item">
              <div class="completion-check done">✓</div>
              <div class="completion-text">Créer un compte <span>Terminé</span></div>
            </div>
            <div class="completion-item">
              <div class="completion-check todo">○</div>
              <div class="completion-text">Ajouter une photo <span>Non complété</span></div>
            </div>
            <div class="completion-item">
              <div class="completion-check todo">○</div>
              <div class="completion-text">Renseigner le CV <span>Non complété</span></div>
            </div>
            <div class="completion-item">
              <div class="completion-check todo">○</div>
              <div class="completion-text">Ajouter vos compétences <span>Non complété</span></div>
            </div>
            <div class="completion-item">
              <div class="completion-check todo">○</div>
              <div class="completion-text">Première candidature <span>Non complété</span></div>
            </div>
          </div>
        </div>

      </div>
    </div><!-- /content -->
  </div><!-- /main -->

</body>
</html>
