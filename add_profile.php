<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter Profil — HireTounsi</title>

<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800;900&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet"/>

<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --bg: #07080d;
  --surface: #0e1018;
  --surface-2: #161824;
  --border: rgba(255,255,255,0.07);
  --border-focus: rgba(139,92,246,0.6);
  --accent: #8b5cf6;
  --accent-2: #06b6d4;
  --text: #f0eeff;
  --muted: #7a7890;
  --muted-2: #4a4860;
  --radius: 14px;
  --radius-sm: 9px;
}

body {
  font-family: 'Instrument Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  position: relative;
  overflow-x: hidden;
}

/* Ambient glows */
body::before {
  content: '';
  position: fixed;
  top: -180px; left: -180px;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%);
  pointer-events: none;
}
body::after {
  content: '';
  position: fixed;
  bottom: -180px; right: -100px;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(6,182,212,0.07) 0%, transparent 70%);
  pointer-events: none;
}

/* === CARD WRAPPER === */
.card {
  position: relative;
  z-index: 1;
  background: var(--surface-2);
  border: 1px solid var(--border);
  border-radius: 20px;
  width: 100%;
  max-width: 480px;
  padding: 40px;
  animation: slideUp 0.5s cubic-bezier(0.4,0,0.2,1) both;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Inner glow line at top */
.card::before {
  content: '';
  position: absolute;
  top: 0; left: 30px; right: 30px;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(139,92,246,0.6), transparent);
  border-radius: 1px;
}

/* === HEADER === */
.card-header {
  margin-bottom: 32px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--muted);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 24px;
  transition: color 0.2s;
}

.back-link:hover { color: var(--text); }

.card-icon {
  width: 48px; height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(6,182,212,0.15));
  border: 1px solid rgba(139,92,246,0.3);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 16px;
}

.card-header h2 {
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 800;
  font-size: 26px;
  letter-spacing: -0.8px;
  line-height: 1.15;
  margin-bottom: 6px;
}

.card-header h2 span {
  background: linear-gradient(90deg, var(--accent), var(--accent-2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.card-header p {
  color: var(--muted);
  font-size: 14px;
  line-height: 1.5;
}

/* === FORM === */
.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 7px;
}

input, textarea, select {
  width: 100%;
  padding: 13px 15px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text);
  font-family: 'Instrument Sans', sans-serif;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
  resize: none;
}

input::placeholder,
textarea::placeholder { color: var(--muted-2); }

input:focus, textarea:focus, select:focus {
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
  background: rgba(14,16,24,0.8);
}

textarea { height: 110px; line-height: 1.6; }

/* === SKILLS INPUT === */
.tags-input-wrapper {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 8px 10px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  min-height: 48px;
  align-items: center;
  cursor: text;
  transition: all 0.2s;
}

.tags-input-wrapper:focus-within {
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
}

.skill-tag {
  display: flex;
  align-items: center;
  gap: 5px;
  background: rgba(139,92,246,0.15);
  border: 1px solid rgba(139,92,246,0.3);
  color: #a78bfa;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 500;
  animation: tagPop 0.15s ease;
}

@keyframes tagPop {
  from { transform: scale(0.8); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}

.skill-tag button {
  background: none;
  border: none;
  color: #a78bfa;
  cursor: pointer;
  padding: 0;
  width: auto;
  font-size: 14px;
  line-height: 1;
  opacity: 0.6;
  transition: opacity 0.15s;
}

.skill-tag button:hover { opacity: 1; }

.tags-input {
  flex: 1;
  min-width: 100px;
  background: none;
  border: none;
  padding: 4px 5px;
  color: var(--text);
  font-family: 'Instrument Sans', sans-serif;
  font-size: 14px;
  outline: none;
  box-shadow: none;
}

.tags-input:focus { box-shadow: none; border: none; }

.hint {
  font-size: 11px;
  color: var(--muted-2);
  margin-top: 5px;
}

/* === DIVIDER === */
.divider {
  height: 1px;
  background: var(--border);
  margin: 24px 0;
}

/* === SUBMIT === */
.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, var(--accent), #7c3aed);
  border: none;
  border-radius: var(--radius-sm);
  color: white;
  font-family: 'Cabinet Grotesk', sans-serif;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: -0.2px;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 20px rgba(139,92,246,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(139,92,246,0.45);
}

.btn-submit:active { transform: translateY(0); }

/* === FOOTER NOTE === */
.form-note {
  text-align: center;
  margin-top: 18px;
  font-size: 12px;
  color: var(--muted-2);
}

.form-note span {
  color: var(--accent);
  font-weight: 600;
}
</style>
</head>

<body>

<div class="card">

  <div class="card-header">
    <a href="talents.php" class="back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Retour aux talents
    </a>

    <div class="card-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="url(#grad)" stroke-width="2">
        <defs>
          <linearGradient id="grad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#8b5cf6"/>
            <stop offset="100%" stop-color="#06b6d4"/>
          </linearGradient>
        </defs>
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
    </div>

    <h2>Créez votre <span>profil</span></h2>
    <p>Rejoignez le répertoire des talents tunisiens et soyez découvert par des entreprises.</p>
  </div>

  <form method="POST" action="save_profile.php">

    <div class="form-group">
      <label>Nom complet</label>
      <input name="name" placeholder="Ex : Amine Ben Salah" required>
    </div>

    <div class="form-group">
      <label>Compétences principales</label>
      <div class="tags-input-wrapper" id="tagsWrapper">
        <!-- Tags dynamiques ici -->
        <input class="tags-input" id="tagsInput" placeholder="Tapez et appuyez sur Entrée…">
      </div>
      <input type="hidden" name="skill" id="skillHidden">
      <p class="hint">Ex : React, UI/UX, Node.js — séparés par Entrée ou virgule</p>
    </div>

    <div class="form-group">
      <label>Ville</label>
      <input name="city" placeholder="Ex : Tunis, Sfax, Sousse…">
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description" placeholder="Présentez-vous brièvement : expérience, expertise, projets…"></textarea>
    </div>

    <div class="divider"></div>

    <button type="submit" class="btn-submit">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
      Publier mon profil
    </button>

  </form>

  <p class="form-note">Votre profil sera <span>vérifié</span> avant publication.</p>

</div>

<script>
const wrapper = document.getElementById('tagsWrapper');
const input   = document.getElementById('tagsInput');
const hidden  = document.getElementById('skillHidden');
const tags    = [];

function syncHidden() {
  hidden.value = tags.join(',');
}

function addTag(val) {
  val = val.trim();
  if (!val || tags.includes(val)) return;
  tags.push(val);

  const chip = document.createElement('span');
  chip.className = 'skill-tag';
  chip.innerHTML = `${val}<button type="button" aria-label="Supprimer">×</button>`;
  chip.querySelector('button').addEventListener('click', () => {
    tags.splice(tags.indexOf(val), 1);
    chip.remove();
    syncHidden();
  });

  wrapper.insertBefore(chip, input);
  syncHidden();
}

input.addEventListener('keydown', e => {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault();
    addTag(input.value.replace(',', ''));
    input.value = '';
  }
  if (e.key === 'Backspace' && !input.value && tags.length) {
    const last = wrapper.querySelectorAll('.skill-tag');
    last[last.length - 1].remove();
    tags.pop();
    syncHidden();
  }
});

input.addEventListener('blur', () => {
  if (input.value) { addTag(input.value); input.value = ''; }
});

wrapper.addEventListener('click', () => input.focus());
</script>

</body>
</html>