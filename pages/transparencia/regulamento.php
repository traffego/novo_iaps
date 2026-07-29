<?php
/**
 * Transparência: Regulamento de Compras — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title = 'Regulamento de Compras';
$page_description = 'Regulamento de compras e contratações do Instituto Atleta Para Sempre.';
$tem_pdf    = file_exists(UPLOAD_PATH . '/docs/regulamento_compras.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span>›</span><a href="/transparencia/declaracao">Transparência</a><span>›</span><span>Regulamento</span>
        </nav>
        <h1 class="page-hero-title">Regulamento de Compras</h1>
    </div>
</section>
<section class="section" id="regulamento">
    <div class="container container-narrow">
        <div class="transparency-card fade-in-up">
            <div class="transparency-icon">📋</div>
            <h2>Regulamento de Compras e Contratações</h2>
            <p>O Regulamento de Compras e Contratações do Instituto Atleta Para Sempre estabelece as normas e procedimentos para aquisição de bens e contratação de serviços, em conformidade com os princípios da legalidade, impessoalidade, moralidade, publicidade e eficiência.</p>
            <p>Todas as aquisições são realizadas mediante processos competitivos, com cotação prévia de preços e publicação de editais, garantindo a transparência e o melhor uso dos recursos disponíveis.</p>
            <?php if ($tem_pdf): ?>
            <div style="margin-top:2rem">
                <a href="/uploads/docs/regulamento_compras.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Regulamento (PDF)
                </a>
            </div>
            <?php else: ?>
            <p class="text-muted" style="margin-top:1rem"><em>Documento em processo de atualização.</em></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
