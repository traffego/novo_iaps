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
$total_dir   = count($dirigentes);

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
        <h1 class="page-hero-title">Corpo Diretivo & Gestão</h1>
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
    <div class="container">
        <!-- HEADER METRICS -->
        <div class="painel-summary-grid fade-in-up">
            <div class="summary-card">
                <div class="s-icon">👥</div>
                <div class="s-info">
                    <span class="s-num"><?= $total_dir > 0 ? $total_dir : '0' ?></span>
                    <span class="s-lbl">Dirigentes Cadastrados</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">🏆</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Ricardo Rocha</span>
                    <span class="s-lbl">Patrono Institucional</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">📋</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Sem Remuneração</span>
                    <span class="s-lbl">Atuação Voluntária MROSC</span>
                </div>
            </div>
            <div class="summary-card">
                <div class="s-icon">✅</div>
                <div class="s-info">
                    <span class="s-num" style="font-size:1.1rem; line-height:1.2;">Vigente</span>
                    <span class="s-lbl">Status do Mandato</span>
                </div>
            </div>
        </div>

        <?php if ($tem_pdf): ?>
        <div class="transp-banner-card fade-in-up mb-8" style="background:var(--bg-surface); border:1px solid var(--border-color);">
            <div class="t-banner-icon">📄</div>
            <div class="t-banner-content" style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:1rem;">
                <div>
                    <h3 style="margin:0; font-size:1.15rem;">Ata & Relação Oficial de Dirigentes (PDF)</h3>
                    <p style="margin:0.25rem 0 0 0; color:var(--text-muted); font-size:0.875rem;">Documento registrado contendo a ata de eleição e identificação dos representantes legais.</p>
                </div>
                <a href="/uploads/docs/dirigentes.pdf" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Relação Oficial
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($dirigentes)): ?>
        <div class="painel-grid fade-in-up">
            <?php foreach ($dirigentes as $d): ?>
            <div class="painel-card">
                <div class="painel-card-doc-icon" style="height:120px; background:var(--color-primary-alpha);">
                    <div class="doc-icon-box" style="font-weight:800; font-size:1.5rem; color:var(--color-primary);">
                        <?= strtoupper(substr($d['nome_diretor'], 0, 1)) ?>
                    </div>
                </div>
                <div class="painel-card-body">
                    <div class="painel-card-meta">
                        <span class="p-badge badge-img"><?= e($d['cargo_diretor'] ?: 'DIRIGENTE') ?></span>
                    </div>
                    <h3 class="painel-card-title"><?= e($d['nome_diretor']) ?></h3>
                    <p class="painel-card-desc" style="margin-bottom:0.75rem;">
                        <strong>Cargo:</strong> <?= e($d['cargo_diretor']) ?>
                    </p>
                    
                    <div style="font-size:0.85rem; color:var(--text-muted); display:flex; flex-direction:column; gap:0.35rem; margin-top:auto; padding-top:0.75rem; border-top:1px dashed var(--border-color);">
                        <?php if ($d['telefone']): ?>
                            <span style="display:flex; align-items:center; gap:0.4rem;">
                                📞 <?= e($d['telefone']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($d['e_mail']): ?>
                            <span style="display:flex; align-items:center; gap:0.4rem; word-break:break-all;">
                                ✉️ <a href="mailto:<?= e($d['e_mail']) ?>" style="color:var(--color-primary);"><?= e($d['e_mail']) ?></a>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif (!$tem_pdf): ?>
        <div class="empty-state fade-in-up">
            <div class="empty-icon">👥</div>
            <h3>Corpo diretivo em atualização</h3>
            <p>A relação completa dos membros da diretoria será disponibilizada nesta página em breve.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
