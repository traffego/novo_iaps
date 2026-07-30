<?php
/**
 * Transparência: Estatuto — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Estatuto Social';
$page_description = 'Estatuto Social do Instituto Atleta Para Sempre — documento fundamental que rege a organização.';
$tem_pdf          = file_exists(UPLOAD_PATH . '/docs/estatuto.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Estatuto Social</span>
        </nav>
        <h1 class="page-hero-title">Estatuto Social</h1>
        <p class="page-hero-sub">Documento constituinte que rege a estrutura jurídica, finalidades e diretrizes institucionais.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link active">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="estatuto">
    <div class="container">
        <!-- HEADER METRICS BANNER -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">🏛️</div>
                <div class="s-info">
                    <span class="s-num">2012</span>
                    <span class="s-lbl">Ano de Registro</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📍</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Recife-PE</span>
                    <span class="s-lbl">Sede Jurídica</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📜</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">OSC / 3º Setor</span>
                    <span class="s-lbl">Natureza Jurídica</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">⚖️</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Consolidado</span>
                    <span class="s-lbl">Estatuto Vigente</span>
                </div>
            </div>
        </div>

        <!-- MAIN ESTATUTO CARD BANNER -->
        <div class="transp-banner-card fade-in-up mb-8">
            <div class="t-banner-icon">📜</div>
            <div class="t-banner-content" style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:1.5rem;">
                <div style="max-width:700px;">
                    <span class="section-tag">Norma Fundamental</span>
                    <h2>Estatuto Social Consolidado</h2>
                    <p>O <strong>Estatuto Social</strong> é o instrumento supremo que define as normas de funcionamento do Instituto Atleta Para Sempre, estabelecendo sua finalidade de interesse público, governança, direitos e deveres dos associados e diretrizes de prestação de contas.</p>
                </div>
                <?php if ($tem_pdf): ?>
                <a href="/uploads/docs/estatuto.pdf" class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Estatuto (PDF)
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ESTATUTO PILLARS GRID -->
        <div class="painel-grid fade-in-up">
            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">🎯</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">CAPÍTULO I</span>
                    </div>
                    <h3 class="painel-card-title">Denominação & Sede</h3>
                    <p class="painel-card-desc">Associação civil sem fins lucrativos, com autonomia administrativa, financeira e sede em Recife-PE.</p>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">🏆</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img">CAPÍTULO II</span>
                    </div>
                    <h3 class="painel-card-title">Objetivos Sociais</h3>
                    <p class="painel-card-desc">Fomento do esporte, lazer, formação cidadã e ações de Assistência Social no contraturno escolar.</p>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">👔</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">CAPÍTULO III</span>
                    </div>
                    <h3 class="painel-card-title">Governança & Administração</h3>
                    <p class="painel-card-desc">Estrutura organizacional composta pela Assembleia Geral, Diretoria Executiva e Conselho Fiscal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
