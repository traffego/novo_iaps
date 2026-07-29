<?php
/**
 * Transparência: Financeiro — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Documentos Financeiros';
$page_description = 'Documentos financeiros e relatórios contábeis do Instituto Atleta Para Sempre.';

$docs = db_fetch_all('SELECT * FROM tab_documentos_financeiro ORDER BY data_documento DESC');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span>›</span><a href="/transparencia/declaracao">Transparência</a><span>›</span><span>Financeiro</span>
        </nav>
        <h1 class="page-hero-title">Documentos Financeiros</h1>
        <p class="page-hero-sub">Prestações de contas e relatórios financeiros.</p>
    </div>
</section>
<section class="section" id="financeiro">
    <div class="container container-narrow">
        <?php if (empty($docs)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">💰</div>
            <h3>Nenhum documento disponível</h3>
            <p>Os documentos financeiros serão publicados conforme as prestações de contas forem aprovadas.</p>
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
                    Baixar
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
