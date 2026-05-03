<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

/* ROUTER */
$page = $_GET["page"] ?? "dashboard";

/* ================= USERS ================= */
$search = $_GET["search"] ?? "";

if ($search != "") {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}
$users = $stmt->fetchAll();

/* ================= TALENTS ================= */
$talents = $pdo->query("SELECT * FROM talents ORDER BY id DESC")->fetchAll();

/* ================= JOBS ================= */
$jobs = $pdo->query("SELECT * FROM jobs ORDER BY id DESC")->fetchAll();

/* ================= STATS ================= */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalTalents = $pdo->query("SELECT COUNT(*) FROM talents")->fetchColumn();
$totalJobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin — HireTounsi</title>

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
  --green:   #22c55e;
  --red:     #ef4444;
  --sidebar: 230px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg);
  color:var(--text);
  display:flex;
  min-height:100vh;
}

/* ── AMBIENT ── */
body::before {
  content:''; position:fixed; top:-200px; left:0;
  width:500px; height:500px;
  background:radial-gradient(circle,rgba(139,92,246,.07) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

/* ════════════════════════════════
   SIDEBAR
════════════════════════════════ */
.sidebar {
  width:var(--sidebar);
  height:100vh;
  background:var(--surface2);
  border-right:1px solid var(--border);
  padding:28px 16px;
  position:fixed;
  top:0; left:0;
  display:flex;
  flex-direction:column;
  gap:4px;
  z-index:50;
}

.sidebar-logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:18px;
  letter-spacing:-.5px;
  padding:0 10px;
  margin-bottom:28px;
  display:flex; align-items:center; gap:8px;
}

.sidebar-logo span { color:var(--accent); }

.sidebar-logo .badge-admin {
  background:rgba(139,92,246,.15);
  border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; font-size:10px; font-weight:700;
  padding:2px 7px; border-radius:20px; letter-spacing:.05em;
}

.sidebar-label {
  font-size:10px; font-weight:700; letter-spacing:.1em;
  text-transform:uppercase; color:var(--muted2);
  padding:0 10px; margin:12px 0 6px;
}

.sidebar a {
  display:flex; align-items:center; gap:10px;
  padding:10px 12px;
  color:var(--muted); text-decoration:none;
  border-radius:10px; font-size:14px; font-weight:500;
  transition:.2s;
}
.sidebar a:hover { background:var(--surface); color:var(--text); }
.sidebar a.active {
  background:rgba(139,92,246,.12);
  color:#a78bfa;
  border:1px solid rgba(139,92,246,.2);
}

.sidebar-icon {
  width:28px; height:28px; border-radius:7px;
  display:flex; align-items:center; justify-content:center;
  background:rgba(255,255,255,.04); flex-shrink:0;
  font-size:14px;
}

.sidebar-bottom {
  margin-top:auto;
  border-top:1px solid var(--border);
  padding-top:12px;
}

/* ════════════════════════════════
   MAIN
════════════════════════════════ */
.main {
  margin-left:var(--sidebar);
  padding:36px 32px;
  width:100%;
  position:relative; z-index:1;
}

/* ── PAGE HEADER ── */
.page-header {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:32px;
}

.page-header h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:28px; letter-spacing:-.7px;
}

.page-header h1 span {
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  background-clip:text;
}

/* ── STAT CARDS ── */
.stats-grid {
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:16px;
  margin-bottom:32px;
}

.stat-card {
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:16px;
  padding:24px;
  position:relative; overflow:hidden;
  animation:up .4s ease both;
}

