<?php
session_start();
include "config.php";



// récupérer talents approuvés
$stmt = $pdo->query("SELECT * FROM talents WHERE status='approved' ORDER BY id DESC");
$talents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Talents Tunisiens</title>

<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800;900&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet"/>

<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --bg: #07080d;
  --surface: #0e1018;
  --surface-2: #161824;
  --border: rgba(255,255,255,0.07);
  --border-hover: rgba(139,92,246,0.5);
  --accent: #8b5cf6;
  --accent-2: #06b6d4;
  --accent-glow: rgba(139,92,246,0.25);
  --text: #f0eeff;
  --muted: #7a7890;
  --muted-2: #4a4860;
  --radius: 16px;
  --radius-sm: 8px;
}

body {
  font-family: 'Instrument Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  overflow-x: hidden;
}

/* === BACKGROUND ATMOSPHERE === */
body::before {
  content: '';
  position: fixed;
  top: -200px;
  left: -200px;
  width: 700px;
  height: 700px;
  background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

body::after {
  content: '';
  position: fixed;
  bottom: -200px;
  right: -100px;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(6,182,212,0.06) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

/* === NAV === */
nav {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;
  height: 64px;
  border-bottom: 1px solid var(--border);
  background: rgba(7,8,13,0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.nav-logo {
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 900;
  font-size: 20px;
  color: var(--text);
  text-decoration: none;
  letter-spacing: -0.5px;
}

.nav-logo span {
  color: var(--accent);
}

.nav-links {
  display: flex;
  gap: 4px;
  list-style: none;
}

.nav-links a {
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 6px 14px;
  border-radius: 20px;
  transition: all 0.2s;
}

.nav-links a:hover {
  color: var(--text);
  background: var(--surface-2);
}

.btn-connect {
  background: var(--accent);
  border: none;
  padding: 8px 20px;
  border-radius: 20px;
  color: #fff;
  cursor: pointer;
  font-family: 'Instrument Sans', sans-serif;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
  box-shadow: 0 0 20px rgba(139,92,246,0.3);
}

.btn-connect:hover {
  background: #7c3aed;
  box-shadow: 0 0 30px rgba(139,92,246,0.5);
}

/* === MAIN === */
main {
  position: relative;
  z-index: 1;
  max-width: 1280px;
  margin: 0 auto;
  padding: 48px 32px;
}

/* === PAGE HEADER === */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 40px;
}

.page-header-text h2 {
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 800;
  font-size: 32px;
  letter-spacing: -1px;
  line-height: 1.1;
  margin-bottom: 6px;
}

.page-header-text h2 span {
  background: linear-gradient(90deg, var(--accent), var(--accent-2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.page-header-text p {
  color: var(--muted);
  font-size: 15px;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--surface-2);
  border: 1px solid var(--border);
  padding: 10px 20px;
  border-radius: 12px;
  color: var(--text);
  text-decoration: none;
  font-family: 'Instrument Sans', sans-serif;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
  cursor: pointer;
  white-space: nowrap;
}

.btn-add:hover {
  border-color: var(--accent);
  background: rgba(139,92,246,0.1);
  color: var(--accent);
}

/* === SEARCH === */
.search-wrapper {
  position: relative;
  margin-bottom: 32px;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 14px 16px 14px 46px;
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text);
  font-family: 'Instrument Sans', sans-serif;
  font-size: 15px;
  outline: none;
  transition: all 0.2s;
}

.search-input::placeholder { color: var(--muted); }

.search-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(139,92,246,0.12);
  background: var(--surface);
}

/* === LAYOUT === */
.layout {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 28px;
  align-items: start;
}

/* === SIDEBAR === */
.sidebar {
  position: sticky;
  top: 84px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sidebar-label {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--muted-2);
  padding: 0 4px;
  margin-top: 8px;
}

.filter-select {
  width: 100%;
  padding: 10px 12px;
  background: var(--surface-2);
  border: 1px solid var(--border);
  color: var(--text);
  border-radius: var(--radius-sm);
  font-family: 'Instrument Sans', sans-serif;
  font-size: 14px;
  outline: none;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7890' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 32px;
  transition: all 0.2s;
}

.filter-select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
}

/* === STATS STRIP === */
.stats-strip {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
}

.stat-chip {
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 13px;
  color: var(--muted);
}

.stat-chip b { color: var(--text); font-weight: 600; }

/* === CARD GRID === */
.card-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

/* === CARD === */
.card {
  background: var(--surface-2);
  padding: 20px;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
}

.card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(139,92,246,0.03) 0%, transparent 60%);
  opacity: 0;
  transition: opacity 0.25s;
}

