<?php
/**
 * Transparência: Dirigentes — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Dirigentes';
$page_description = 'Identificação dos dirigentes e responsáveis do Instituto Atleta Para Sempre.';

$dirigentes  = db_fetch_all('SELECT * FROM tab_dirigente WHERE cod_org = 10001 ORDER BY posicao, id');
$tem_pdf     = file_exists(UPLOAD_PATH . '/docs/dirigentes.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a><span>›</span><a href="/transparencia/declaracao">Transparência</a><span>›</span><span>Dirigentes</span>
        </nav>
        <h1 class="page-hero-title">Dirigentes</h1>
        <p class="page-hero-sub">Conheça os responsáveis pela gestão do Instituto.</p>
    </div>
</section>

<section class="section" id="dirigentes">
    <div class="container container-narrow">
        <?php if ($tem_pdf): ?>
        <div class="download-card fade-in-up">
            <div class="download-info">
                <span class="download-icon">📄</span>
                <div>
                    <strong>Identificação de Dirigentes</strong>
                    <p>Documento oficial com a relação de dirigentes e representantes legais.</p>
                </div>
            </div>
            <a href="/uploads/docs/dirigentes.pdf" class="btn btn-primary" target="_blank" rel="noopener">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Baixar PDF
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($dirigentes)): ?>
        <div class="directors-grid fade-in-up">
            <?php foreach ($dirigentes as $d): ?>
            <div class="director-card">
                <div class="director-avatar">
                    <?= strtoupper(substr($d['nome_diretor'], 0, 1)) ?>
                </div>
                <div class="director-info">
                    <h3><?= e($d['nome_diretor']) ?></h3>
                    <span class="director-cargo"><?= e($d['cargo_diretor']) ?></span>
                    <?php if ($d['telefone']): ?><p><?= e($d['telefone']) ?></p><?php endif; ?>
                    <?php if ($d['e_mail']): ?><p><a href="mailto:<?= e($d['e_mail']) ?>"><?= e($d['e_mail']) ?></a></p><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif (!$tem_pdf): ?>
        <div class="empty-state"><p>Informações disponíveis em breve.</p></div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
