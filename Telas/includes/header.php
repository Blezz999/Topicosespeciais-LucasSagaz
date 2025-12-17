<?php ?>
<!-- ====== ESTILO DO HEADER ====== -->
<link rel="stylesheet" href="css/header.css">

<!-- ====== HEADER PRINCIPAL ====== -->
<header class="site-header">

  <!-- =========================
       LOGO DO IFSC
       Clica para voltar à página inicial
       ========================= -->
  <div class="logo">
    <a href="index.php">
      <img src="img/ifsc.png" alt="Logo IFSC">
    </a>
  </div>

  <!-- =========================
       MENU DE NAVEGAÇÃO PRINCIPAL
       Links com destaque para a página atual
       ========================= -->
  <nav class="main-nav" aria-label="Menu principal do site">
    <ul id="nav-list">
      <li>
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
          Home
        </a>
      </li>
      <li>
        <a href="relatorios.php" class="<?= basename($_SERVER['PHP_SELF']) == 'relatorios.php' ? 'active' : '' ?>">
          Relatórios
        </a>
      </li>
      <li>
        <a href="dashboard_mabel.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_mabel.php' ? 'active' : '' ?>">
          Mabel
        </a>
      </li>
      <li>
        <a href="dashboard_ptqa.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_ptqa.php' ? 'active' : '' ?>">
          PTQA
        </a>
      </li>
      <li>
        <a href="sobre.php" class="<?= basename($_SERVER['PHP_SELF']) == 'sobre.php' ? 'active' : '' ?>">
          Sobre
        </a>
      </li>
    </ul>
  </nav>

</header>
