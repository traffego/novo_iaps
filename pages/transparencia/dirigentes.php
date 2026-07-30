<?php
/**
 * Transparência: Dirigentes — Instituto Atleta Para Sempre
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';

$page_title       = 'Dirigentes';
$page_description = 'Identificação dos dirigentes e representantes legais do Instituto Atleta Para Sempre.';

$dirigentes  = db_fetch_all('SELECT * FROM tab_dirigente WHERE cod_org = 10001 ORDER BY posicao, id');
$tem_pdf     = file_exists(UPLOAD_PATH . '/docs/dirigentes.pdf');

ob_start();
?>
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Navegação">
            <a href="/">Início</a>
            <span aria-hidden="true">›</span>
            <a href="/transparencia/declaracao">Transparência</a>
            <span aria-hidden="true">›</span>
            <span>Dirigentes</span>
        </nav>
        <h1 class="page-hero-title">Corpo Diretivo & Dirigentes</h1>
        <p class="page-hero-sub">Identificação e contatos dos responsáveis pela gestão executiva e fiscal do Instituto.</p>
    </div>
</section>

<!-- SUB-NAVEGAÇÃO DA TRANSPARÊNCIA -->
<div class="transparencia-subnav-wrapper">
    <div class="container">
        <div class="transparencia-subnav">
            <a href="/transparencia/declaracao" class="t-subnav-link">Declaração</a>
            <a href="/transparencia/dirigentes" class="t-subnav-link active">Dirigentes</a>
            <a href="/transparencia/estatuto" class="t-subnav-link">Estatuto</a>
            <a href="/transparencia/financeiro" class="t-subnav-link">Financeiro</a>
            <a href="/transparencia/regulamento" class="t-subnav-link">Regulamento</a>
            <a href="/transparencia/termos" class="t-subnav-link">Termos</a>
            <a href="/transparencia/painel" class="t-subnav-link">Painel de Transferências</a>
        </div>
    </div>
</div>

<section class="section" id="dirigentes">
    <div class="container container-narrow">
        <?php if ($tem_pdf): ?>
        <div class="download-card fade-in-up mb-8">
            <div class="download-info">
                <span class="download-icon">📄</span>
                <div>
                    <strong>Relação Oficial de Dirigentes</strong>
                    <p>Documento oficial assinado com a relação de todos os dirigentes e mandatos vigentes.</p>
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
                    <?php if ($d['telefone']): ?>
                        <p class="director-meta">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <?= e($d['telefone']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($d['e_mail']): ?>
                        <p class="director-meta">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:<?= e($d['e_mail']) ?>"><?= e($d['e_mail']) ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif (!$tem_pdf): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">👥</div>
            <h3>Corpo diretivo em atualização</h3>
            <p>A listagem dos membros da diretoria será publicada em breve.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
