<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

$page = $_GET["page"] ?? "dashboard";

/* USERS */
$search = $_GET["search"] ?? "";

if ($search != "") {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users");
}
$users = $stmt->fetchAll();

/* TALENTS */
$talents = $pdo->query("SELECT * FROM talents")->fetchAll();

/* JOBS */
$jobs = $pdo->query("SELECT * FROM jobs")->fetchAll();

/* STATS */
$totalUsers   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalTalents = $pdo->query("SELECT COUNT(*) FROM talents")->fetchColumn();
$totalJobs    = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — HireTounsi</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:      #07080d;
  --surface: #0e1018;
  --s2:      #161824;
  --border:  rgba(255,255,255,.07);
  --accent:  #8b5cf6;
  --accent2: #06b6d4;
  --text:    #f0eeff;
  --muted:   #7a7890;
  --muted2:  #4a4860;
  --green:   #22c55e;
  --red:     #ef4444;
  --yellow:  #f59e0b;
  --sidebar: 220px;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text);
  display:flex; min-height:100vh;
}

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
  width:var(--sidebar); min-height:100vh;
  background:var(--s2); border-right:1px solid var(--border);
  padding:24px 14px;
  position:fixed; top:0; left:0;
  display:flex; flex-direction:column; gap:3px;
  z-index:50;
}

.sidebar-logo {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:18px; letter-spacing:-.5px;
  color:var(--text); padding:0 8px; margin-bottom:6px;
  display:flex; align-items:center; gap:8px;
}
.sidebar-logo em { color:var(--accent); font-style:normal; }
.admin-badge {
  font-size:9px; font-weight:700; letter-spacing:.07em;
  background:rgba(139,92,246,.15); border:1px solid rgba(139,92,246,.3);
  color:#a78bfa; padding:2px 7px; border-radius:20px; text-transform:uppercase;
}

.sidebar-sep {
  font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
  color:var(--muted2); padding:0 8px; margin:14px 0 6px;
}

.sidebar a {
  display:flex; align-items:center; gap:10px;
  padding:9px 10px; border-radius:10px;
  color:var(--muted); text-decoration:none;
  font-size:13px; font-weight:500; transition:.2s;
}
.sidebar a:hover { background:var(--surface); color:var(--text); }
.sidebar a.active {
  background:rgba(139,92,246,.12);
  border:1px solid rgba(139,92,246,.2);
  color:#c4b5fd;
}

.s-icon {
  width:26px; height:26px; border-radius:7px;
  background:rgba(255,255,255,.04);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; flex-shrink:0;
}

.sidebar-bottom {
  margin-top:auto; padding-top:14px; border-top:1px solid var(--border);
}

/* ════════════════════════════════
   MAIN
════════════════════════════════ */
.main {
  margin-left:var(--sidebar); flex:1;
  padding:36px 32px; position:relative; z-index:1;
  min-height:100vh;
}

/* page header */
.ph {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:28px;
}
.ph h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:26px; letter-spacing:-.6px;
}
.ph h1 span {
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}

