<?php
// templates/header.php
if (!defined('ROOT_PATH')) exit;
?>
<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <a href="/" class="navbar-brand">
                <img src="https://institutoatletaparasempre.org/imgs/logo_site.png" alt="Instituto Atleta Para Sempre" style="height: 40px;">
            </a>

            <div class="navbar-collapse" id="navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="/" class="nav-link">Início</a></li>
                    <li class="nav-item"><a href="/quem-somos" class="nav-link">Quem Somos</a></li>
                    <li class="nav-item"><a href="/projetos" class="nav-link">Projetos</a></li>
                    <li class="nav-item"><a href="/noticias" class="nav-link">Notícias</a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" id="navbarDropdown">
                            Transparência
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </a>
                        <div class="dropdown-menu">
                            <a href="/transparencia/declaracao" class="dropdown-item">Declaração</a>
                            <a href="/transparencia/dirigentes" class="dropdown-item">Dirigentes</a>
                            <a href="/transparencia/estatuto" class="dropdown-item">Estatuto</a>
                            <a href="/transparencia/financeiro" class="dropdown-item">Financeiro</a>
                            <a href="/transparencia/regulamento" class="dropdown-item">Regulamento</a>
                            <a href="/transparencia/termos" class="dropdown-item">Termos</a>
                            <a href="/transparencia/painel" class="dropdown-item">Painel de Transferências</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="/trabalhe-conosco" class="nav-link">Trabalhe Conosco</a></li>
                    <li class="nav-item"><a href="/contato" class="nav-link">Contato</a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <button id="mobile-menu-toggle" class="navbar-toggler" aria-label="Menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
            </div>
        </nav>
    </div>
</header>
