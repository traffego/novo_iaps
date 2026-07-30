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
        <p class="page-hero-sub">Regras e procedimentos para aquisição de bens, materiais e contratação de serviços.</p>
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
    <div class="container container-narrow">
        <div class="transparency-card fade-in-up">
            <div class="transparency-icon">📋</div>
            <h2 class="section-title mb-4">Regulamento Próprio de Contratações</h2>
            <p>O <strong>Regulamento de Compras e Contratações</strong> do Instituto Atleta Para Sempre estabelece as normas e procedimentos objetivos para a aquisição de bens, materiais esportivos e serviços de terceiros.</p>
            <p>Em obediência aos princípios constitucionais da <strong>legalidade, impessoalidade, moralidade, publicidade e eficiência</strong>, o Instituto garante a pesquisa mercadológica, igualdade de condições entre fornecedores e seleção da proposta mais vantajosa para o interesse público.</p>

            <?php if ($tem_pdf): ?>
            <div style="margin-top:2rem">
                <a href="/uploads/docs/regulamento_compras.pdf" class="btn btn-primary btn-lg" target="_blank" rel="noopener">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Regulamento (PDF)
                </a>
            </div>
            <?php else: ?>
            <div class="doc-notice mt-6">
                <span>ℹ️ Regulamento consolidado em fase de publicação para download.</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
