<?php
// templates/header.php
if (!defined('ROOT_PATH')) exit;

$total_noticias = 0;
try {
    if (function_exists('db_count')) {
        $total_noticias = db_count('SELECT COUNT(*) FROM tab_noticias');
    }
} catch (Throwable $e) {
    $total_noticias = 0;
}
?>
<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="/" class="logo-inverted-container" title="Instituto Atleta Para Sempre">
                <img src="https://institutoatletaparasempre.org/imgs/logo_site.png" alt="Instituto Atleta Para Sempre" class="header-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="logo-fallback" style="display:none;">
                    <div class="logo-icon-pill">A</div>
                    <span class="logo-text-inverted">Atleta Para Sempre</span>
                </div>
            </a>

            <div class="navbar-collapse" id="navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="/quem-somos" class="nav-link"><i data-lucide="info"></i> Quem Somos</a></li>
                    <li class="nav-item"><a href="/projetos" class="nav-link"><i data-lucide="trophy"></i> Projetos</a></li>
                    
                    <?php if ($total_noticias > 0): ?>
                    <li class="nav-item"><a href="/noticias" class="nav-link"><i data-lucide="newspaper"></i> Notícias</a></li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" id="navbarDropdown">
                            <i data-lucide="shield-check"></i> Transparência
                            <i data-lucide="chevron-down" style="width:14px; height:14px; margin-left:2px;"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="/transparencia/declaracao" class="dropdown-item"><i data-lucide="file-text"></i> Declaração</a>
                            <a href="/transparencia/dirigentes" class="dropdown-item"><i data-lucide="users"></i> Dirigentes</a>
                            <a href="/transparencia/estatuto" class="dropdown-item"><i data-lucide="file-check"></i> Estatuto</a>
                            <a href="/transparencia/financeiro" class="dropdown-item"><i data-lucide="dollar-sign"></i> Financeiro</a>
                            <a href="/transparencia/regulamento" class="dropdown-item"><i data-lucide="book-open"></i> Regulamento</a>
                            <a href="/transparencia/termos" class="dropdown-item"><i data-lucide="file-signature"></i> Termos</a>
                            <a href="/transparencia/painel" class="dropdown-item"><i data-lucide="layout-dashboard"></i> Painel de Transferências</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="/trabalhe-conosco" class="nav-link"><i data-lucide="briefcase"></i> Trabalhe Conosco</a></li>
                    <li class="nav-item"><a href="/fornecedores" class="nav-link"><i data-lucide="building-2"></i> Fornecedores</a></li>
                    <li class="nav-item"><a href="/contato" class="nav-link"><i data-lucide="mail"></i> Contato</a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <button id="theme-toggle" class="theme-toggle btn-icon" aria-label="Alternar tema">
                    <i data-lucide="sun"></i>
                </button>
                <button id="mobile-menu-toggle" class="navbar-toggler btn-icon" aria-label="Menu">
                    <i data-lucide="menu"></i>
                </button>
            </div>
        </nav>
    </div>
</header>