/* ── STAT CARDS ── */
.stats {
  display:grid; grid-template-columns:repeat(3,1fr); gap:14px;
  margin-bottom:28px;
}
.stat {
  background:var(--s2); border:1px solid var(--border); border-radius:14px;
  padding:22px; position:relative; overflow:hidden;
  animation:up .4s ease both;
}
.stat::after {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
}
.stat:nth-child(1)::after { background:linear-gradient(90deg,var(--accent),#7c3aed); }
.stat:nth-child(2)::after { background:linear-gradient(90deg,var(--accent2),#0891b2); }
.stat:nth-child(3)::after { background:linear-gradient(90deg,var(--yellow),#f97316); }
.stat-label {
  font-size:11px; font-weight:700; letter-spacing:.07em;
  text-transform:uppercase; color:var(--muted); margin-bottom:8px;
}
.stat-val {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:42px; letter-spacing:-2px; line-height:1;
}
.stat:nth-child(1) .stat-val { color:#c4b5fd; }
.stat:nth-child(2) .stat-val { color:#67e8f9; }
.stat:nth-child(3) .stat-val { color:#fcd34d; }

/* ── SEARCH BAR ── */
.search-bar {
  display:flex; gap:8px; margin-bottom:22px;
}
.search-wrap { position:relative; flex:1; }
.si {
  position:absolute; left:13px; top:50%; transform:translateY(-50%);
  color:var(--muted); pointer-events:none;
}
.search-input {
  width:100%; padding:10px 14px 10px 40px;
  background:var(--s2); border:1px solid var(--border);
  border-radius:10px; color:var(--text);
  font-family:'Instrument Sans',sans-serif; font-size:14px; outline:none; transition:.2s;
}
.search-input::placeholder { color:var(--muted2); }
.search-input:focus { border-color:rgba(139,92,246,.55); box-shadow:0 0 0 3px rgba(139,92,246,.1); }
.btn-search {
  background:var(--accent); border:none; padding:10px 18px; border-radius:10px;
  color:#fff; font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:14px;
  cursor:pointer; transition:.2s;
}
.btn-search:hover { background:#7c3aed; }

/* ── TABLE ── */
.table-wrap {
  background:var(--s2); border:1px solid var(--border);
  border-radius:14px; overflow:hidden; animation:up .4s ease both;
}
table { width:100%; border-collapse:collapse; }
thead { background:rgba(255,255,255,.02); }
th {
  padding:13px 16px; font-size:10px; font-weight:700; letter-spacing:.08em;
  text-transform:uppercase; color:var(--muted); text-align:left;
  border-bottom:1px solid var(--border);
}
td {
  padding:13px 16px; font-size:13px;
  border-bottom:1px solid var(--border); color:var(--text);
}
tr:last-child td { border-bottom:none; }
tbody tr { transition:.15s; }
tbody tr:hover { background:rgba(255,255,255,.02); }
.td-id { color:var(--muted2); font-size:12px; font-weight:600; }
.avatar {
  display:inline-flex; align-items:center; justify-content:center;
  width:28px; height:28px; border-radius:7px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:11px;
  color:#fff; margin-right:8px; vertical-align:middle; flex-shrink:0;
}
.td-name { font-weight:600; }
.td-email, .td-muted { color:var(--muted); font-size:12px; }

/* role badge */
.role-badge {
  display:inline-block; padding:3px 9px; border-radius:20px;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
}
.role-admin { background:rgba(139,92,246,.15); border:1px solid rgba(139,92,246,.3); color:#c4b5fd; }
.role-user  { background:rgba(255,255,255,.05); border:1px solid var(--border); color:var(--muted); }

/* ── BTN ── */
.btn {
  display:inline-flex; align-items:center; gap:5px;
  padding:5px 12px; border-radius:7px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:12px;
  text-decoration:none; transition:.2s; border:1px solid transparent; cursor:pointer;
}
.btn-blue  { background:rgba(99,102,241,.15); border-color:rgba(99,102,241,.35); color:#818cf8; }
.btn-blue:hover  { background:rgba(99,102,241,.25); }
.btn-red   { background:rgba(239,68,68,.1);  border-color:rgba(239,68,68,.3);  color:#f87171; }
.btn-red:hover   { background:rgba(239,68,68,.2); }
.btn-green { background:rgba(34,197,94,.1);  border-color:rgba(34,197,94,.3);  color:#4ade80; }
.btn-green:hover { background:rgba(34,197,94,.2); }

/* ── TALENT / JOB GRID ── */
.items-grid {
  display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:12px;
}
.item-card {
  background:var(--s2); border:1px solid var(--border); border-radius:13px;
  padding:18px; transition:.2s; animation:up .4s ease both;
}
.item-card:hover { border-color:rgba(139,92,246,.3); }
.ic-top { display:flex; align-items:center; gap:11px; margin-bottom:14px; }
.ic-avatar {
  width:40px; height:40px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-family:'Cabinet Grotesk',sans-serif; font-weight:900; font-size:14px; color:#fff;
}
.ic-avatar.talent { background:linear-gradient(135deg,#6366f1,#8b5cf6); }
.ic-avatar.job    { background:linear-gradient(135deg,#f59e0b,#ef4444); }
.ic-name { font-family:'Cabinet Grotesk',sans-serif; font-weight:800; font-size:14px; letter-spacing:-.3px; }
.ic-sub  { font-size:11px; color:var(--muted); margin-top:2px; }
.ic-divider { height:1px; background:var(--border); margin:12px 0; }
.ic-footer { display:flex; align-items:center; justify-content:space-between; }
.ic-actions { display:flex; gap:6px; }

/* status */
.status {
  display:inline-flex; align-items:center; gap:5px;
  padding:3px 9px; border-radius:20px;
  font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
}
.sdot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.s-pending  { background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.3); color:#fbbf24; }
.s-approved { background:rgba(34,197,94,.1);  border:1px solid rgba(34,197,94,.25); color:#4ade80; }
.s-rejected { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25); color:#f87171; }

/* welcome card */
.welcome-card {
  background:var(--s2); border:1px solid var(--border);
  border-radius:14px; padding:24px; margin-top:20px;
  animation:up .4s ease both;
}
.welcome-card p { color:var(--muted); font-size:14px; }

/* empty */
.empty { text-align:center; padding:48px; color:var(--muted2); font-size:13px; }

@keyframes up {
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1;transform:translateY(0)}
}
</style>
</head>

<body>

<!-- ════ SIDEBAR ════ -->
<div class="sidebar">
  <div class="sidebar-logo">
    Hire<em>Tounsi</em>
    <span class="admin-badge">Admin</span>
  </div>

  <span class="sidebar-sep">Navigation</span>

  <a href="?page=dashboard" class="<?= $page==='dashboard'?'active':'' ?>">
    <span class="s-icon">🏠</span> Dashboard
  </a>
  <a href="?page=users" class="<?= $page==='users'?'active':'' ?>">
    <span class="s-icon">👤</span> Utilisateurs
  </a>
  <a href="?page=talents" class="<?= $page==='talents'?'active':'' ?>">
    <span class="s-icon">🎯</span> Talents
  </a>
  <a href="?page=jobs" class="<?= $page==='jobs'?'active':'' ?>">
    <span class="s-icon">💼</span> Offres d'emploi
  </a>

  <div class="sidebar-bottom">
    <a href="logout.php">
      <span class="s-icon">🚪</span> Déconnexion
    </a>
  </div>
</div>

<!-- ════ MAIN ════ -->
<div class="main">

<!-- ─── DASHBOARD ─── -->
<?php if($page == "dashboard"): ?>

  <div class="ph">
    <h1>Tableau de <span>bord</span></h1>
  </div>

  <div class="stats">
    <div class="stat">
      <div class="stat-label">Utilisateurs</div>
      <div class="stat-val"><?= $totalUsers ?></div>
    </div>
    <div class="stat">
      <div class="stat-label">Talents</div>
      <div class="stat-val"><?= $totalTalents ?></div>
    </div>
    <div class="stat">
      <div class="stat-label">Offres d'emploi</div>
      <div class="stat-val"><?= $totalJobs ?></div>
    </div>
  </div>

  <div class="welcome-card">
    <p>Bienvenue dans le panneau d'administration <strong style="color:var(--text)">HireTounsi</strong>. Gérez les utilisateurs, talents et offres depuis la barre latérale.</p>
  </div>


<!-- ─── USERS ─── -->
<?php elseif($page == "users"): ?>

  <div class="ph">
    <h1>Utilis<span>ateurs</span></h1>
  </div>

  <form method="GET" class="search-bar">
    <input type="hidden" name="page" value="users">
    <div class="search-wrap">
      <span class="si">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </span>
      <input class="search-input" type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher par nom ou email…">
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
          <th>Rôle</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($users as $u): ?>
        <tr>
          <td class="td-id"><?= $u["id"] ?></td>
          <td>
            <span class="avatar"><?= strtoupper(substr($u["name"],0,2)) ?></span>
            <span class="td-name"><?= htmlspecialchars($u["name"]) ?></span>
          </td>
          <td class="td-email"><?= htmlspecialchars($u["email"]) ?></td>
          <td>
            <span class="role-badge <?= $u['role']==='admin'?'role-admin':'role-user' ?>">
              <?= htmlspecialchars($u["role"]) ?>
            </span>
          </td>
          <td>
            <a class="btn btn-blue" href="edit_user.php?id=<?= $u['id'] ?>">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Edit
            </a>
            <?php if($u["role"] != "admin"): ?>
            <a class="btn btn-red" href="delete_user.php?id=<?= $u['id'] ?>">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              Delete
            </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($users)): ?>
        <tr><td colspan="5" class="empty">Aucun utilisateur trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>


<!-- ─── TALENTS ─── -->
<?php elseif($page == "talents"): ?>

  <div class="ph">
    <h1>Gestion des <span>Talents</span></h1>
  </div>

  <?php if(empty($talents)): ?>
    <div class="empty">Aucun talent enregistré.</div>
  <?php else: ?>
  <div class="items-grid">
    <?php foreach($talents as $t): ?>
    <div class="item-card">
      <div class="ic-top">
        <div class="ic-avatar talent"><?= strtoupper(substr($t["name"],0,2)) ?></div>
        <div>
          <div class="ic-name"><?= htmlspecialchars($t["name"]) ?></div>
          <div class="ic-sub"><?= htmlspecialchars($t["skill"]) ?></div>
        </div>
      </div>
      <div class="ic-divider"></div>
      <div class="ic-footer">
        <span class="status s-<?= $t['status'] ?>">
          <span class="sdot"></span><?= ucfirst($t["status"]) ?>
        </span>
        <?php if($t["status"] == "pending"): ?>
        <div class="ic-actions">
          <a class="btn btn-green" href="approve.php?id=<?= $t['id'] ?>">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
            Approuver
          </a>
          <a class="btn btn-red" href="reject.php?id=<?= $t['id'] ?>">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
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

  <div class="ph">
    <h1>Offres d'<span>emploi</span></h1>
  </div>

  <?php if(empty($jobs)): ?>
    <div class="empty">Aucune offre enregistrée.</div>
  <?php else: ?>
  <div class="items-grid">
    <?php foreach($jobs as $j): ?>
    <div class="item-card">
      <div class="ic-top">
        <div class="ic-avatar job">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
          <div class="ic-name"><?= htmlspecialchars($j["title"]) ?></div>
          <div class="ic-sub"><?= htmlspecialchars($j["company"]) ?></div>
        </div>
      </div>
      <div class="ic-divider"></div>
      <div class="ic-footer">
        <span class="status s-<?= $j['status'] ?>">
          <span class="sdot"></span><?= ucfirst($j["status"]) ?>
        </span>
        <?php if($j["status"] == "pending"): ?>
        <div class="ic-actions">
          <a class="btn btn-green" href="approve_job.php?id=<?= $j['id'] ?>">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
            Approuver
          </a>
          <a class="btn btn-red" href="reject_job.php?id=<?= $j['id'] ?>">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
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