.card:hover {
  transform: translateY(-4px);
  border-color: var(--border-hover);
  box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(139,92,246,0.15);
}

.card:hover::before { opacity: 1; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Stagger each card */
.card:nth-child(1)  { animation-delay: 0.05s }
.card:nth-child(2)  { animation-delay: 0.10s }
.card:nth-child(3)  { animation-delay: 0.15s }
.card:nth-child(4)  { animation-delay: 0.20s }
.card:nth-child(5)  { animation-delay: 0.25s }
.card:nth-child(6)  { animation-delay: 0.30s }
.card:nth-child(7)  { animation-delay: 0.35s }
.card:nth-child(8)  { animation-delay: 0.40s }
.card:nth-child(9)  { animation-delay: 0.45s }

/* === AVATAR === */
.avatar {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 800;
  font-size: 15px;
  color: white;
  flex-shrink: 0;
  letter-spacing: -0.5px;
}

.card-top {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 14px;
}

.card-name {
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.card-role {
  font-size: 12px;
  color: var(--muted);
  margin-top: 2px;
}

/* === LOCATION === */
.card-location {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--muted);
  margin-bottom: 12px;
}

.card-location svg {
  flex-shrink: 0;
  color: var(--accent);
  opacity: 0.7;
}

/* === TAGS === */
.tags {
  display: flex;
  gap: 5px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.tag {
  background: rgba(139,92,246,0.1);
  border: 1px solid rgba(139,92,246,0.2);
  color: #a78bfa;
  padding: 3px 9px;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.01em;
}

/* === CARD FOOTER === */
.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 14px;
  border-top: 1px solid var(--border);
}

.verified-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 500;
  color: #34d399;
}

.verified-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #34d399;
  box-shadow: 0 0 6px #34d399;
}

.btn-profile {
  background: var(--accent);
  border: none;
  padding: 7px 14px;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  font-family: 'Instrument Sans', sans-serif;
  font-weight: 600;
  font-size: 12px;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-block;
}

.btn-profile:hover {
  background: #7c3aed;
  box-shadow: 0 4px 14px rgba(139,92,246,0.4);
  transform: translateY(-1px);
}
</style>
</head>

<body>

<nav>
  <a href="index.html" class="nav-logo">Hire<span>Tounsi</span></a>
  <ul class="nav-links">
    <li><a href="#">Offres</a></li>
    <li><a href="#">Entreprises</a></li>
    <li><a href="#">Talents</a></li>
  </ul>
  <button class="btn-connect">Connexion</button>
</nav>

<main>

  <div class="page-header">
    <div class="page-header-text">
      <h2>Répertoire des <span>Talents Tunisiens</span></h2>
      <p>Découvrez et connectez-vous avec des profils qualifiés</p>
    </div>
    <a href="add_profile.php" class="btn-add">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
      Ajouter profil
    </a>
  </div>

  <div class="search-wrapper">
    <span class="search-icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </span>
    <input class="search-input" placeholder="Rechercher un talent, une compétence, une ville…">
  </div>

  <div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
      <span class="sidebar-label">Filtres</span>
      <select class="filter-select">
        <option value="">Toutes catégories</option>
      </select>

      <span class="sidebar-label">Localisation</span>
      <select class="filter-select">
        <option value="">Toutes les villes</option>
      </select>
    </div>

    <!-- CONTENT -->
    <div>
      <div class="stats-strip">
        <div class="stat-chip"><b><?= count($talents) ?></b> talents</div>
        <div class="stat-chip">✦ Vérifiés</div>
      </div>

      <div class="card-grid">

        <?php foreach($talents as $t): ?>
        <div class="card">

          <div class="card-top">
            <div class="avatar">
              <?= strtoupper(substr($t['name'], 0, 2)) ?>
            </div>
            <div>
              <div class="card-name"><?= htmlspecialchars($t['name']) ?></div>
              <div class="card-role"><?= htmlspecialchars($t['skill']) ?></div>
            </div>
          </div>

          <div class="card-location">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= htmlspecialchars($t['city'] ?? 'Tunisie') ?>
          </div>

          <div class="tags">
            <?php
              $skills = explode(",", $t['skill']);
              foreach(array_slice($skills, 0, 3) as $s):
            ?>
              <span class="tag"><?= htmlspecialchars(trim($s)) ?></span>
            <?php endforeach; ?>
          </div>

          <div class="card-footer">
            <div class="verified-badge">
              <span class="verified-dot"></span>
              Vérifié
            </div>
            <a href="profile.php?id=<?= $t['id'] ?>" class="btn-profile">Voir profil →</a>
          </div>

        </div>
        <?php endforeach; ?>

      </div>
    </div>

  </div>

</main>

</body>
</html>
