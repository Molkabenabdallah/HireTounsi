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

    <div style="
      font-size:10px;
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      color:var(--muted2);
      margin-bottom:8px;
      padding:0 2px;
    ">
      Mode actuel
    </div>

    <div class="role-switcher">

      <a href="switch_role.php?role=candidat"
         class="role-btn active"
         id="btn-candidat">

        <svg width="13" height="13"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">

          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>

        </svg>

        Candidat
      </a>

      <a href="switch_role.php?role=recruteur"
         class="role-btn"
         id="btn-recruteur">

        <svg width="13"
             height="13"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">

          <rect width="20" height="14" x="2" y="7" rx="2"/>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>

        </svg>

        Recruteur
      </a>

    </div>
  </div>

  <!-- ═════════ NAVIGATION ═════════ -->
  <nav class="sidebar-nav">

    <span class="nav-sep">Navigation</span>

    <a href="dashboard.php" class="nav-item">
      <span class="nav-icon">🏠</span>
      Tableau de bord
    </a>

    <a href="jobs.php" class="nav-item active">
      <span class="nav-icon">🔍</span>
      Offres d'emploi
    </a>

    <a href="mes_candidatures.php" class="nav-item">
      <span class="nav-icon">📋</span>
      Mes candidatures
    </a>

    <a href="companies.php" class="nav-item">
      <span class="nav-icon">🏢</span>
      Entreprises
    </a>

    <a href="talents.php" class="nav-item">
      <span class="nav-icon">👥</span>
      Talents
    </a>

    <span class="nav-sep">Compte</span>

    <a href="messages.php" class="nav-item">
      <span class="nav-icon">💬</span>
      Messages
    </a>

    <a href="settings.php" class="nav-item">
      <span class="nav-icon">⚙️</span>
      Paramètres
    </a>

  </nav>

  <!-- ═════════ USER ═════════ -->
  <div class="sidebar-bottom">

    <div class="user-pill">

      <div class="avatar-sm">
        <?= $initials ?>
      </div>

      <div class="user-pill-info">

        <div class="upn">
          <?= $userName ?>
        </div>

        <div class="upr">
          Candidat · Membre depuis <?= $joinDate ?>
        </div>

      </div>

      <a href="logout.php"
         class="logout-btn"
         title="Déconnexion">

        <svg width="15"
             height="15"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">

          <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>

        </svg>

      </a>

    </div>

  </div>

</aside>