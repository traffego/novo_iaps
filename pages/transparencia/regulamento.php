<?php
/**
 * Transparência: Regulamento de Compras — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Regulamento de Compras';
$page_description = 'Regulamento próprio de compras, cotações e contratações do Instituto Atleta Para Sempre.';
$tem_pdf          = file_exists(UPLOAD_PATH . '/docs/regulamento_compras.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Regulamento de Compras</span>
        </nav>
        <h1 class="page-hero-title">Regulamento de Compras & Contratações</h1>
        <p class="page-hero-sub">Regras e procedimentos objetivados para aquisição de bens, materiais e contratação de serviços.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link active">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="regulamento">
    <div class="container">
        <!-- HEADER METRICS -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">🛍️</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Cotação Prévia</span>
                    <span class="s-lbl">Pesquisa Mercadológica</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">⚖️</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Impessoalidade</span>
                    <span class="s-lbl">Julgamento Objetivo</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📢</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Publicidade</span>
                    <span class="s-lbl">Editais Abertos</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">✅</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.15rem;">Aprovado</span>
                    <span class="s-lbl">Regulamento Próprio</span>
                </div>
            </div>
        </div>

        <!-- MAIN REGULAMENTO BANNER -->
        <div class="transp-banner-card fade-in-up mb-8">
            <div class="t-banner-icon">📋</div>
            <div class="t-banner-content" style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:1.5rem;">
                <div style="max-width:700px;">
                    <span class="section-tag">Norma de Contratação</span>
                    <h2>Regulamento Próprio de Compras</h2>
                    <p>O <strong>Regulamento de Compras e Contratações</strong> estabelece as diretrizes formais para aquisições de insumos desportivos, contratação de profissionais e locações, em obediência aos princípios da legalidade, moralidade, publicidade e melhor custo-benefício para o interesse público.</p>
                </div>
                <?php if ($tem_pdf): ?>
                <a href="/uploads/docs/regulamento_compras.pdf" class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Regulamento (PDF)
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- PROCEDURES GRID -->
        <div class="painel-grid fade-in-up">
            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">🔍</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">ETAPA 1</span>
                    </div>
                    <h3 class="painel-card-title">Cotação de Preços</h3>
                    <p class="painel-card-desc">Realização obrigatória de cotação com no mínimo 3 fornecedores cadastrados para cada aquisição.</p>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">⚖️</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img">ETAPA 2</span>
                    </div>
                    <h3 class="painel-card-title">Julgamento & Seleção</h3>
                    <p class="painel-card-desc">Critério do menor preço ou melhor técnica, com parecer de homologação registrado.</p>
                </div>
            </div>

            <div class="painel-card">
                <div class="painel-card-doc-icon">
                    <div class="doc-icon-box">📄</div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-doc">ETAPA 3</span>
                    </div>
                    <h3 class="painel-card-title">Formalização & Contrato</h3>
                    <p class="painel-card-desc">Emissão de ordem de fornecimento, contrato formal e publicação nos canais de transparência.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
