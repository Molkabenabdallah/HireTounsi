<?php
include "config.php";

// récupérer toutes les offres
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY id DESC");
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin — Offres</title>
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@700;800;900&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:      #07080d;
  --surface: #0e1018;
  --surface2:#161824;
  --border:  rgba(255,255,255,0.07);
  --accent:  #8b5cf6;
  --text:    #f0eeff;
  --muted:   #7a7890;
  --muted2:  #4a4860;
}

body {
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg); color:var(--text);
  min-height:100vh; padding:40px 32px;
}

body::before {
  content:''; position:fixed; top:-200px; left:-150px;
  width:550px; height:550px;
  background:radial-gradient(circle,rgba(139,92,246,.08) 0%,transparent 70%);
  pointer-events:none; z-index:0;
}

.container { position:relative; z-index:1; max-width:900px; margin:0 auto; }

/* ── HEADER ── */
.page-header {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:32px;
}

.page-header h1 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:900; font-size:28px; letter-spacing:-.7px;
}

.page-header h1 span {
  background:linear-gradient(90deg,#8b5cf6,#06b6d4);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  background-clip:text;
}

.back-link {
  display:inline-flex; align-items:center; gap:6px;
  color:var(--muted); text-decoration:none; font-size:13px; font-weight:500;
  padding:8px 14px; border-radius:9px;
  border:1px solid var(--border); background:var(--surface2);
  transition:.2s;
}
.back-link:hover { color:var(--text); border-color:rgba(139,92,246,.4); }

/* ── STATS STRIP ── */
.stats-strip { display:flex; gap:10px; margin-bottom:28px; flex-wrap:wrap; }

.stat-chip {
  background:var(--surface2); border:1px solid var(--border);
  border-radius:20px; padding:6px 14px; font-size:13px; color:var(--muted);
}
.stat-chip b { color:var(--text); font-weight:700; }

/* ── GRID ── */
.grid {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(270px,1fr));
  gap:14px;
}

/* ── CARD ── */
.card {
  background:var(--surface2); border:1px solid var(--border);
  border-radius:15px; padding:20px;
  transition:.25s cubic-bezier(.4,0,.2,1);
  position:relative; overflow:hidden;
  animation:up .4s ease both;
}

.card:hover { transform:translateY(-3px); border-color:rgba(139,92,246,.35); box-shadow:0 10px 32px rgba(0,0,0,.35); }

.card:nth-child(1){animation-delay:.04s} .card:nth-child(2){animation-delay:.08s}
.card:nth-child(3){animation-delay:.12s} .card:nth-child(4){animation-delay:.16s}
.card:nth-child(5){animation-delay:.20s} .card:nth-child(6){animation-delay:.24s}

@keyframes up {
  from{opacity:0;transform:translateY(12px)}
  to{opacity:1;transform:translateY(0)}
}

.card-icon {
  width:42px; height:42px; border-radius:11px;
  background:linear-gradient(135deg,rgba(245,158,11,.2),rgba(239,68,68,.1));
  border:1px solid rgba(245,158,11,.2);
  display:flex; align-items:center; justify-content:center;
  margin-bottom:14px; color:#fbbf24;
}

.card h3 {
  font-family:'Cabinet Grotesk',sans-serif;
  font-weight:800; font-size:15px; letter-spacing:-.3px;
  margin-bottom:4px;
}

.card-company {
  display:flex; align-items:center; gap:5px;
  font-size:13px; color:var(--muted); margin-bottom:14px;
}

.divider { height:1px; background:var(--border); margin:14px 0; }

.card-footer { display:flex; align-items:center; justify-content:space-between; }

/* Status */
.status {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:20px;
  font-size:11px; font-weight:700; letter-spacing:.04em; text-transform:uppercase;
}
.status-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }
.status.pending  { background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.3); color:#fbbf24; }
.status.approved { background:rgba(34,197,94,.1);  border:1px solid rgba(34,197,94,.25); color:#4ade80; }
.status.rejected { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.25); color:#f87171; }

/* Actions */
.actions { display:flex; gap:6px; }

.btn {
  display:inline-flex; align-items:center; gap:5px;
  padding:6px 12px; border-radius:7px;
  font-family:'Instrument Sans',sans-serif; font-weight:600; font-size:12px;
  text-decoration:none; transition:.2s; border:1px solid transparent;
}

.approve {
  background:rgba(34,197,94,.12); border-color:rgba(34,197,94,.3); color:#4ade80;
}
.approve:hover { background:rgba(34,197,94,.22); }

.reject {
  background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.3); color:#f87171;
}
.reject:hover { background:rgba(239,68,68,.2); }

/* Empty */
.empty {
  text-align:center; padding:60px; color:var(--muted2);
  grid-column:1/-1;
}
</style>
</head>

<body>
<div class="container">

  <div class="page-header">
    <h1>Offres d'<span>emploi</span></h1>
    <a href="admin.php?page=jobs" class="back-link">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Retour admin
    </a>
  </div>

  <?php
    $pending  = array_filter($jobs, fn($j) => $j['status'] === 'pending');
    $approved = array_filter($jobs, fn($j) => $j['status'] === 'approved');
    $rejected = array_filter($jobs, fn($j) => $j['status'] === 'rejected');
  ?>

  <div class="stats-strip">
    <div class="stat-chip">Total <b><?= count($jobs) ?></b></div>
    <div class="stat-chip">🟡 En attente <b><?= count($pending) ?></b></div>
    <div class="stat-chip">🟢 Approuvées <b><?= count($approved) ?></b></div>
    <div class="stat-chip">🔴 Refusées <b><?= count($rejected) ?></b></div>
  </div>

  <div class="grid">

  <?php if(empty($jobs)): ?>
    <div class="empty">Aucune offre enregistrée.</div>
  <?php endif; ?>

  <?php foreach($jobs as $job): ?>
    <div class="card">

      <div class="card-icon">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <rect width="20" height="14" x="2" y="7" rx="2"/>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
      </div>

      <h3><?= htmlspecialchars($job['title']) ?></h3>

      <div class="card-company">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/></svg>
        <?= htmlspecialchars($job['company']) ?>
      </div>

      <div class="divider"></div>

      <div class="card-footer">
        <span class="status <?= $job['status'] ?>">
          <span class="status-dot"></span>
          <?= ucfirst($job['status']) ?>
        </span>

        <?php if($job['status'] == 'pending'): ?>
        <div class="actions">
          <a href="approve_job.php?id=<?= $job['id'] ?>" class="btn approve">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
            Valider
          </a>
          <a href="reject_job.php?id=<?= $job['id'] ?>" class="btn reject">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
            Refuser
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div>
  <?php endforeach; ?>

  </div>
</div>
</body>
</html>