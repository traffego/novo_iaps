<?php
/**
 * Transparência: Financeiro — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Documentos Financeiros';
$page_description = 'Documentos financeiros, balanços contábeis e prestações de contas do Instituto Atleta Para Sempre.';

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro ORDER BY data_documento DESC');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Documentos Financeiros</span>
        </nav>
        <h1 class="page-hero-title">Documentos Financeiros & Balanços</h1>
        <p class="page-hero-sub">Demostrativos contábeis, balanços patrimoniais e prestações de contas oficiais.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link active">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="financeiro">
    <div class="container container-narrow">
        <?php if (empty($docs)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">💰</div>
            <h3>Nenhum documento financeiro publicado</h3>
            <p>Os demonstrativos contábeis e relatórios financeiros são validados e publicados periodicamente.</p>
        </div>
        <?php else: ?>
        <div class="docs-list fade-in-up">
            <?php foreach ($docs as $doc): ?>
            <div class="doc-list-item">
                <div class="doc-list-info">
                    <h4><?= e($doc['titulo']) ?></h4>
                    <?php if ($doc['resumo']): ?><p><?= e($doc['resumo']) ?></p><?php endif; ?>
                    <span class="doc-date"><?= format_date($doc['data_documento']) ?></span>
                </div>
                <a href="/uploads/transparencia/<?= e($doc['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Documento
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
