<?php
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user["name"];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HireTounsi – Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:       #0c0c18;
      --bg2:      #11112a;
      --card:     #15152b;
      --border:   rgba(255,255,255,0.08);
      --purple:   #7c3aed;
      --purple2:  #9d5bfc;
      --accent:   #a78bfa;
      --text:     #f0eeff;
      --muted:    #8a89aa;
      --error:    #f87171;
      --errorBg:  rgba(239,68,68,0.10);
      --errorBdr: rgba(239,68,68,0.25);
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* ── BLOBS ── */
    .blob { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
    .blob-a { width: 560px; height: 560px; background: #3b0f8c; opacity: .38; top: -180px; left: -120px; }
    .blob-b { width: 400px; height: 400px; background: #1e0a60; opacity: .42; bottom: -100px; right: -80px; }
    .blob-c { width: 200px; height: 200px; background: #7c3aed; opacity: .18; top: 50%; left: 60%; }

    /* ── NAV ── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 52px;
      background: rgba(12,12,24,0.75);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }
    .logo {
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.2rem;
      color: #fff; text-decoration: none; display: flex; align-items: center; gap: 9px;
    }
    .logo-box {
      width: 34px; height: 34px; background: var(--purple); border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      font-size: .85rem; font-weight: 900; color: #fff;
      box-shadow: 0 0 16px rgba(124,58,237,.5);
    }
    .logo em { color: var(--purple2); font-style: normal; }
    .nav-links { display: flex; gap: 34px; list-style: none; }
    .nav-links a { color: var(--muted); text-decoration: none; font-size: .875rem; transition: color .2s; }
    .nav-links a:hover { color: #fff; }

    /* ── LAYOUT ── */
    .page {
      position: relative; z-index: 1;
      display: grid; grid-template-columns: 1fr 1fr;
      min-height: 100vh; padding-top: 66px;
    }

    /* ── ILLUSTRATION SIDE ── */
    .illus {
      display: flex; align-items: center; justify-content: center;
      padding: 60px 40px;
    }
    .illus-inner {
      display: flex; flex-direction: column; align-items: center; gap: 40px;
    }
    .illus-svg {
      width: 320px; max-width: 90%;
      animation: levitate 6s ease-in-out infinite;
      filter: drop-shadow(0 24px 48px rgba(124,58,237,.25));
    }
    @keyframes levitate {
      0%, 100% { transform: translateY(0px); }
      50%       { transform: translateY(-16px); }
    }
    .illus-tagline {
      text-align: center;
      font-family: 'Syne', sans-serif; font-weight: 700;
      font-size: 1.5rem; line-height: 1.2;
      color: #fff; max-width: 280px;
    }
    .illus-tagline span { color: var(--accent); }
    .illus-stats {
      display: flex; gap: 32px;
    }
    .stat { text-align: center; }
    .stat strong { display: block; font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800; color: #fff; }
    .stat small { font-size: .75rem; color: var(--muted); }

    /* ── FORM SIDE ── */
    .form-side {
      display: flex; align-items: center; justify-content: center;
      padding: 60px 64px 60px 32px;
    }
    .form-box { width: 100%; max-width: 420px; }

    .form-box h2 {
      font-family: 'Syne', sans-serif; font-weight: 800;
      font-size: 2rem; letter-spacing: -.5px; margin-bottom: 6px;
    }
    .form-box .hint { color: var(--muted); font-size: .875rem; margin-bottom: 32px; }

    /* Google btn */
    .btn-google {
      width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;
      background: rgba(255,255,255,0.05); border: 1px solid var(--border);
      border-radius: 12px; padding: 13px 20px; color: var(--text);
      font-family: 'DM Sans', sans-serif; font-size: .9rem;
      cursor: pointer; text-decoration: none;
      transition: background .2s, border-color .2s;
    }
    .btn-google:hover { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,.16); }
    .btn-google svg { width: 18px; height: 18px; flex-shrink: 0; }

    /* Divider */
    .divider {
      display: flex; align-items: center; gap: 14px;
      margin: 22px 0; color: var(--muted); font-size: .78rem;
    }
    .divider::before, .divider::after {
      content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* Fields */
    .field { margin-bottom: 16px; }
    .field label { display: block; font-size: .78rem; color: var(--muted); margin-bottom: 6px; font-weight: 500; letter-spacing: .3px; }
    .field input {
      width: 100%; padding: 13px 16px;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--border);
      border-radius: 11px; color: var(--text);
      font-family: 'DM Sans', sans-serif; font-size: .9rem;
      outline: none; transition: border-color .2s, background .2s;
    }
    .field input:focus { border-color: var(--purple); background: rgba(124,58,237,0.06); }
    .field input::placeholder { color: var(--muted); }

    /* Remember row */
    .row-remember {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 26px;
    }
    .toggle { position: relative; width: 40px; height: 22px; flex-shrink: 0; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute; inset: 0; border-radius: 50px; cursor: pointer;
      background: rgba(255,255,255,0.1); transition: .28s;
    }
    .slider::before {
      content: ''; position: absolute;
      width: 16px; height: 16px; border-radius: 50%;
      background: #fff; top: 3px; left: 3px; transition: .28s;
    }
    .toggle input:checked + .slider { background: var(--purple); }
    .toggle input:checked + .slider::before { transform: translateX(18px); }
    .row-remember span { font-size: .83rem; color: var(--muted); }

    /* Submit */
    .btn-submit {
      width: 100%; padding: 14px;
      background: linear-gradient(130deg, var(--purple) 0%, var(--purple2) 100%);
      border: none; border-radius: 11px;
      color: #fff; font-family: 'DM Sans', sans-serif; font-size: .95rem; font-weight: 500;
      cursor: pointer; transition: opacity .2s, box-shadow .2s;
      box-shadow: 0 4px 28px rgba(124,58,237,.45);
    }
    .btn-submit:hover { opacity: .88; box-shadow: 0 4px 36px rgba(124,58,237,.65); }

    /* Links */
    .switch-link {
      text-align: center; margin-top: 22px;
      font-size: .83rem; color: var(--muted);
    }
    .switch-link a { color: var(--accent); font-weight: 600; text-decoration: none; }
    .switch-link a:hover { text-decoration: underline; }

    /* Error */
    .alert-error {
      background: var(--errorBg); border: 1px solid var(--errorBdr);
      border-radius: 10px; padding: 12px 16px;
      color: var(--error); font-size: .85rem; margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 800px) {
      .page { grid-template-columns: 1fr; }
      .illus { display: none; }
      .form-side { padding: 40px 24px; }
      nav { padding: 14px 20px; }
      .nav-links { display: none; }
    }
  </style>
</head>
<body>

  <div class="blob blob-a"></div>
  <div class="blob blob-b"></div>
  <div class="blob blob-c"></div>

  <!-- NAV -->
  <nav>
    <a href="index.html" class="logo">
  <img src="imgs/logo.png" alt="HireTounsi" width="20%">
</a>
    <ul class="nav-links">
      <li><a href="#">Offres d'emploi</a></li>
      <li><a href="#">Entreprises</a></li>
      <li><a href="#">Talents tunisiens</a></li>
      <li><a href="#">À propos</a></li>
      <li><a href="#">Blogs</a></li>
    </ul>
  </nav>

  <div class="page">

    <!-- ILLUSTRATION -->
    <div class="illus">
      <div class="illus-inner">
        <svg class="illus-svg" viewBox="0 0 380 400" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Globe -->
          <circle cx="120" cy="95" r="68" stroke="white" stroke-width="2.2" stroke-opacity=".9"/>
          <ellipse cx="120" cy="95" rx="32" ry="68" stroke="white" stroke-width="1.8" stroke-opacity=".7"/>
          <line x1="52" y1="95" x2="188" y2="95" stroke="white" stroke-width="1.8" stroke-opacity=".7"/>
          <path d="M 62 65 Q 120 80 178 65" stroke="white" stroke-width="1.6" fill="none" stroke-opacity=".6"/>
          <path d="M 62 125 Q 120 110 178 125" stroke="white" stroke-width="1.6" fill="none" stroke-opacity=".6"/>
          <!-- Arm holding globe -->
          <line x1="120" y1="163" x2="120" y2="200" stroke="white" stroke-width="2.8" stroke-linecap="round" stroke-opacity=".85"/>
          <!-- Body -->
          <ellipse cx="190" cy="285" rx="56" ry="75" stroke="white" stroke-width="2.2" stroke-opacity=".85"/>
          <!-- Head -->
          <circle cx="190" cy="192" r="32" stroke="white" stroke-width="2.2" stroke-opacity=".9"/>
          <!-- Eyes -->
          <circle cx="181" cy="189" r="2.8" fill="white" fill-opacity=".9"/>
          <circle cx="199" cy="189" r="2.8" fill="white" fill-opacity=".9"/>
          <!-- Smile -->
          <path d="M 182 201 Q 190 208 198 201" stroke="white" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-opacity=".85"/>
          <!-- Jacket -->
          <path d="M 166 245 L 185 283 L 190 257 L 195 283 L 214 245" stroke="white" stroke-width="1.8" fill="none" stroke-opacity=".8"/>
          <!-- Tie -->
          <path d="M 187 257 L 190 288 L 193 257 L 190 250 Z" stroke="white" stroke-width="1.4" fill="none" stroke-opacity=".7"/>
          <!-- Arms -->
          <path d="M 134 265 Q 105 255 95 283" stroke="white" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-opacity=".8"/>
          <path d="M 246 265 Q 274 280 278 305" stroke="white" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-opacity=".8"/>
          <!-- Left hand knuckle -->
          <circle cx="94" cy="291" r="7" stroke="white" stroke-width="1.8" fill="none" stroke-opacity=".7"/>
          <!-- Desk -->
          <rect x="105" y="348" width="170" height="11" rx="5.5" stroke="white" stroke-width="1.8" fill="none" stroke-opacity=".7"/>
          <line x1="115" y1="359" x2="115" y2="398" stroke="white" stroke-width="1.8" stroke-opacity=".6"/>
          <line x1="265" y1="359" x2="265" y2="398" stroke="white" stroke-width="1.8" stroke-opacity=".6"/>
          <!-- Papers -->
          <rect x="128" y="318" width="52" height="33" rx="4" stroke="white" stroke-width="1.8" fill="none" stroke-opacity=".65" transform="rotate(-7 154 334)"/>
          <rect x="138" y="313" width="52" height="33" rx="4" stroke="white" stroke-width="1.8" fill="none" stroke-opacity=".55" transform="rotate(4 164 329)"/>
          <line x1="144" y1="327" x2="168" y2="324" stroke="white" stroke-width="1.4" stroke-opacity=".5"/>
          <line x1="144" y1="334" x2="166" y2="331" stroke="white" stroke-width="1.4" stroke-opacity=".5"/>
          <line x1="144" y1="341" x2="162" y2="338" stroke="white" stroke-width="1.4" stroke-opacity=".5"/>
        </svg>

        <div class="illus-tagline">
          Connectez-vous et<br><span>trouvez le bon emploi</span>
        </div>

        <div class="illus-stats">
          <div class="stat">
            <strong>500K+</strong>
            <small>Utilisateurs</small>
          </div>
          <div class="stat">
            <strong>650+</strong>
            <small>Entreprises</small>
          </div>
          <div class="stat">
            <strong>350+</strong>
            <small>Freelances</small>
          </div>
        </div>
      </div>
    </div>

    <!-- FORM -->
    <div class="form-side">
      <div class="form-box">
        <h2>Content de vous revoir</h2>
        <p class="hint">Connectez-vous à votre compte pour continuer</p>

        <?php if ($error): ?>
          <div class="alert-error">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <a href="#" class="btn-google">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Continuer avec Google
        </a>

        <div class="divider">Ou continuez avec</div>

        <form method="POST" action="login.php">
          <div class="field">
            <label>Email</label>
            <input type="email" name="email" placeholder="votre adresse email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
          </div>
          <div class="field">
            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="votre mot de passe" required/>
          </div>

          <div class="row-remember">
            <label class="toggle">
              <input type="checkbox" name="remember"/>
              <span class="slider"></span>
            </label>
            <span>Se souvenir de moi</span>
          </div>

          <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <p class="switch-link">Pas de compte ? <a href="register.php">Créer un compte</a></p>
      </div>
    </div>

  </div>
</body>
</html>
