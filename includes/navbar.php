<?php
// Se a variável não for definida na página, o padrão será vazio
$menuAtivo = $menuAtivo ?? '';
?>

<!-- =========================================
     TOPO MOBILE
     ========================================= -->
<div class="mobile-top-bar d-flex d-md-none">
    <h1 class="m-0 fw-bold text-primary fs-4">Bandesivos</h1>
    <div class="d-flex align-items-center gap-2">
        <span id="badgeAdminMobile" class="badge bg-danger d-none">ADMIN</span>
        <small class="fw-bold text-dark">👤 <span id="nomeLogadoMobile"></span></small>
    </div>
</div>

<!-- =========================================
     MENU INFERIOR MOBILE
     ========================================= -->
<div class="mobile-bottom-nav d-flex d-md-none">
    <a href="index.php" class="nav-item-mobile <?= ($menuAtivo == 'mapa') ? 'active' : '' ?>">
        <i class="bi bi-map-fill fs-4 mb-1"></i> <span>Mapa</span>
    </a>
    <a href="feed.php" class="nav-item-mobile <?= ($menuAtivo == 'radar') ? 'active' : '' ?>">
        <i class="bi bi-broadcast fs-4 mb-1"></i> <span>Radar</span>
    </a>
    <a href="#" onclick="abrirMissao()" class="nav-item-mobile <?= ($menuAtivo == 'missao') ? 'active' : '' ?>">
        <i class="bi bi-bullseye fs-4 mb-1"></i> <span>Missão</span>
    </a>
    <a href="album.php" class="nav-item-mobile <?= ($menuAtivo == 'album') ? 'active' : '' ?>">
        <i class="bi bi-journal-album fs-4 mb-1"></i> <span>Álbum</span>
    </a>
    <div class="nav-item-mobile dropdown <?= ($menuAtivo == 'mais') ? 'active' : '' ?>">
        <a href="#" data-bs-toggle="dropdown" class="text-decoration-none text-secondary d-flex flex-column align-items-center">
            <i class="bi bi-grid-fill fs-4 mb-1"></i> <span>Mais</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="margin-bottom: 15px;">
            <li><a class="dropdown-item fw-bold text-warning bg-light" href="ranking.php"><i class="bi bi-trophy-fill me-2"></i> Hall da Fama</a></li>
            <li><a class="dropdown-item fw-bold text-danger d-none" href="admin.php" id="btnMenuAdminMobile"><i class="bi bi-shield-lock-fill me-2"></i> Admin</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item fw-bold text-danger" href="#" onclick="sairDoApp()"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
        </ul>
    </div>
</div>

<!-- =========================================
     MENU SUPERIOR PC (Flutuante)
     ========================================= -->
<div class="d-none d-md-flex align-items-center"
    style="position: fixed; top: 15px; right: 15px; z-index: 1050; background: white; padding: 8px 15px; border-radius: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
    
    <small class="fw-bold text-dark me-2 border-end pe-3">
        👤 <span id="nomeLogado"></span> <span id="badgeAdmin" class="badge bg-danger ms-1 d-none">ADMIN</span>
    </small>
    
    <a href="admin.php" id="btnMenuAdmin" class="btn btn-sm btn-outline-danger ms-1 fw-bold d-none rounded-pill px-3">🛡️ Admin</a>
    
    <!-- BOTÃO DO MAPA ADICIONADO AQUI 👇 -->
    <a href="index.php" class="btn btn-sm btn-dark ms-2 fw-bold text-white rounded-pill px-3"><i class="bi bi-map-fill me-1"></i> Mapa</a>
    
    <button onclick="abrirMissao()" class="btn btn-sm btn-success ms-2 fw-bold text-white rounded-pill px-3"><i class="bi bi-bullseye me-1"></i> Missão</button>
    <a href="feed.php" class="btn btn-sm btn-info ms-2 fw-bold text-white rounded-pill px-3"><i class="bi bi-broadcast me-1"></i> Radar</a>
    <a href="album.php" class="btn btn-sm btn-primary ms-2 fw-bold rounded-pill px-3"><i class="bi bi-journal-album me-1"></i> Álbum</a>
    <a href="ranking.php" class="btn btn-sm btn-warning text-dark ms-2 fw-bold rounded-pill px-3"><i class="bi bi-trophy-fill me-1"></i> Ranking</a>
    <button onclick="sairDoApp()" class="btn btn-sm text-danger ms-2 fw-bold"><i class="bi bi-box-arrow-right fs-5"></i></button>
</div>

<?php 
include './includes/modal_missao.php'; 
?>