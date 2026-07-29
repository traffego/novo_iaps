<?php
/**
 * Transparência: Estatuto — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Estatuto Social';
$page_description = 'Estatuto Social do Instituto Atleta Para Sempre — documento que rege a organização.';
$tem_pdf          = file_exists(UPLOAD_PATH . '/docs/estatuto.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span>›</span><a href="/transparencia/declaracao">Transparência</a><span>›</span><span>Estatuto</span>
        </nav>
        <h1 class="page-hero-title">Estatuto Social</h1>
    </div>
</section>
<section class="section" id="estatuto">
    <div class="container container-narrow">
        <div class="transparency-card fade-in-up">
            <div class="transparency-icon">📜</div>
            <h2>Estatuto Social</h2>
            <p>O Estatuto Social é o documento que rege o funcionamento do Instituto Atleta Para Sempre, definindo sua natureza jurídica, finalidade, estrutura organizacional, direitos e deveres de seus membros e as normas de funcionamento.</p>
            <p>O Instituto é constituído como entidade sem fins lucrativos, de caráter educativo, cultural e desportivo, atuando em conformidade com a Lei de Incentivo ao Esporte (Lei nº 11.438/2006).</p>
            <?php if ($tem_pdf): ?>
            <div style="margin-top:2rem">
                <a href="/uploads/docs/estatuto.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Estatuto Social (PDF)
                </a>
            </div>
            <?php else: ?>
            <p class="text-muted" style="margin-top:1rem"><em>Documento em processo de digitalização. Disponível em breve.</em></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