.stat-card::before {
  content:''; position:absolute;
  top:0; left:0; right:0; height:2px;
}
.stat-card:nth-child(1)::before { background:linear-gradient(90deg,var(--accent),#7c3aed); }
.stat-card:nth-child(2)::before { background:linear-gradient(90deg,var(--accent2),#0284c7); }
.stat-card:nth-child(3)::before { background:linear-gradient(90deg,#f59e0b,#ef4444); }

.stat-label {
  font-size:12px; font-weight:600; letter-spacing:.06em;
  text-transform:uppercase; color:var(--muted); margin-bottom:10px;
  display:flex; align-items:center; gap:6px;
}

.stat-value {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:40px; letter-spacing:-1.5px;
  line-height:1;
}

.stat-card:nth-child(1) .stat-value { color:#a78bfa; }
.stat-card:nth-child(2) .stat-value { color:#22d3ee; }
.stat-card:nth-child(3) .stat-value { color:#fbbf24; }

/* ── TABLE ── */
.table-wrap {
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:16px;
  overflow:hidden;
  animation:up .4s ease both;
}

table { width:100%; border-collapse:collapse; }

thead { background:rgba(255,255,255,.02); }

th {
  padding:14px 18px;
  font-size:11px; font-weight:700; letter-spacing:.07em;
  text-transform:uppercase; color:var(--muted);
  text-align:left; border-bottom:1px solid var(--border);
}

td {
  padding:14px 18px;
  font-size:14px;
  border-bottom:1px solid var(--border);
  color:var(--text);
}

tr:last-child td { border-bottom:none; }

tbody tr { transition:.15s; }
tbody tr:hover { background:rgba(255,255,255,.02); }

.td-id {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:700; color:var(--muted2); font-size:13px;
}

.td-avatar {
  width:32px; height:32px; border-radius:8px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  display:inline-flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:12px; color:white;
  margin-right:8px; vertical-align:middle;
}

.td-name { font-weight:600; }
.td-email { color:var(--muted); font-size:13px; }

/* ── TALENT / JOB CARDS ── */
.items-grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:14px;
}

.item-card {
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:14px;
  padding:20px;
  transition:.2s;
  animation:up .4s ease both;
}

.item-card:hover { border-color:rgba(139,92,246,.3); }

.item-card-top {
  display:flex; align-items:center; gap:12px;
  margin-bottom:14px;
}

.item-avatar {
  width:42px; height:42px; border-radius:11px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; color:white; flex-shrink:0;
}

.item-avatar.job-av {
  background:linear-gradient(135deg,#f59e0b,#ef4444);
}

.item-name {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
}

.item-sub { font-size:12px; color:var(--muted); margin-top:2px; }

.item-divider { height:1px; background:var(--border); margin:14px 0; }

.item-footer { display:flex; align-items:center; justify-content:space-between; }

/* Status badge */
.status {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:20px;
  font-size:11px; font-weight:700; letter-spacing:.04em;
  text-transform:uppercase;
}
.status.pending  { background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.3); color:#fbbf24; }
.status.approved { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); color:#4ade80; }
.status.rejected { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); color:#f87171; }

.status-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }

/* Action buttons */
.actions { display:flex; gap:6px; }

.btn {
  display:inline-flex; align-items:center; gap:5px;
  padding:6px 12px; border-radius:7px;
  color:white; text-decoration:none; font-size:12px; font-weight:600;
  font-family:'Instrument Sans',sans-serif;
  transition:.2s; border:none; cursor:pointer;
}

.approve {
  background:rgba(34,197,94,.15);
  border:1px solid rgba(34,197,94,.3);
  color:#4ade80;
}
.approve:hover { background:rgba(34,197,94,.25); }

.reject {
  background:rgba(239,68,68,.12);
  border:1px solid rgba(239,68,68,.3);
  color:#f87171;
}
.reject:hover { background:rgba(239,68,68,.22); }

/* ── SEARCH ── */
.search-bar {
  display:flex; gap:10px; margin-bottom:24px;
}

.search-wrap { position:relative; flex:1; }
.search-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; }

.search-input {
  width:100%; padding:11px 14px 11px 42px;
  background:var(--surface2); border:1px solid var(--border);
  border-radius:10px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none;
  transition:.2s;
}
.search-input::placeholder { color:var(--muted2); }
.search-input:focus { border-color:rgba(139,92,246,.5); box-shadow:0 0 0 3px rgba(139,92,246,.1); }

.btn-search {
  background:var(--accent); border:none; padding:11px 20px;
  border-radius:10px; color:white; cursor:pointer;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:14px;
  transition:.2s;
}
.btn-search:hover { background:#7c3aed; }

/* ── EMPTY ── */
.empty {
  text-align:center; padding:48px;
  color:var(--muted2); font-size:14px;
}
.empty svg { margin-bottom:12px; opacity:.3; }

@keyframes up {
  from{opacity:0;transform:translateY(12px)}
  to{opacity:1;transform:translateY(0)}
}
</style>
</head>

<body>

<!-- ════════════ SIDEBAR ════════════ -->
<div class="sidebar">

  <div class="sidebar-logo">
    <span>Hire<span>Tounsi</span></span>
    <span class="badge-admin">ADMIN</span>
  </div>

  <span class="sidebar-label">Navigation</span>

  <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">
    <span class="sidebar-icon">🏠</span> Dashboard
  </a>

  <a href="?page=users" class="<?= $page=='users'?'active':'' ?>">
    <span class="sidebar-icon">👤</span> Utilisateurs
  </a>

  <a href="?page=talents" class="<?= $page=='talents'?'active':'' ?>">
    <span class="sidebar-icon">🎯</span> Talents
  </a>

  <a href="?page=jobs" class="<?= $page=='jobs'?'active':'' ?>">
    <span class="sidebar-icon">💼</span> Offres d'emploi
  </a>

  <div class="sidebar-bottom">
    <a href="logout.php">
      <span class="sidebar-icon">🚪</span> Déconnexion
    </a>
  </div>

</div>

<!-- ════════════ MAIN ════════════ -->
<div class="main">

<!-- ─── DASHBOARD ─── -->
<?php if($page == "dashboard"): ?>

  <div class="page-header">
    <h1>Tableau de <span>bord</span></h1>
  </div>

  <div class="stats-grid">

    <div class="stat-card">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Utilisateurs
      </div>
      <div class="stat-value"><?= $totalUsers ?></div>
    </div>

    <div class="stat-card">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        Talents
      </div>
      <div class="stat-value"><?= $totalTalents ?></div>
    </div>

    <div class="stat-card">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Offres d'emploi
      </div>
      <div class="stat-value"><?= $totalJobs ?></div>
    </div>

  </div>

  <!-- Recent activity placeholder -->
  <div class="table-wrap" style="padding:24px">
    <div style="font-family:'Cabinet Grotesk',sans-serif;font-weight:800;font-size:16px;margin-bottom:4px">Activité récente</div>
    <div style="font-size:13px;color:var(--muted)">Bienvenue dans le panneau d'administration HireTounsi.</div>
  </div>


<!-- ─── USERS ─── -->
<?php elseif($page == "users"): ?>

  <div class="page-header">
    <h1>Utilis<span>ateurs</span></h1>
  </div>

  <form method="GET" class="search-bar">
    <input type="hidden" name="page" value="users">
    <div class="search-wrap">
      <span class="search-icon">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </span>
      <input class="search-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher par nom ou email…">
    </div>
    <button class="btn-search" type="submit">Rechercher</button>
  </form>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Utilisateur</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($users as $u):




          
          
          
          ?>
        <tr>
          <td class="td-id"><?= $u["id"] ?></td>
          <td>
            <span class="td-avatar"><?= strtoupper(substr($u["name"],0,2)) ?></span>
            <span class="td-name"><?= htmlspecialchars($u["name"]) ?></span>
          </td>
          <td class="td-email"><?= htmlspecialchars($u["email"]) ?></td>
        </tr>
        <?php endforeach; ?>

        <?php if(empty($users)): ?>
        <tr><td colspan="3" style="text-align:center;color:var(--muted2);padding:32px">Aucun utilisateur trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>


<!-- ─── TALENTS ─── -->
<?php elseif($page == "talents"): ?>

  <div class="page-header">
    <h1>Gestion des <span>Talents</span></h1>
  </div>

  <?php if(empty($talents)): ?>
    <div class="empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
      <div>Aucun talent enregistré</div>
    </div>
  <?php else: ?>
  <div class="items-grid">
    <?php foreach($talents as $t): ?>
    <div class="item-card">

      <div class="item-card-top">
        <div class="item-avatar"><?= strtoupper(substr($t["name"],0,2)) ?></div>
        <div>
          <div class="item-name"><?= htmlspecialchars($t["name"]) ?></div>
          <div class="item-sub"><?= htmlspecialchars($t["skill"]) ?></div>
        </div>
      </div>

      <div class="item-divider"></div>

      <div class="item-footer">
        <span class="status <?= $t['status'] ?>">
          <span class="status-dot"></span>
          <?= ucfirst($t["status"]) ?>
        </span>

        <?php if($t["status"] == "pending"): ?>
        <div class="actions">
          <a class="btn approve" href="approve.php?id=<?= $t['id'] ?>">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
            Approuver
          </a>
          <a class="btn reject" href="reject.php?id=<?= $t['id'] ?>">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
            Rejeter
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>


<!-- ─── JOBS ─── -->
<?php elseif($page == "jobs"): ?>

  <div class="page-header">
    <h1>Offres d'<span>emploi</span></h1>
  </div>

  <?php if(empty($jobs)): ?>
    <div class="empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      <div>Aucune offre enregistrée</div>
    </div>
  <?php else: ?>
  <div class="items-grid">
    <?php foreach($jobs as $job): ?>
    <div class="item-card">

      <div class="item-card-top">
        <div class="item-avatar job-av">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
          <div class="item-name"><?= htmlspecialchars($job["title"]) ?></div>
          <div class="item-sub"><?= htmlspecialchars($job["company"]) ?></div>
        </div>
      </div>

      <div class="item-divider"></div>

      <div class="item-footer">
        <span class="status <?= $job['status'] ?>">
          <span class="status-dot"></span>
          <?= ucfirst($job["status"]) ?>
        </span>

        <?php if($job["status"] == "pending"): ?>
        <div class="actions">
          <a class="btn approve" href="approve_job.php?id=<?= $job['id'] ?>">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
            Approuver
          </a>
          <a class="btn reject" href="reject_job.php?id=<?= $job['id'] ?>">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
            Rejeter
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

<?php endif; ?>

</div><!-- /main -->

</body>
</html>