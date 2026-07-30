<?php
/**
 * Transparência: Termos de Colaboração — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Termos de Colaboração';
$page_description = 'Termos de fomento e parcerias públicas e privadas celebradas pelo Instituto Atleta Para Sempre.';

$termos = db_fetch_all('SELECT * FROM tab_documentos_termo_colaboracao ORDER BY data_documento DESC');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Termos de Colaboração</span>
        </nav>
        <h1 class="page-hero-title">Termos de Fomento & Colaboração</h1>
        <p class="page-hero-sub">Parcerias públicas, acordos institucionais e termos de fomento celebrados.</p>
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
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link active">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="termos">
    <div class="container container-narrow">
        <?php if (empty($termos)): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">🤝</div>
            <h3>Nenhum termo publicado</h3>
            <p>Os termos de colaboração e fomento celebrados serão listados publicamente nesta seção.</p>
        </div>
        <?php else: ?>
        <div class="docs-list fade-in-up">
            <?php foreach ($termos as $t): ?>
            <div class="doc-list-item">
                <div class="doc-list-info">
                    <h4><?= e($t['titulo']) ?></h4>
                    <?php if ($t['resumo']): ?><p><?= e($t['resumo']) ?></p><?php endif; ?>
                    <span class="doc-date"><?= format_date($t['data_documento']) ?></span>
                </div>
                <a href="/uploads/transparencia/<?= e($t['arquivo']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Termo
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
